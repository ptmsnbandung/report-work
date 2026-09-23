<?php

namespace App\Services;

use App\Models\Kronologis;
use App\Models\Tiket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class KronologisService
{
    protected ?NotificationService $notificationService;

    public function __construct(
        ?NotificationService $notificationService = null
    ) {
        $this->notificationService = $notificationService 
            ?? (function_exists('app') && app()->bound(NotificationService::class) ? app(NotificationService::class) : null);
    }

    /**
     * Kategori yang valid
     */
    public const KATEGORI_LIST = [
        'IZIN'     => 'Izin Masuk Ruangan / Lokasi',
        'OTDR'     => 'Hasil Pengukuran OTDR',
        'TRACING'  => 'Tracing Jalur Kabel',
        'MATERIAL' => 'Material Menuju / Sampai Lokasi',
        'JOINTING' => 'Proses Jointing / Penyambungan',
        'LINK_UP'  => 'Link UP / Normalisasi Sinyal',
        'SELESAI'  => 'Pekerjaan Lapangan Selesai',
        'LAIN'     => 'Update Lapangan Lainnya',
    ];

    /**
     * Tambah catatan kronologis baru untuk tiket
     */
    public function addKronologis(Tiket $tiket, array $data, User $user, ?UploadedFile $foto = null): Kronologis
    {
        return DB::transaction(function () use ($tiket, $data, $user, $foto) {
            $timestamp = !empty($data['timestamp'])
                ? Carbon::parse($data['timestamp'])
                : Carbon::now();

            $fotoUrl = null;
            if ($foto && $foto->isValid()) {
                $fotoUrl = $this->uploadFoto($foto, $tiket->id);
            }

            $kronologis = Kronologis::create([
                'id_tiket'  => $tiket->id,
                'timestamp' => $timestamp,
                'user_id'   => $user->id,
                'kategori'  => $data['kategori'],
                'informasi' => $data['informasi'],
                'foto_url'  => $fotoUrl,
                'latitude'  => !empty($data['latitude']) ? (float) $data['latitude'] : null,
                'longitude' => !empty($data['longitude']) ? (float) $data['longitude'] : null,
            ]);

            // Jika status tiket masih OPEN dan teknis mulai bekerja, ubah otomatis ke PROSES
            if ($tiket->status === 'OPEN') {
                $tiket->update(['status' => 'PROSES']);
            }

            $loaded = $kronologis->load('user');

            if ($this->notificationService) {
                try {
                    $this->notificationService->notifyKronologisBaru($tiket, $loaded);
                } catch (\Throwable $e) {
                    // Fail silently
                }
            }

            return $loaded;
        });
    }

    /**
     * Upload dan simpan foto kronologis
     */
    protected function uploadFoto(UploadedFile $file, int|string $tiketId): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = 'krono_' . $tiketId . '_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;

        // Simpan ke public/uploads/kronologis
        $destinationPath = public_path('uploads/kronologis');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);
        $fullPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;

        // Auto compress/resize on server if file is large (> 1MB or > 1920px)
        $this->compressImageOnServer($fullPath);

        return 'uploads/kronologis/' . $filename;
    }

    /**
     * Kompresi dan resize gambar otomatis di sisi server jika ukuran > 1MB atau dimensi > 1920px
     */
    protected function compressImageOnServer(string $filepath): void
    {
        if (!file_exists($filepath) || !extension_loaded('gd')) {
            return;
        }

        $filesize = filesize($filepath);
        $imageInfo = @getimagesize($filepath);
        if (!$imageInfo) {
            return;
        }

        [$width, $height, $type] = $imageInfo;

        // Jika ukuran file < 1MB dan dimensi wajar, tidak perlu re-compress
        if ($filesize < 1048576 && $width <= 1920 && $height <= 1920) {
            return;
        }

        $maxDim = 1920;
        $newWidth = $width;
        $newHeight = $height;

        if ($width > $maxDim || $height > $maxDim) {
            if ($width > $height) {
                $newHeight = (int) round(($height * $maxDim) / $width);
                $newWidth = $maxDim;
            } else {
                $newWidth = (int) round(($width * $maxDim) / $height);
                $newHeight = $maxDim;
            }
        }

        $srcImg = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($filepath),
            IMAGETYPE_PNG  => @imagecreatefrompng($filepath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($filepath),
            default        => null,
        };

        if (!$srcImg) {
            return;
        }

        $dstImg = imagecreatetruecolor($newWidth, $newHeight);

        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagealphablending($dstImg, false);
            imagesavealpha($dstImg, true);
        }

        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($dstImg, $filepath, 80),
            IMAGETYPE_PNG  => imagepng($dstImg, $filepath, 7),
            IMAGETYPE_WEBP => imagewebp($dstImg, $filepath, 80),
            default        => null,
        };

        imagedestroy($srcImg);
        imagedestroy($dstImg);
    }

    /**
     * Hapus catatan kronologis (Admin Only)
     */
    public function deleteKronologis(Kronologis $kronologis, User $user): bool
    {
        if (!$user->hasRole('admin')) {
            throw new InvalidArgumentException('Hanya Administrator yang berhak menghapus catatan kronologis.');
        }

        // Hapus file foto jika ada di public_path
        if ($kronologis->foto_url && file_exists(public_path($kronologis->foto_url))) {
            @unlink(public_path($kronologis->foto_url));
        }

        return (bool) $kronologis->delete();
    }

    /**
     * Dapatkan semua kronologis tiket berurutan
     */
    public function getTimeline(Tiket $tiket): Collection
    {
        return $tiket->kronologis()->with('user')->orderBy('timestamp', 'asc')->get();
    }
}

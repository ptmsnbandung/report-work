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
            $videoUrl = null;

            if ($foto && $foto->isValid()) {
                $ext = strtolower($foto->getClientOriginalExtension() ?: '');
                $isVideo = in_array($ext, ['mp4', 'webm', 'mov', 'm4v', '3gp', 'avi'])
                    || str_starts_with((string)$foto->getMimeType(), 'video/');

                if ($isVideo) {
                    $videoUrl = $this->uploadMediaFile($foto, $tiket->id, 'video');
                } else {
                    $fotoUrl = $this->uploadMediaFile($foto, $tiket->id, 'foto');
                }
            }

            $kronologis = Kronologis::create([
                'id_tiket'  => $tiket->id,
                'timestamp' => $timestamp,
                'user_id'   => $user->id,
                'kategori'  => $data['kategori'],
                'informasi' => $data['informasi'],
                'foto_url'  => $fotoUrl,
                'video_url' => $videoUrl,
                'latitude'  => !empty($data['latitude']) ? (float) $data['latitude'] : null,
                'longitude' => !empty($data['longitude']) ? (float) $data['longitude'] : null,
            ]);

            // Catat first response teknisi & ubah status ke PROSES jika masih OPEN
            if ($tiket->first_response_at === null && $user->hasRole('teknis')) {
                $firstResponseAt = Carbon::now();
                $responseTime = max(0, (int) $tiket->tanggal_open->diffInMinutes($firstResponseAt));
                $tiket->update([
                    'first_response_at' => $firstResponseAt,
                    'response_time_minutes' => $responseTime,
                    'status' => $tiket->status === 'OPEN' ? 'PROSES' : $tiket->status,
                ]);
            } elseif ($tiket->status === 'OPEN') {
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
     * Upload dan simpan media (foto atau video) kronologis
     */
    public function uploadMediaFile(UploadedFile $file, int|string $tiketId, string $type = 'foto'): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: ($type === 'video' ? 'mp4' : 'jpg'));
        $prefix = $type === 'video' ? 'krono_vid_' : 'krono_';
        $filename = $prefix . $tiketId . '_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;

        $subDir = $type === 'video' ? 'uploads/kronologis/videos' : 'uploads/kronologis';
        $destinationPath = public_path($subDir);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);
        $fullPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;

        if ($type === 'foto') {
            $this->compressImageOnServer($fullPath);
        }

        return $subDir . '/' . $filename;
    }

    /**
     * Upload dan simpan foto kronologis (backward compatibility)
     */
    protected function uploadFoto(UploadedFile $file, int|string $tiketId): string
    {
        return $this->uploadMediaFile($file, $tiketId, 'foto');
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
        // Hapus file video jika ada di public_path
        if ($kronologis->video_url && file_exists(public_path($kronologis->video_url))) {
            @unlink(public_path($kronologis->video_url));
        }

        return (bool) $kronologis->delete();
    }

    /**
     * Dapatkan kronologis tiket berurutan dengan opsi cursor pagination
     */
    public function getTimeline(Tiket $tiket, ?int $beforeId = null, ?int $afterId = null, int $limit = 40): Collection
    {
        $query = $tiket->kronologis()->with('user');

        if ($afterId) {
            return $query->where('id', '>', $afterId)
                ->orderBy('timestamp', 'asc')
                ->orderBy('id', 'asc')
                ->get();
        }

        if ($beforeId) {
            $items = $query->where('id', '<', $beforeId)
                ->orderBy('timestamp', 'desc')
                ->orderBy('id', 'desc')
                ->take($limit)
                ->get();
            return $items->sortBy('timestamp')->values();
        }

        return $query->orderBy('timestamp', 'asc')->orderBy('id', 'asc')->get();
    }
}

<?php

namespace App\Services;

use App\Models\Dokumentasi;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumentasiService
{
    /**
     * Upload satu atau banyak foto dokumentasi untuk tiket
     *
     * @param Tiket $tiket
     * @param array $data
     * @param UploadedFile[]|UploadedFile|null $files
     * @return Collection<int, Dokumentasi>
     */
    public function upload(Tiket $tiket, array $data, $files = null): Collection
    {
        return DB::transaction(function () use ($tiket, $data, $files) {
            $createdDocs = collect();
            $timestamp = !empty($data['timestamp'])
                ? Carbon::parse($data['timestamp'])
                : Carbon::now();

            $kategori = $data['kategori'] ?? 'Lain-lain';
            $latitude = !empty($data['latitude']) ? (float) $data['latitude'] : null;
            $longitude = !empty($data['longitude']) ? (float) $data['longitude'] : null;

            // Pastikan files dalam bentuk array
            $fileList = [];
            if ($files instanceof UploadedFile) {
                $fileList[] = $files;
            } elseif (is_array($files)) {
                $fileList = $files;
            }

            foreach ($fileList as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                    $filename = 'doc_' . $tiket->id . '_' . now()->format('Ymd_His') . '_' . Str::random(6) . '.' . $extension;
                    $path = $file->storeAs('dokumentasi/' . $tiket->id, $filename, 'public');

                    // Auto compress/resize on server if large
                    $this->compressImageOnServer(storage_path('app/public/' . $path));

                    $doc = Dokumentasi::create([
                        'id_tiket' => $tiket->id,
                        'kategori' => $kategori,
                        'file_path' => $path,
                        'timestamp' => $timestamp,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ]);

                    $createdDocs->push($doc);
                }
            }

            // Jika ada path manual/string yang dikirim (misal via test atau seeder)
            if (empty($fileList) && !empty($data['file_path'])) {
                $doc = Dokumentasi::create([
                    'id_tiket' => $tiket->id,
                    'kategori' => $kategori,
                    'file_path' => $data['file_path'],
                    'timestamp' => $timestamp,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);

                $createdDocs->push($doc);
            }

            // Auto-update status tiket jadi PROSES jika masih OPEN
            if ($tiket->status === 'OPEN') {
                $tiket->update(['status' => 'PROSES']);
            }

            return $createdDocs;
        });
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
     * Hapus foto dokumentasi dan file fisiknya dari storage
     */
    public function delete(Dokumentasi $dokumentasi): bool
    {
        return DB::transaction(function () use ($dokumentasi) {
            if ($dokumentasi->file_path && Storage::disk('public')->exists($dokumentasi->file_path)) {
                Storage::disk('public')->delete($dokumentasi->file_path);
            }

            return (bool) $dokumentasi->delete();
        });
    }
}

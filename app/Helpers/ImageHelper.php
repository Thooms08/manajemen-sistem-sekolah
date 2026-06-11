<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Resolve URL gambar secara otomatis:
     * - Path lama "assets/..."        → asset('assets/...')  (masih di public/)
     * - Path baru "folder/file.jpg"   → Storage::url(...)    (di storage/app/public/)
     * - null / kosong                 → null
     */
    public static function url(?string $path): ?string
    {
        if (!$path || $path === '-') {
            return null;
        }

        // Path lama yang masih ada di public/assets/
        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        // Path baru di storage/app/public/
        return Storage::url($path);
    }
}

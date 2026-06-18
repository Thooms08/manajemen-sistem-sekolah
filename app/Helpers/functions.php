<?php

use App\Helpers\PermissionHelper;

if (!function_exists('can')) {
    /**
     * Cek apakah user yang sedang login punya aksi tertentu pada modul.
     * Admin selalu return true.
     *
     * @param string $modul  Kode modul (sesuai config/modules.php)
     * @param string $aksi   view | create | edit | delete
     */
    function can(string $modul, string $aksi = 'view'): bool
    {
        return PermissionHelper::can($modul, $aksi);
    }
}

if (!function_exists('canAny')) {
    /**
     * Cek apakah user punya akses apapun ke modul ini (minimal satu aksi).
     * Dipakai untuk menentukan apakah menu sidebar ditampilkan.
     */
    function canAny(string $modul): bool
    {
        return PermissionHelper::canAny($modul);
    }
}

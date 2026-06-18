<?php

namespace App\Helpers;

use App\Models\Pengaturan\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Helper untuk cek permission user di Blade view.
 *
 * Penggunaan di Blade:
 *   @if(can('data_guru', 'create')) ... @endif
 *   @if(canAny('data_guru'))         ... @endif  (punya aksi apapun)
 *
 * Fungsi helper global tersedia via:
 *   can('modul', 'aksi')
 *   canAny('modul')
 *   userPermissions()
 */
class PermissionHelper
{
    /**
     * Cache permissions user saat ini (per request).
     */
    private static ?array $perms = null;

    public static function getPermissions(): array
    {
        if (self::$perms !== null) {
            return self::$perms;
        }

        if (!Auth::check()) {
            self::$perms = [];
            return self::$perms;
        }

        $user     = Auth::user();
        $roleSlug = $user->role ?? $user->rules ?? '';

        // Admin punya semua aksi
        if ($roleSlug === 'admin') {
            self::$perms = ['__admin__' => true];
            return self::$perms;
        }

        $role = Role::with('permissions')->where('slug', $roleSlug)->first();
        if (!$role) {
            self::$perms = [];
            return self::$perms;
        }

        $map = [];
        foreach ($role->permissions as $perm) {
            $map[$perm->modul] = (array) $perm->aksi;
        }

        self::$perms = $map;
        return self::$perms;
    }

    /**
     * Reset cache (panggil bila perlu refresh, misal setelah update permission).
     */
    public static function reset(): void
    {
        self::$perms = null;
    }

    /**
     * Cek apakah user punya aksi tertentu pada modul.
     */
    public static function can(string $modul, string $aksi = 'view'): bool
    {
        $perms = self::getPermissions();

        // Admin selalu bisa
        if (isset($perms['__admin__'])) {
            return true;
        }

        return in_array($aksi, $perms[$modul] ?? []);
    }

    /**
     * Cek apakah user punya aksi apapun pada modul (modul muncul di sidebar).
     */
    public static function canAny(string $modul): bool
    {
        $perms = self::getPermissions();

        if (isset($perms['__admin__'])) {
            return true;
        }

        return !empty($perms[$modul]);
    }
}

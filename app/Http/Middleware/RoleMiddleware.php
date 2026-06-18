<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaturan\Role;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Mendukung dua mode:
     *   role:admin          → cek tepat satu role
     *   role:dynamic        → lolos jika user punya role apapun di tabel roles
     *                         (digunakan untuk grup route dashboard user)
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            abort(403, 'Anda harus login terlebih dahulu.');
        }

        $user     = Auth::user();
        $userRole = $user->role ?? $user->rules ?? '';

        // Mode khusus: loloskan semua role yang terdaftar di tabel roles
        if ($role === 'dynamic') {
            $exists = Role::where('slug', $userRole)->exists();
            if (!$exists) {
                abort(403, 'Akses ditolak. Role Anda tidak terdaftar.');
            }
            return $next($request);
        }

        // Mode normal: cek tepat satu role (mis. 'admin')
        if ($userRole !== $role) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk role: ' . $role);
        }

        return $next($request);
    }
}

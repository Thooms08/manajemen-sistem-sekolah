<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaturan\Role;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: CheckPermission
 *
 * Penggunaan di route: ->middleware('permission:nama_modul')
 * atau: ->middleware('permission:nama_modul,aksi')
 *
 * Logika:
 *  - Admin          → selalu lolos
 *  - User dinamis   → cek permission di DB; jika ditolak, redirect back dengan flash alert
 *  - Belum login    → redirect ke halaman login
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $modul, string $aksi = 'view'): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user     = Auth::user();
        $userRole = $user->role ?? $user->rules ?? '';

        // Admin selalu lolos
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Cek permission user di database
        $role = Role::with('permissions')
            ->where('slug', $userRole)
            ->first();

        if (!$role) {
            return $this->denyAccess(
                $request,
                'Role Anda tidak ditemukan. Hubungi Admin.'
            );
        }

        $perm = $role->permissions->firstWhere('modul', $modul);

        if (!$perm || !in_array($aksi, (array) $perm->aksi)) {
            // Label modul dari config
            $modulConf  = config("modules.{$modul}");
            $modulLabel = $modulConf['label'] ?? $modul;

            return $this->denyAccess(
                $request,
                "Maaf, Anda tidak memiliki akses ke halaman {$modulLabel}. Silakan hubungi Admin jika ini keliru."
            );
        }

        return $next($request);
    }

    /**
     * Tolak akses: jika AJAX/JSON → return JSON 403,
     * jika request biasa → redirect back dengan flash error.
     */
    private function denyAccess(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message], 403);
        }

        // Redirect ke dashboard user dengan flash error
        $back = url()->previous();
        $dashboard = route('user.dashboard');

        // Jika previous URL sama dengan current (loop), arahkan ke dashboard
        $target = ($back && $back !== $request->fullUrl()) ? redirect()->back() : redirect()->route('user.dashboard');

        return $target->with('permission_error', $message);
    }
}

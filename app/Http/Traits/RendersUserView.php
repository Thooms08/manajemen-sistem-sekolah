<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

/**
 * Trait RendersUserView
 *
 * Gunakan di controller yang juga melayani user dinamis.
 * Ganti return view('admin.xxx') dengan return $this->renderView('admin.xxx', $data)
 *
 * Logika:
 *  - Admin        → render admin.xxx  (tidak berubah)
 *  - User dinamis → cek apakah user.xxx ada, jika ya render itu, jika tidak fallback ke admin.xxx
 *
 * Konversi nama view:
 *   admin.data_master.guru  → user.data_master.guru
 *   admin.keuangan.pemasukan → user.keuangan.pemasukan
 *   admin.informasi_sekolah.informasi → user.informasi_sekolah.informasi
 */
trait RendersUserView
{
    protected function renderView(string $viewName, array $data = []): \Illuminate\View\View|\Illuminate\Contracts\View\Factory
    {
        $user     = Auth::user();
        $roleSlug = $user ? ($user->role ?? $user->rules ?? '') : '';

        // Admin selalu pakai view admin
        if ($roleSlug === 'admin' || !Auth::check()) {
            return view($viewName, $data);
        }

        // User dinamis: swap prefix admin. → user.
        $userViewName = preg_replace('/^admin\./', 'user.', $viewName);

        // Cek apakah view user ada
        if ($userViewName !== $viewName && View::exists($userViewName)) {
            return view($userViewName, $data);
        }

        // Fallback ke view admin
        return view($viewName, $data);
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $isSecureRequest = request()->isSecure()
            || Str::contains((string) request()->header('x-forwarded-proto'), 'https');

        if (app()->environment('production', 'staging') || $isSecureRequest) {
            URL::forceScheme('https');
            config(['session.secure' => true]);
        }

        // Jika Anda ingin memaksa HTTPS di semua environment (termasuk local), 
        // hapus pengecekan 'if' di atas dan cukup gunakan baris di bawah ini:
        // URL::forceScheme('https');

        View::composer('*', function ($view) {
            $sekolah = DB::table('profile_sekolah')->first();

            $view->with('sekolah', $sekolah ?: (object) [
                'nama_sekolah' => 'Sistem Informasi Sekolah',
                'logo' => null,
                'deskripsi' => 'Selamat datang di sistem informasi sekolah kami.',
                'alamat' => '-',
                'no_hp' => '-',
                'email' => '-',
                'tautan_google_maps' => null,
                'akreditasi' => 'Belum diatur',
                'foto_sekolah' => null,
            ]);
        });
    }
}
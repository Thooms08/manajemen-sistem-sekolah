<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
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
        if (! $this->app->runningInConsole()) {
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
        }

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

        // Blade directive: @imgUrl($path) — otomatis resolve path lama (public/assets) dan baru (storage/public)
        \Illuminate\Support\Facades\Blade::directive('imgUrl', function ($expression) {
            return "<?php echo \\App\\Helpers\\ImageHelper::url($expression); ?>";
        });

        // Blade directive: @canDo('modul', 'aksi') ... @endCanDo
        // Shortcut untuk cek permission tanpa konflik dengan bawaan Laravel
        \Illuminate\Support\Facades\Blade::if('canDo', function (string $modul, string $aksi = 'view') {
            return \App\Helpers\PermissionHelper::can($modul, $aksi);
        });

        // Blade directive: @canAnyDo('modul') ... @endCanAnyDo
        \Illuminate\Support\Facades\Blade::if('canAnyDo', function (string $modul) {
            return \App\Helpers\PermissionHelper::canAny($modul);
        });
    }
}
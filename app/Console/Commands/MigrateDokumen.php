<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateDokumen extends Command
{
    protected $signature   = 'migrate:dokumen {--fresh : Drop all tables and re-run migrations} {--seed : Run seeders after migration}';
    protected $description = 'Run migrations for the dokumen_db database (tabel dokumen_ppdb)';

    public function handle(): int
    {
        $this->info('Running migrations for dokumen_db...');

        $options = [
            '--database' => 'dokumen_db',
            '--path'     => 'database/migrations/dokumen',
            '--force'    => true,
        ];

        if ($this->option('fresh')) {
            Artisan::call('migrate:fresh', $options);
        } else {
            Artisan::call('migrate', $options);
        }

        $this->line(Artisan::output());
        $this->info('Done!');

        return self::SUCCESS;
    }
}

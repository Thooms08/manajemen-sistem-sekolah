<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateKeuangan extends Command
{
    protected $signature   = 'migrate:keuangan {--fresh : Drop all tables and re-run migrations} {--seed : Run seeders after migration}';
    protected $description = 'Run migrations for the keuangan_db database';

    public function handle(): int
    {
        $this->info('Running migrations for keuangan_db...');

        $options = [
            '--database' => 'keuangan_db',
            '--path'     => 'database/migrations/keuangan',
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

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:database-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat dump database ke storage/app/backup';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $connection = config('database.connections.'.config('database.default'));

        if (($connection['driver'] ?? null) !== 'mysql') {
            $this->error('Backup hanya mendukung koneksi MySQL.');

            return self::FAILURE;
        }

        Storage::makeDirectory('backup');

        $path = Storage::path('backup/backup-'.now()->format('Y-m-d').'.sql');

        $result = Process::timeout(600)->run([
            'mysqldump',
            '--host='.$connection['host'],
            '--port='.$connection['port'],
            '--user='.$connection['username'],
            '--password='.$connection['password'],
            '--single-transaction',
            '--routines',
            $connection['database'],
            '--result-file='.$path,
        ]);

        if ($result->failed()) {
            $this->error('Backup gagal: '.$result->errorOutput());
            report(new \RuntimeException('Backup database gagal: '.$result->errorOutput()));

            return self::FAILURE;
        }

        $this->info('Backup tersimpan: '.$path);

        return self::SUCCESS;
    }
}

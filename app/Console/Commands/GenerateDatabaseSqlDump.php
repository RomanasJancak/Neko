<?php

namespace App\Console\Commands;

use App\Models\DatabaseSqlBackup;
use Illuminate\Console\Command;

class GenerateDatabaseSqlDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump-sql {--name=} {--chunk-size-kb=1024}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SQL restore dump (schema + data), split into chunk files when size exceeds configured chunk size.';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseSqlBackup $backup): int
    {
        $name = $this->option('name');
        $chunkSizeKb = (int) $this->option('chunk-size-kb');

        if ($chunkSizeKb <= 0) {
            $this->error('Option --chunk-size-kb must be greater than 0.');

            return self::FAILURE;
        }

        $result = $backup->createRestoreDump($name, $chunkSizeKb * 1024);

        $this->info('SQL dump created successfully.');
        $this->line('Total bytes: ' . number_format($result['total_bytes']));
        $this->line('Chunk size (bytes): ' . number_format($result['chunk_size_bytes']));
        $this->line('Generated files:');

        foreach ($result['files'] as $file) {
            $this->line('- ' . $file);
        }

        return self::SUCCESS;
    }
}

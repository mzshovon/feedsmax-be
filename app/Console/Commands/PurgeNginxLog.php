<?php

namespace App\Console\Commands;

use App\Classes\LogPurgeManager;
use Illuminate\Console\Command;

class PurgeNginxLog extends Command
{
    /**
     * The name and signature of the console command.
     * dir: Name of log directory
     * filename: Name of log file
     * size: Max filesize to be purged
     * unit: Size in MB,GB,KB etc.
     * duration: Keep last max days log if content check true.
     * purge_content: content wise or filewise purge.
     * @var string
     */
    protected $signature = 'purge:nginx-log {--dir} {--filename} {--size} {--unit} {--duration} {--purge_content}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will purge nginx log/log content ex. access.log, error.log';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dir = !$this->option('dir') ? "/nginx" : $this->option('dir');
        $filename = !$this->option('filename') ? "error.log" : $this->option('filename');
        $size = !$this->option('size') ? 1 : $this->option('size');
        $unit = !$this->option('unit') ? "GB" : $this->option('unit');
        $duration = !$this->option('duration') ? 5 : $this->option('duration');
        $purge_content = !$this->option('purge_content') ? true : $this->option('purge_content');

        (new LogPurgeManager())->purge(
            $dir,
            $filename,
            $purge_content,
            $size,
            $unit,
            $duration
        );

        return $this->info("Log purging done for {$filename}");
    }
}

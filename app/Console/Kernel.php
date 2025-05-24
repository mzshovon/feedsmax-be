<?php

namespace App\Console;

use App\Classes\DatabasePurgeManager;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    const DATABASE_PURGING_TTL = "12:01";
    const DATABASE_PURGING_THRESHOLD = 90;
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(fn (DatabasePurgeManager $databasePurgeManager) =>
            $databasePurgeManager->ttl(self::DATABASE_PURGING_THRESHOLD)->purge()
        )->dailyAt(self::DATABASE_PURGING_TTL)
        ->name('database-purging')
        ->onOneServer();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

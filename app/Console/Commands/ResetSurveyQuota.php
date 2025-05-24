<?php

namespace App\Console\Commands;

use App\Classes\ResetSurveyQuotaAndStoreOldHistory;
use Illuminate\Console\Command;

class ResetSurveyQuota extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:survey-quota';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset survey quota and store previous records into history';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $resetClassObject = new ResetSurveyQuotaAndStoreOldHistory();
        $reset = $resetClassObject->reset();
        if($reset) {
            return $this->info("Survey quota reset successfully!");
        }
        return $this->error("Survey quota reset failed!");
    }
}

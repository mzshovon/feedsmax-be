<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateTablesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $column;
    protected int $value;
    protected array $updateDate;
    protected object $modelRepoObject;
    /**
     * Create a new job instance.
     */
    public function __construct(string $column, int $value, array $updateDate, object $modelRepoObject)
    {
        $this->column = $column;
        $this->value = $value;
        $this->updateDate = $updateDate;
        $this->modelRepoObject = $modelRepoObject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->modelRepoObject->update($this->column, $this->value, $this->updateDate);
    }
}

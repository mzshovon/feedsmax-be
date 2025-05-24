<?php

namespace App\Classes;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class DatabasePurgeManager
{
    protected $tables = [
        [
            "name" => "hits",
            "check" => "created_at"
        ],
        [
            "name" => "attempts",
            "check" => "created_at"
        ]
    ];

    protected $backupDisabledTableList = ["hits"]; // Enable for purging

    protected $purgeDisabledTableList = []; // Enable for backup

    protected string $backupTablePrefix = "backup_";

    protected int $ttl = 90;

    /**
     * @param int $ttl
     *
     * @return $this
     */
    public function ttl(int $ttl)
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * @return void
     */
    public function purge() : void
    {
        $this->process();
    }

    /**
     * @return void
     */
    protected function process() : void
    {
        foreach ($this->tables as $table) {
            $this->backupAndDeleteOldData($table['name'], $table['check']);
        }
    }

    /**
     * @param string $table
     * @param string $column
     *
     * @return void
     */
    protected function backupAndDeleteOldData(string $table, string $column) : void
    {
        DB::beginTransaction();

        try {

            if(in_array($table, $this->backupDisabledTableList)) {
                $this->deleteOldData($table, $column);
            } else if(in_array($table, $this->purgeDisabledTableList)) {
                $this->backupOldData($table, $column);
            } else {
                if($this->backupOldData($table, $column)) {
                    $this->deleteOldData($table, $column);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            // If an exception occurs, rollback the transaction
            DB::rollBack();
            throw new Exception("Something went wrong with database..");
        }
    }

    /**
     * @param string $table
     * @param string $column
     *
     * @return bool
     */
    private function backupOldData(string $table, string $column) : bool
    {
        $threshold = Carbon::now()->subDays($this->ttl);
        return DB::table($table)
            ->orderBy($column)
            ->where($column, '<', $threshold)
            ->chunk(1000, function ($oldData) use ($table){
                foreach ($oldData as $record) {
                    DB::table($this->backupTablePrefix . $table)->insert((array)$record);
                }
            });
    }

    /**
     * @param string $table
     * @param string $column
     *
     * @return void
     */
    private function deleteOldData(string $table, string $column) : void
    {
        $threshold = Carbon::now()->subDays($this->ttl);
        DB::table($table)
            ->where($column, '<', $threshold)
            ->delete();
    }

}

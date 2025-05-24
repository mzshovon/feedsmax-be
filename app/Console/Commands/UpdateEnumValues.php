<?php

namespace App\Console\Commands;

use App\Enums\ChoiceType;
use App\Enums\QuotaType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateEnumValues extends Command
{
    protected $signature = 'enum:update {table} {column} {enumKey}';
    protected $description = 'Update ENUM values for a specified column in a table based on config/enums.php';

    public function handle()
    {
        try {
            $table = $this->argument('table');
            $column = $this->argument('column');
            $func = $this->argument('enumKey');

            if (method_exists($this, $func)) {
                $enumValues = $this->$func();
            }

            if(empty($enumValues)){
                $this->error("No ENUM values found for key: $func");
                return;
            }

            $enumString = implode("','", $enumValues);

            DB::statement("ALTER TABLE $table MODIFY COLUMN $column ENUM('$enumString')");

            $this->info("ENUM values for $column in $table updated successfully.");

        } catch (\Exception $ex) {
            $this->error("Columns or table doesn't exists .{$ex->getMessage()}");
        }

    }

    public function select(){
        return array_column(ChoiceType::cases(), 'value');
    }

    public function quota(){
        return array_column(QuotaType::cases(), 'value');
    }
}

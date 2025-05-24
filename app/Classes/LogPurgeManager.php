<?php

namespace App\Classes;

use Carbon\Carbon;

class LogPurgeManager
{
    protected array $conversionFormula = [
        "GB" => 1024 * 1024 * 1024,
        "MB" => 1024 * 1024,
        "KB" => 1024
    ];

    protected array $searchDateFormatLogWise = [
        "error.log" => "Y/m/d",
        "access.log" => "d/M/Y",
    ];

    /**
     * @param string $logDir
     * @param $logFile= "error.log"
     * @param bool $contents
     * @param int|null $maxSize
     * @param int $numDaysToKeep
     *
     * @return [type]
     */
    public function purge(
        string $logDir = '/nginx',
        string $logFile= "error.log",
        bool $purgeContent = true,
        int|null $maxSize = 1,
        string|null $unit = "GB",
        int $numDaysToKeep = 5
    ) : void
    {
        if($purgeContent) {
            $this->purgeOldLog($logDir, $logFile, $maxSize, $unit, $numDaysToKeep);
        } else {
            $this->purgeOldFile($logDir, $logFile);
        }
    }

    /**
     * @param string $logDirectory
     * @param string $logFile
     *
     * @return void
     */
    private function purgeOldFile(string $logDirectory, string $logFile) : void
    {
        $fileName = $logDirectory . DIRECTORY_SEPARATOR . $logFile;
        if(file_exists(storage_path($fileName))){
            unlink(storage_path($fileName));
        }
    }

    /**
     * @param string $logDirectory
     * @param string $logFile
     * @param int $maxSize
     * @param string $unit
     * @param int $numDaysToKeep
     *
     * @return void
     */
    private function purgeOldLog(
        string $logDirectory,
        string $logFile,
        int $maxSize = 1,
        string $unit,
        int $numDaysToKeep = 5
    ) : void
    {
        $logDirectory = storage_path($logDirectory);

        // Convert max size from unit to bytes
        $maxSizeBytes = $maxSize * $this->conversionFormula[$unit];

        // Open the directory
        if ($handle = opendir($logDirectory)) {
                $filePath = $logDirectory . DIRECTORY_SEPARATOR . $logFile;

                if (is_file($filePath)) {

                    // Check file size
                    $fileSize = filesize($filePath);
                    if ($fileSize) {
                        $intervals = $this->getTimeFrames($numDaysToKeep, $logFile, "d");
                        $intervalString = implode(":|", $intervals); // Filter between command
                        $grepCommand = "grep -E '$intervalString' $filePath ";
                        exec($grepCommand, $filteredLines);

                        // If found, truncate the file from that position
                        if (!empty($filteredLines)) {

                            if(!is_writeable($filePath)){
                                // Update the permission to write Log file
                                $this->updateFilePermission($filePath);
                            }
                            //Make array output to loggable string
                            $lines = implode("\n", $filteredLines) . "\n";
                            // Rewrite the filtered data
                            file_put_contents("{$filePath}", $lines);
                        }
                    }
                }

            // Close the directory handle
            closedir($handle);
        }
    }

    /**
     * @param int $threshold
     * @param string $fileName
     * @param string $type
     *
     * @return array
     */
    private function getTimeFrames(int $threshold, string $fileName, string $type): array
    {
        $intervals = [];
        $intervalMap = [
            'd' => 'day',
            'h' => 'hour',
            'm' => 'minute',
        ];

        if (isset($intervalMap[$type])) {
            $interval = $intervalMap[$type];

            for ($i = $threshold -1; $i >= 0; $i--) {
                $intervals[] = Carbon::now()->sub("$i $interval")->format($this->searchDateFormatLogWise[$fileName]);
            }
        }
        return $intervals;
    }

    /**
     * @param string $filePath
     * @param int $permission
     *
     * @return void
     */
    private function updateFilePermission(string $filePath, int $permission = 777) : void
    {
        exec("chmod {$permission} -R {$filePath}");
    }

}



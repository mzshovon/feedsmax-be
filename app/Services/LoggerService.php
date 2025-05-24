<?php

namespace App\Services;

use App\Services\Contracts\LoggerServiceInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Psr\Log\LoggerInterface;

class LoggerService implements LoggerServiceInterface
{
    const LOG_CHANNEL = 'custom';

    private static LogObject $logObject;
    private string $parentLogDir = "logs";
    private string $filedir = "{year}{month}{date}";
    private string $filename = "app-{hostname}-{date}_{hour}";

    private array $excludeResponseParamList = [
        'data.data.url',
        'data.theme',
        'data.questions',
        'data.choice_types',
        'data.nps'
    ];

    public function __construct()
    {
    }

    /**
     * @return void
     */
    public static function init(): void
    {
        $request = app('request');
        $x_request_id = $request->header('X-Request-ID') ?? "";

        if(!in_array(strtolower($request->method()), ['options', 'head'])) {
            self::$logObject = new LogObject();
            self::$logObject->setStartTime(round(microtime(true) * 1000)); // Set init time
            self::$logObject->setUrl($request->path()); // Set request URL
            self::$logObject->setRequest($request->all()); // Set request body
            self::$logObject->setXRequestId($x_request_id); // Set X Request ID
        }
    }

    /**
     * @param array|string $append_data
     *
     * @return void
     */
    public function append(array|string $append_data): void
    {
        self::$logObject->setExtra($append_data);
    }

    /**
     * @param string $exception
     * @param string $trace
     *
     * @return void
     */
    public function exception(string $exception, string $trace = ""): void
    {
        $exception = substr($exception,0,100);
        self::$logObject->setException($exception);

        if(!empty($trace)) {
            $trace = substr($trace,0,10);
            self::$logObject->setStackTrace($trace);
        }
    }

    /**
     * @return void
     */
    public function close(): void
    {
        $decodedResponse = [];
        $status = 000;
        $app = new App();
        if($app::has('response')) {
            $status = app('response')->status();
            $decodedResponse = json_decode(app('response')->content(), true) ?? [];
        }

        $response = $this->exclude($decodedResponse, $this->excludeResponseParamList);
        self::$logObject->setResponseCode($status); // Set response body
        self::$logObject->setResponse($response); // Set response body
        self::$logObject->setEndTime(round(microtime(true) * 1000)); // Set execution end time
        if($status >= 200 && $status < 300) {
            self::$logObject->setLogLevel("INFO"); // Set execution end time
        } else {
            self::$logObject->setLogLevel("ERROR"); // Set execution end time
        }
        $this->log($this->directory(), $this->filename(), self::$logObject->toJson(), $status);
    }

    /**
     * @return string
     */
    public function filename(): string
    {
        return str_replace(
            ['{hostname}', '{date}', '{hour}'],
            [
                gethostname(),
                Carbon::now()->format("Y-m-d"),
                Carbon::now()->hour
            ],
            $this->filename
        );
    }

    /**
     * @return string
     */
    public function directory(): string
    {
        $carbonFormattedDate = Carbon::now();
        return str_replace(
            ['{year}', '{month}', '{date}'],
            [
                $carbonFormattedDate->format("Y"),
                $carbonFormattedDate->format("m"),
                $carbonFormattedDate->format("d")
            ],
            $this->filedir
        );
    }


    /**
     * @param int $days
     *
     * @return void
     * @throws Exception
     */
    public function purging(int $days): void
    {
        $thresholdRangeValue = (int)Carbon::now()->subDays($days)->format("Ymd");
        $regexForCustomDateFormattedLogDir = '/[0-9]+/'; // Regex customized date formatted dir list
        $dirs = scandir(storage_path()); // All dirs under storage
        $getCustomizedLogDirList = []; // Regex matched dir list

        foreach($dirs as $dir) {
            if(preg_match($regexForCustomDateFormattedLogDir, $dir)) {
                $getCustomizedLogDirList[] = $dir;
            }
        }

        if(!empty($getCustomizedLogDirList)) {
            foreach ($getCustomizedLogDirList as $dir) {
                if((int)$dir < $thresholdRangeValue) {
                    $this->removeDir(storage_path($dir));
                }
            }
        }
    }

    /**
     * @param string $dir
     *
     * @return void
     * @throws Exception
     */
    private function removeDir(string $dir): void
    {
        // File::deletedirectory($dir) used for not empty dir delete because rmdir() doesn't allow to delete non-empty dir
        retry(3, fn() => File::deletedirectory($dir));
    }

    /**
     * @param array $content
     * @param array $exclusions
     *
     * @return array
     */
    private function exclude(array $content, array $exclusions): array
    {
        if(!empty($exclusions)) {
            foreach ($exclusions as $exclusion) {
                $keys = explode(".", $exclusion);
                checkAndUnsetRecusiveArrayKey($content, $keys);
            }
        }

        return $content;
    }

    /**
     * @param string|null $fileName
     * @param string|null $logBody
     * @param int $status
     *
     * @return void
     */
    private function log(?string $fileDir, ?string $fileName, ?string $logBody, int $status): void
    {
        config(['logging.channels.custom.path' => storage_path("{$this->parentLogDir}/{$fileDir}/{$fileName}.log")]);
        if($status >= 200 && $status < 300) {
            logger()->channel(self::LOG_CHANNEL)->info($logBody);
        } else {
            logger()->channel(self::LOG_CHANNEL)->error($logBody);
        }
    }

}

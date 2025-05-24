<?php

namespace App\Services\Contracts;

use Illuminate\Http\Request;

interface LoggerServiceInterface
{
    public static function init(): void;
    public function append(array|string $append_data): void;
    public function exception(string $exception): void;
    public function filename(): string;
    public function directory(): string;
    public function close(): void;
    public function purging(int $days): void;
}

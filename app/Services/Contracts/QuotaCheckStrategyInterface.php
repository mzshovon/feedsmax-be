<?php

namespace App\Services\Contracts;

interface QuotaCheckStrategyInterface {

    public function checkQuota(string $type, string $param) : bool;

}

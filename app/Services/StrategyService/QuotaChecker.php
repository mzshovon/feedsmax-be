<?php

namespace App\Services\StrategyService;

use App\Services\Contracts\QuotaCheckStrategyInterface;

class QuotaChecker {

    protected object $strategy;

    /**
     * @param QuotaCheckStrategyInterface $strategy
     *
     * @return
     */
    public function setStrategy(QuotaCheckStrategyInterface $strategy) {
        $this->strategy = $strategy;
    }

    /**
     * @param string $quota_type
     * @param string $quota_value
     *
     * @return bool
     */
    public function checkQuota(string $quota_type, string $quota_value): bool {
        return $this->strategy->checkQuota($quota_type, $quota_value);
    }


}

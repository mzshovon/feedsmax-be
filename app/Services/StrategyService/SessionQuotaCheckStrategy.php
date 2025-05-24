<?php

namespace App\Services\StrategyService;

use App\Models\SurveyQuota;
use App\Repositories\SurveyQuotaRepo;
use App\Services\Contracts\QuotaCheckStrategyInterface;

class SessionQuotaCheckStrategy implements QuotaCheckStrategyInterface {

    /**
     * @param string $quota_type
     * @param string $quota_value
     *
     * @return bool
     */
    public function checkQuota(string $quota_type, string $quota_value): bool
    {
        $repo = new SurveyQuotaRepo(new SurveyQuota());
        return $repo->quotaCheck($quota_type, $quota_value);
    }

}

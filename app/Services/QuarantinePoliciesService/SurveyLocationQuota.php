<?php

namespace App\Services\PolicyService;

use App\Services\Contracts\RuleEngineInterface;
use App\Services\StrategyService\LocationQuotaCheckStrategy;
use App\Services\StrategyService\QuotaChecker;

class SurveyLocationQuota implements RuleEngineInterface
{
    private $quotaChecker;

    public function __construct()
    {
        $this->quotaChecker = new QuotaChecker();
    }

    /**
     * Here match() will check if msisdn has hits and attempt both in between provision days
     * then it will return false
     * otherwise return true
     * ex. provision explained if any attempt placed in session ex. 5, 10, 15 minutes
     * If the total count set to 0 after decrement it will return false
     * After certain days it will check and place new attempt for that particular msisdn
     */
    /**
     * @param array $request
     * @param mixed ...$args
     *
     * @return bool
     */
    public function match(array $request, ...$args): bool
    {
        $session = $args[0];
        $this->quotaChecker->setStrategy(new LocationQuotaCheckStrategy());
        $location = $this->getLocation();
        return $this->quotaChecker->checkQuota("location", $location);
    }

    /**
     * It will fetch data from defined table
     * @return string
     */
    private function getLocation() : string
    {
        $location = "";
        return $location;
    }
}

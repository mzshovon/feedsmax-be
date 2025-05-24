<?php

namespace App\Services\RuleService;

use App\Models\Hit;
use App\Repositories\HitRepo;
use App\Services\Contracts\RuleEngineInterface;
use Carbon\Carbon;

class SurveyViewChannelDay implements RuleEngineInterface
{
    private HitRepo $hitRepo;

    public function __construct()
    {
        $this->hitRepo = new HitRepo(new Hit());
    }

    /**
     * Here match() will check if msisdn has hits and attempt both in between provision days
     * then it will return false
     * otherwise return true
     * ex. provision explained if any attempt placed between 15 days
     * then another attempt won't be placed
     * After 15 days it will check and place new attempt for that particular msisdn
     */
    public function match(array $request, ...$args): bool
    {
        [$survey,$acceptDay] = $args[0];
        $numberOfDaysFromLastAcceptedAttempt = $this->dayFromLastAcceptedAttemptToToday(
            $request['msisdn'],
            $request['channel_id'],
            $request['trigger_id']
        );
        if (gettype($numberOfDaysFromLastAcceptedAttempt) === "integer" && $numberOfDaysFromLastAcceptedAttempt <= $acceptDay) {
            return false;
        }
        return true;
    }

    /**
     * @param string $msisdn
     * @param string $channel
     * @param int $trigger_id
     * @return int|null
     */
    private function dayFromLastAcceptedAttemptToToday(string $msisdn, string $channel, int $trigger_id): int|null
    {
        $dayFromLastAcceptedAttemptToToday = null;
        $lastAcceptedAttemptFromHit = $this->hitRepo->getLastAttemptView($msisdn, $channel, $trigger_id);
        if ($lastAcceptedAttemptFromHit && $lastAcceptedAttemptFromHit->attempt_date) {
            $dayFromLastAcceptedAttemptToToday = now()->diffInDays(Carbon::parse($lastAcceptedAttemptFromHit->attempt_date));
        }
        return $dayFromLastAcceptedAttemptToToday;
    }
}

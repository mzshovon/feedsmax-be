<?php

namespace App\Services\RuleService;

use App\Models\Attempt;
use App\Repositories\AttemptRepo;
use App\Services\Contracts\RuleEngineInterface;
use Carbon\Carbon;

class SurveyAcceptChannelDay implements RuleEngineInterface
{
    private AttemptRepo $attemptRepo;

    public function __construct()
    {
        $this->attemptRepo = new AttemptRepo(new Attempt());
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
        $numberOfDaysFromLastAcceptedAttempt = $this->dayFromLastSubmittedAttemptResponseToToday(
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
    private function dayFromLastSubmittedAttemptResponseToToday(string $msisdn, string $channel, int $trigger_id): int|null
    {
        $dayFromLastAcceptedAttemptToToday = null;
        $lastAcceptedAttempt = $this->attemptRepo->getLastAttempt($msisdn, $channel, $trigger_id);
        if ($lastAcceptedAttempt && $lastAcceptedAttempt->submitted_at) {
            $dayFromLastAcceptedAttemptToToday = now()->diffInDays(Carbon::parse($lastAcceptedAttempt->attempt_date));
        }
        return $dayFromLastAcceptedAttemptToToday;
    }
}

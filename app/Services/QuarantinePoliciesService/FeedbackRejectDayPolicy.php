<?php

namespace App\Services\RuleService;

use App\Models\Attempt;
use App\Repositories\StriveRepo;
use App\Services\Contracts\RuleEngineInterface;
use Carbon\Carbon;

class FeedbackRejectDayPolicy implements RuleEngineInterface
{
    private StriveRepo $StriveRepo;

    public function __construct()
    {
        $this->StriveRepo = new StriveRepo(new Attempt());
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
        [$survey, $rejectDay] = $args[0];
        $numberOfDaysFromLastRejectedAttempt = $this->dayFromLastRejectAttemptToToday(
            $request['msisdn'],
            $request['channel_id'],
            $request['trigger_id']
        );
        if (gettype($numberOfDaysFromLastRejectedAttempt) === "integer" && $numberOfDaysFromLastRejectedAttempt <= $rejectDay) {
            return false;
        }

        return true;
    }

    /**
     * @param string $msisdn
     * @param string $channel
     * @param int $type
     *
     * @return string|null
     */
    private function dayFromLastRejectAttemptToToday(string $msisdn, string $channel, int $trigger_id): int|null
    {
        $dayFromLastRejectedAttemptToToday = null;
        $lastRejectedAttemptFromHit = $this->StriveRepo->getLastAttempt($msisdn, $channel, $trigger_id, true);
        if ($lastRejectedAttemptFromHit && $lastRejectedAttemptFromHit->created_at) {
            $dayFromLastRejectedAttemptToToday = now()->diffInDays(Carbon::parse($lastRejectedAttemptFromHit->created_at));
        }
        return $dayFromLastRejectedAttemptToToday;
    }
}

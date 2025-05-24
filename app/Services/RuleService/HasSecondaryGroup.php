<?php

namespace App\Services\RuleService;

use App\Models\Attempt;
use App\Models\Trigger;
use App\Repositories\AttemptRepo;
use App\Repositories\TriggerRepo;
use App\Services\Contracts\RuleEngineInterface;
use Carbon\Carbon;

class HasSecondaryGroup implements RuleEngineInterface
{
    private AttemptRepo $attemptRepo;
    private TriggerRepo $triggerRepo;

    public function __construct()
    {
        $this->attemptRepo = new AttemptRepo(new Attempt());
        $this->triggerRepo = new TriggerRepo(new Trigger());
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
        return $this->hasSecondaryGroup(
            $request['msisdn'],
            $request['channel_id'],
            $request['trigger_id']
        );
    }

    /**
     * @param string $msisdn
     * @param string $channel
     * @param int $trigger_id
     *
     * @return bool
     */
    private function hasSecondaryGroup(string $msisdn, string $channel, int $trigger_id): bool
    {
        $nextGroupId = $this->triggerRepo->getEventById($trigger_id)->next_group_id;
        if($nextGroupId) {
            $data = $this->attemptRepo->getLastAttempt($msisdn, $channel, $trigger_id);
            if(!$data) {
                return true;
            }
            return $data->group_id !== intval($nextGroupId);
        }
        return false;
    }
}

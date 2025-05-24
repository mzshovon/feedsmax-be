<?php

namespace App\Services;

use App\Jobs\UpdateTablesJob;
use App\Repositories\AttemptRepo;
use App\Repositories\HitRepo;
use App\Repositories\TriggerRepo;
use Carbon\Carbon;

class AttemptService
{
    private string $secondaryChannelMapperFunc = "has_secondary_group";

    public function __construct(
        private AttemptRepo $repo,
        private readonly TriggerRepo $triggerRepo,
        private readonly HitRepo $hitRepo
    ) {
    }

    /**
     * @param string $msisdn
     * @param string $eventName
     * @param string $channelName
     *
     * @return string|null
     */
    public function checkAndFetchExistingAttemptByMsisdn(string $msisdn, string $eventName, string $channelName): string|null
    {
        $uuid = $this->repo->getUUIDByMsisdn($msisdn, $eventName, $channelName);
        return $uuid;
    }

    /**
     * @param array $request
     * @param string $channel
     * @param string $event
     *
     * @return array
     */
    public function store(
        array $request,
        string $channel,
        int $channelId,
        string $event,
        int $eventId,
        string $groupId,
        array $matchedRules = [],
        int $nextGroupId = null,
        ): array
    {
        $data['channel'] = $channel;
        $data['channel_id'] = $channelId;
        $data['event'] = $event;
        $data['event_id'] = $eventId;
        $data['view'] = false;
        $data['trigger_matches'] = json_encode($matchedRules);
        $data['group_id'] = $groupId;

        if($nextGroupId) {
            $hasLastAttempt = $this->repo->getLastAttempt($request['msisdn'], $channelId, $eventId);
            if($hasLastAttempt && $hasLastAttempt->group_id != $nextGroupId) {
                $data['group_id'] = $nextGroupId;
            }
        }
        // From request
        $data['msisdn'] = $request['msisdn'];
        $data['platform'] = isset($request['platform'])
            ? strtolower($request['platform']) : null;
        $data['device_id'] = $request['device_id'] ?? null;
        $data['model'] = isset($request['model'])
            ? strtolower($request['model']) : null;
        $data['loyalty'] = isset($request['loyalty'])
            ? strtolower($request['loyalty']) : "blank";
        $data['device_name'] = $request['device_name'] ?? null;
        $data['user_network'] = $request['user_network'] ?? null;
        $data['app_version'] = $request['app_version'] ?? null;
        $data['os_version'] = $request['os_version'] ?? null;
        $data['extra'] = isset($request['extra']) && !empty($request['extra'])
            ? json_encode($request['extra']) : null;

        $saveData = $this->repo->save($data);

        return [$saveData->id, $saveData];
    }

    /**
     * @param mixed $attemptId
     *
     * @return void
     */
    public function updateViewStatusByAttemptId($attemptId): void
    {
        $request['view'] = true;
        UpdateTablesJob::dispatch("id", $attemptId, $request, $this->repo);
        unset($request['view']);
        $request['attempt_date'] = Carbon::now();
        UpdateTablesJob::dispatch("attempt_id", $attemptId, $request, $this->hitRepo);
    }

    /**
     * @param int $id
     *
     * @return int
     */
    public function getTriggerInfoFromId(int $id): array
    {
        $triggerId = null;
        $groupId = null;
        $numOfQuestions = null;
        $channelName = null;
        $event = null;

        $attemptInfoById = $this->repo->getAttemptInfoById($id);
        if ($attemptInfoById) {
            [$triggerId, $lang, $channelId, $retry, $groupId, $nextGroupId, $groupName, $numOfQuestions] = $this->triggerRepo->getTriggerInfo($attemptInfoById->event, $attemptInfoById->channel, 'read');
            $channelName = $attemptInfoById->channel;
            $event = $attemptInfoById->event;
            $groupId = $attemptInfoById->group_id;
        }
        return [$triggerId, $groupId, $numOfQuestions, $channelName, $event];
    }
}

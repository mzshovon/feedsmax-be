<?php

namespace App\Services;

use App\Jobs\UpdateTablesJob;
use App\Repositories\StriveRepo;
use App\Repositories\HitRepo;
use App\Repositories\EventRepo;
use Carbon\Carbon;

class AttemptService
{
    private string $secondaryChannelMapperFunc = "has_secondary_group";

    public function __construct(
        private StriveRepo $repo,
        private readonly EventRepo $EventRepo,
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
        string $bucketId,
        array $matchedRules = [],
        int $nextbucketId = null,
        ): array
    {
        $data['channel'] = $channel;
        $data['channel_id'] = $channelId;
        $data['event'] = $event;
        $data['event_id'] = $eventId;
        $data['view'] = false;
        $data['trigger_matches'] = json_encode($matchedRules);
        $data['group_id'] = $bucketId;

        if($nextbucketId) {
            $hasLastAttempt = $this->repo->getLastAttempt($request['msisdn'], $channelId, $eventId);
            if($hasLastAttempt && $hasLastAttempt->group_id != $nextbucketId) {
                $data['group_id'] = $nextbucketId;
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
     * @param int $striveId
     * 
     * @return array
     */
    public function getEventInfoFromId(int $striveId): array
    {
        $eventId = null;
        $bucketId = null;
        $numOfQuestionsList = null;
        $channelName = null;
        $eventName = null;

        $striveInfoById = $this->repo->getStriveInfoById($striveId);
        if ($striveInfoById) {
            [
                $eventId, 
                $lang, 
                $channelId, 
                $retry, 
                $bucketId, 
                $bucketName, 
                $pagination
            ] = $this->EventRepo->getEventInfo(
                $striveInfoById->event, 
                $striveInfoById->channel, 
                'read'
            );
            $channelName = $striveInfoById->channel;
            $event = $striveInfoById->event;
            $bucketId = $striveInfoById->group_id;
        }
        return [$eventId, $bucketId, $pagination, $channelName, $eventName, $lang];
    }
}

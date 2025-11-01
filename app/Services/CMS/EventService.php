<?php

namespace App\Services\CMS;

use App\Entity\EventResponseEntityForCMS;
use App\Repositories\{QuestionRepo, PolicyRepo, EventRepo};
use App\Services\Contracts\CMS\EventServiceInterface;
use Illuminate\Database\Eloquent\Model;

class EventService implements EventServiceInterface
{

    public function __construct(
        private EventRepo $eventRepo,
        private readonly QuestionRepo $questionRepo,
        private readonly PolicyRepo $PolicyRepo
    ) {
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $data = $this->eventRepo->getEvents();
        return $data;
    }

    /**
     * @param int $eventId
     *
     * @return array
     */
    public function getEventById(int $eventId): array
    {
        $data = $this->response($this->eventRepo->getEventById($eventId));
        return $data;
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function store(array $request): bool
    {
        $storeData = $this->eventRepo->storeEvent($request);
        if ($storeData) {
            return true;
        }
        return false;
    }

    /**
     * @param array $request
     * @param int $eventId
     *
     * @return bool
     */
    public function update(array $request, int $eventId): bool
    {
        $fillableData = $this->fillableData($request);

        $updateData = $this->eventRepo->updateEventById("id", $eventId, $fillableData);
        if ($updateData) {
            return true;
        }
        return false;
    }

    /**
     * @param array $request
     *
     * @return array
     */
    private function fillableData(array $request): array{
        $data = [];
        $fillable = ['name', 'id', 'bucket_id', 'channel_id', 'client_id', 'status'];
        foreach($request as $key => $value){
            if(in_array($key, $fillable)){
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * @param int $eventId
     * @param array $request
     *
     * @return bool
     */
    public function delete(int $eventId, array $request): bool
    {
        return $this->eventRepo->deleteEventById($eventId, $request);
    }

    /**
     * @param string $channelTag
     *
     * @return array
     */
    public function getEventsByChannelTag(string $channelTag): array
    {
        $data = $this->eventRepo->getEventInfoByChannelTag($channelTag);
        return $data;
    }

    /**
     * @param int $eventId
     *
     * @return array
     */
    public function getRulesByEventId(int $eventId): array
    {
        $data = $this->PolicyRepo->rulesForCMS($eventId);
        return $data;
    }

    /**
     * @param int $eventId
     *
     * @return array
     */
    public function getQuestionsByEventId(int $eventId): array
    {
        $event = $this->eventRepo->getEventById($eventId);
        return $event ? $this->questionRepo->getQuestionListForCMSByBucketId($event->bucket_id) : [];
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function attachRule(array $request): bool
    {
        $checkTaggedRuleExistForEvent = $this->PolicyRepo->singleRuleFetchByGivenParam(
            [
                "event_id" => $request['event_id'],
                "func" => $request['rule'],
            ]);

        if($checkTaggedRuleExistForEvent) {
            if(isset($request['args'])) {
                $rule['args'] = json_encode($request['args']);
            } else {
                $rule['args'] = $checkTaggedRuleExistForEvent['args'];
            }
            return $this->PolicyRepo->updateRuleById("id", $checkTaggedRuleExistForEvent->id,
            ["args" => $rule['args'], "user_name" => $request['user_name'], "enabled" => 1]
        );
        }
        $fetchSelectionRule = $this->PolicyRepo->ruleGetByFuncNameForAttach($request['rule']);
        if(!empty($fetchSelectionRule)){
            $rule = $fetchSelectionRule[0];
            $rule['event_id'] = $request['event_id'];
            $rule['user_name'] = $request['user_name'];
            $rule['enabled'] = 1;
            $rule['args'] = !isset($request['args']) ? $rule['args'] : json_encode($request['args']);

            $attachRule = $this->PolicyRepo->storeRule($rule);
            if($attachRule){
                return true;
            }
        }
        return false;
    }

    /**
     * @param Model|null $event
     *
     * @return array
     */
    private function response(Model|null $event): array
    {
        $data = [];
        if ($event) {
            $data = (new EventResponseEntityForCMS())
                ->setEventType($event->type)
                ->setEventName($event->name)
                ->setBucketId($event->bucket_id)
                ->setContext($event->context)
                ->setStatus($event->status)
                ->setDescription($event->description)
                ->setChannel($event->channel)
                ->build();
        }

        return $data;
    }
}

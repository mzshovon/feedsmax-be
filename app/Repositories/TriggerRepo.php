<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\Trigger;
//use App\Proxy\CacheProxy as Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TriggerRepo
{
    const TRIGGER_CACHE_KEY = 'trigger_records';
    const IDENTIFIER = 'event';

    /**
     * @var Model
     */
    public Model $model;

    public function __construct(Trigger $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $event
     * @param string $channel
     * @param string $instance_type
     *
     * @return array
     */
    public function getTriggerInfo(string $event, string $channel, string $instance_type = 'write'): array
    {
        //Cache check starts from here
        try{
            if (Cache::has(self::TRIGGER_CACHE_KEY)) {
                $data = Cache::get(self::TRIGGER_CACHE_KEY);
                if (!is_null($data)) {
                    return $this->decodeTriggerDataToMatch($data, $event, $channel);
                }
            } else {
                $ttl = config('cache.ttl.trigger');
                $trigger = $this->model::on('mysql::' . $instance_type)
                    ->with(['channel', 'group'])
                    ->where("status", 1)
                    ->get()
                    ->toArray();
                $event_channel_data = $this->hashmapEventChannel($trigger);
                if (!empty($event_channel_data)) {
                    Cache::put(self::TRIGGER_CACHE_KEY, json_encode($event_channel_data), $ttl);
                }
                return $this->decodeTriggerDataToMatch($event_channel_data, $event, $channel);
            }
        } catch (\Exception $ex){
            $trigger = $this->model::on('mysql::' . $instance_type)
                ->with(['channel', 'group'])
                ->get()
                ->toArray();
            $event_channel_data = $this->hashmapEventChannel($trigger);
            return $this->decodeTriggerDataToMatch($event_channel_data, $event, $channel);
        }

    }

    /**
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return array
     */
    public function getEvents($instance_type = "read", array $selected_columns = ["*"]): array
    {
        $event = $this->model::on('mysql::' . $instance_type)
            ->select($selected_columns)
            ->with(['channel', 'group'])
            ->get()
            ->toArray();
        return $event;
    }

    /**
     * @param int $event_id
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model|null
     */
    public function getEventById(int $event_id, $instance_type = "read", array $selected_columns = ["*"]): Model|null
    {
        $event = $this->model::on('mysql::' . $instance_type)
            ->with(['channel', 'group'])
            ->select($selected_columns)
            ->where("id", $event_id)
            ->first();
        return $event;
    }

    /**
     * @param array $request
     *
     * @return Model
     */
    public function storeEvent(array $request): Model
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $store = $this->model::on('mysql::write')->create($request);
            event(new PopulateChangeLog(
                CrudEnum::Create->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                null,
                json_encode($request)
            ));
            return $store;
        } catch (\Exception $e) {
            $store = $this->model::on('mysql::write')->create($request);
            event(new PopulateChangeLog(
                CrudEnum::Create->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                null,
                json_encode($request)
            ));
            return $store;
        }
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function updateEventById(string $param, string $value, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where($param, $value)->first();
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $event = $this->model::on('mysql::write')->where($param, $value)->update($request);
            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $event;
        } catch (\Exception $e) {
            $event = $this->model::on('mysql::write')->where($param, $value)->update($request);
            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $event;
        }
    }

    /**
     * @param int $eventId
     * @param array $request
     *
     * @return bool
     */
    public function deleteEventById(int $eventId, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where("id", $eventId)->first();
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $event = $this->model::on('mysql::write')->where("id", $eventId)->delete();

            event(new PopulateChangeLog(
                CrudEnum::Delete->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                null
            ));
            return $event;
        } catch (\Exception $e) {
            $event = $this->model::on('mysql::write')->where("id", $eventId)->delete();

            event(new PopulateChangeLog(
                CrudEnum::Delete->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                null
            ));
            return $event;
        }
    }

    /**
     * @param array $request
     *
     * @return array
     */
    public function attachRule(int $triggerId, array $questionIds, $instance_type = "read"): array
    {
        $event = $this->model::on('mysql::'.$instance_type)->find($triggerId);
        $questionSyncOperation = $event->questionnaires()->sync($questionIds);
        return $questionSyncOperation;
    }

    public function getTriggerInfoByChannelTag(string $channelTag): array
    {
        return $this->model::with("channel")->whereHas('channel', function ($q) use ($channelTag) {
            $q->where('tag', $channelTag);
        })->get()->makeHidden('channel')->toArray();
    }

    /**
     * @param string|array $encodedTriggerData
     * @param string $event
     * @param string $channelTag
     * @return array
     */
    private function decodeTriggerDataToMatch(string|array $encodedTriggerData, string $event, string $channelTag): array
    {
        $decodedTriggerData = (is_array($encodedTriggerData)) ? $encodedTriggerData : json_decode($encodedTriggerData, true);
        if (array_key_exists($event, $decodedTriggerData)) {
            if (array_key_exists($channelTag, $decodedTriggerData[$event])) {
                return $decodedTriggerData[$event][$channelTag];
            }
        }
        return [null, null, null, null, null, null, null, null];
    }

    /**
     * @param array $event
     *
     * @return array
     */
    private function hashmapEventChannel(array $event): array
    {
        $data = [];
        $failedResult = [null, null, null, null, null, null, null];
        foreach ($event as $key => $value) {
            $data[$value['event']][$value['channel']['tag']] = ($value['channel']['status'] == 1) ? [
                                                                $value['id'],
                                                                $value['lang'],
                                                                $value['channel_id'],
                                                                $value['channel']['retry'],
                                                                $value['group_id'],
                                                                $value['next_group_id'] ?? null,
                                                                $value['group']['name'],
                                                                $value['channel']['num_of_questions']
                                                            ] : $failedResult;
        }
        return $data;
    }
}

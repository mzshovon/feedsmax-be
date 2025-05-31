<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\Event;
//use App\Proxy\CacheProxy as Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EventRepo
{
    const EVENT_CACHE_KEY = 'event_records';
    const IDENTIFIER = 'event';

    /**
     * @var Model
     */
    public Model $model;

    public function __construct(Event $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $eventName
     * @param string $channel
     * @param string $instance_type
     *
     * @return array
     */
    public function getEventInfo(
        string $eventName, 
        string $channel, 
        string $instance_type = 'read'
    ): array
    {
        //Cache check starts from here
        try{
            if (Cache::has(self::EVENT_CACHE_KEY)) {
                $data = Cache::get(self::EVENT_CACHE_KEY);
                if (!is_null($data)) {
                    return $this->decodeEventDataToMatch($data, $eventName, $channel);
                }
            } else {
                $ttl = config('cache.ttl.event');
                $eventData = $this->model::on('mysql::' . $instance_type)
                    ->with(['bucket', 'channel'])
                    ->where("status", 1)
                    ->get()
                    ->toArray();
                $eventChannelData = $this->hashmapEventChannel($eventData);
                if (!empty($eventChannelData)) {
                    Cache::put(self::EVENT_CACHE_KEY, json_encode($eventChannelData), $ttl);
                }
                return $this->decodeEventDataToMatch($eventChannelData, $eventName, $channel);
            }
        } catch (\Exception $ex){
            $eventData = $this->model::on('mysql::' . $instance_type)
                ->with(['channel', 'bucket'])
                ->get()
                ->toArray();
            $eventChannelData = $this->hashmapEventChannel($eventData);
            return $this->decodeEventDataToMatch($eventChannelData, $eventName, $channel);
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
            ->with(['channel', 'bucket'])
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
            ->with(['channel', 'bucket'])
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
    public function attachRule(int $eventId, array $questionIds, $instance_type = "read"): array
    {
        $event = $this->model::on('mysql::'.$instance_type)->find($eventId);
        $questionSyncOperation = $event->questionnaires()->sync($questionIds);
        return $questionSyncOperation;
    }

    public function getEventInfoByChannelTag(string $channelTag): array
    {
        return $this->model::with("channel")->whereHas('channel', function ($q) use ($channelTag) {
            $q->where('tag', $channelTag);
        })->get()->makeHidden('channel')->toArray();
    }

    /**
     * @param string|array $encodedEventData
     * @param string $eventName
     * @param string $channelTag
     * 
     * @return array
     */
    private function decodeEventDataToMatch(string|array $encodedEventData, string $eventName, string $channelTag): array
    {
        $decodedEventData = (is_array($encodedEventData)) ? $encodedEventData : json_decode($encodedEventData, true);
        
        if (array_key_exists($eventName, $decodedEventData)) {
            if (array_key_exists($channelTag, $decodedEventData[$eventName])) {
                return $decodedEventData[$eventName][$channelTag];
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
        foreach ($event as $value) {
            $data[$value['name']][$value['channel']['tag']] = ($value['channel']['status'] == 1) ? [
                                                                $value['id'],
                                                                $value['lang'],
                                                                $value['channel_id'],
                                                                $value['channel']['retry'],
                                                                $value['bucket_id'],
                                                                $value['bucket']['name'],
                                                                $value['channel']['pagination']
                                                            ] : $failedResult;
        }
        return $data;
    }
}

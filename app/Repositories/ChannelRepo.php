<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ChannelRepo
{
    public Model $model;

    const CHANNEL_CACHE_KEY = 'channel_records';
    const IDENTIFIER = 'channel';

    public function __construct(Channel $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return array
     */
    public function getChannels($instance_type = "read", array $selected_columns = ["*"]): array
    {
        $channel = $this->model::on('mysql::' . $instance_type)->with('themes')->select($selected_columns)->get()->toArray();
        return $channel;
    }

    /**
     * @param int $channel_id
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model
     */
    public function getChannelById(int $channel_id, $instance_type = "read", array $selected_columns = ["*"]): Model|null
    {
        $channel = $this->model::on('mysql::' . $instance_type)
            ->with('themes')
            ->select($selected_columns)
            ->where("id", $channel_id)
            ->first();
        return $channel;
    }

    /**
     * @param string $channelTag
     *
     * @return array
     */
    public function getInfoByChannelTag(string $channelTag, $instance_type = "read"): array|null
    {
        try {
            if (Cache::has(self::CHANNEL_CACHE_KEY)) {
                $data = Cache::get(self::CHANNEL_CACHE_KEY);
                if (!is_null($data))
                    return $this->decodeChannelDataToMatch($data, $channelTag);
            } else {
                $ttl = config('cache.ttl.channel');
                $channel = $this->model::on('mysql::' . $instance_type)
                    ->with('themes')
                    ->get()
                    ->toArray();
                $channelRecords = $this->hashmapChannelRecord($channel);
                if (!empty($channelRecords)) {
                    Cache::put(self::CHANNEL_CACHE_KEY, json_encode($channelRecords), $ttl);
                }
                return $this->decodeChannelDataToMatch($channelRecords, $channelTag);
            }
        } catch (\Exception $ex) {
            $ttl = config('cache.ttl.channel');
            $channel = $this->model::on('mysql::' . $instance_type)
                ->with('themes')
                ->get()
                ->toArray();
            $channelRecords = $this->hashmapChannelRecord($channel);
            return $this->decodeChannelDataToMatch($channelRecords, $channelTag);
        }

    }

    /**
     * @param array $request
     *
     * @return Model
     */
    public function storeChannel(array $request): Model
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
    public function updateChannelById(string $param, string $value, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where($param, $value)->first();
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $channel = $this->model::on('mysql::write')->where($param, $value)->update($request);

            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $channel;

        } catch (\Exception $e) {
            $channel = $this->model::on('mysql::write')->where($param, $value)->update($request);

            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $channel;
        }
    }

    /**
     * @param int $channelId
     * @param array $request
     *
     * @return bool
     */
    public function deleteChannelById(int $channelId, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where("id", $channelId)->first();
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $event = $this->model::on('mysql::write')->where("id", $channelId)->delete();

            event(new PopulateChangeLog(
                CrudEnum::Delete->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                null
            ));
            return $event;

        } catch (\Exception $e) {
            $event = $this->model::on('mysql::write')->where("id", $channelId)->delete();

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
     * @param string|array $encodedChannelData
     * @param string $channelTag
     *
     * @return array
     */
    private function decodeChannelDataToMatch(string|array $encodedChannelData, string $channelTag): array
    {
        $decodedChannelData = (is_array($encodedChannelData)) ? $encodedChannelData : json_decode($encodedChannelData, true);
        if (array_key_exists($channelTag, $decodedChannelData)) {
            return $decodedChannelData[$channelTag];
        }
        return [];
    }

    /**
     * @param array $event
     *
     * @return array
     */
    private function hashmapChannelRecord(array $event): array
    {
        $data = [];
        foreach ($event as $key => $value) {
            $data[$value['tag']] = $value;
        }
        return $data;
    }
}

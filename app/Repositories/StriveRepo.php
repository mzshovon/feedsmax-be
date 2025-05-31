<?php

namespace App\Repositories;

use App\Models\Strive;
use App\Services\LoggerService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StriveRepo
{
    public $model;
    const STRIVE_REFERENCE_CHANNEL_KEY = "strive_{reference}_{channel}_{trigger}";

    private $logStoreTTL = 15 * 24 * 60 * 60;


    public function __construct(Strive $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $reference
     * @param string $eventName
     * @param string $channelName
     *
     * @return string|null
     */
    public function getUUIDByReference(string $reference, string $eventName, string $channelName): string|null
    {
        return $this->model::whereReference($reference)->whereEvent($eventName)->whereChannel($channelName)->whereView(1)->first()->uuid ?? null;
    }

    /**
     * @param int $id
     *
     * @return Model|null
     */
    public function getStriveInfoById(int $id): Model|null
    {
        return $this->model::with("bucket")->whereId($id)->first() ?? null;
    }

    /**
     * @param string $reference
     * @param string $channel
     * @param int $event_id
     * @param bool $checkReject
     * @param string $instance_type
     *
     * @return Model
     */
    public function getLastStrive(string $reference, int $channel_id, int $event_id, bool $checkReject = false, string $instance_type = "read"): Model|null
    {
        try {
            //Cache check starts from here
            $get_key = str_replace(
                ['{reference}', '{channel}', '{trigger}'],
                [$reference, $channel_id, $event_id],
                self::STRIVE_REFERENCE_CHANNEL_KEY
            );
            if (Cache::has($get_key)) {
                $data = Cache::get($get_key);
                if (!is_null($data))
                    return $data;
                else
                    return null;
            } else {
                return $this->model::on('mysql::' . $instance_type)
                    ->with("bucket")
                    ->where([
                        'reference' => $reference,
                        'channel_id' => $channel_id,
                        'event_id' => $event_id
                    ])
                    ->when($checkReject, function($q) {
                        $q->latest('created_at');
                    })
                    ->when(!$checkReject, function($q) {
                        $q->latest('submitted_at');
                    })
                    ->first();
            }
        } catch (\Exception $ex) {
            $loggerService = app(LoggerService::class);
            $loggerService->exception($ex->getMessage());
            return $this->model::on('mysql::' . $instance_type)
                ->with("bucket")
                ->where([
                    'reference' => $reference,
                    'channel_id' => $channel_id,
                    'event_id' => $event_id
                ])
                ->when($checkReject, function($q) {
                    $q->latest('created_at');
                })
                ->when(!$checkReject, function($q) {
                    $q->latest('submitted_at');
                })
                ->first();
        }

    }

    /**
     * @param array $request
     *
     * @return Model
     * @throws Exception
     */
    public function save(array $request): Model
    {
        try {
            $strive = $this->model::on('mysql::write')->create($request);
            if(isset($request['event_id']) && isset($request['channel_id'])) {
                $this->storeStriveInfoInCache($request['reference'], $request['channel_id'], $request['channel_id'], $strive);
            }
            return $strive;

        } catch (\Exception $ex) {
            $loggerService = app(LoggerService::class);
            $loggerService->exception($ex->getMessage());
            throw new Exception($ex->getMessage());
        }

    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function update(string $param, string $value, array $request): bool
    {
        try {
            $strive = $this->model::on('mysql::write')->where($param, $value)->update($request);
            if($strive) {
                $striveModel = $this->model->where($param, $value)->first();
                $this->updateStriveInfoInCache($striveModel);
            }
            return $strive;
        } catch (\Exception $e) {
            $loggerService = app(LoggerService::class);
            $loggerService->exception($e->getMessage());
            throw new Exception("Strive update exception");
            // $strive = $this->model::on('mysql::write')->where($param, $value)->update($request);
            // return $strive;
        }

    }

    /**
     * @param string $reference
     * @param int $channel
     * @param int $trigger
     * @param Model $strive
     *
     * @return void
     */
    private function storeStriveInfoInCache(string $reference, int $channel, int $trigger, Model $strive) : void
    {
        $store_key = str_replace(
            ['{reference}', '{channel}', '{trigger}'],
            [$reference, $channel, $trigger],
            self::STRIVE_REFERENCE_CHANNEL_KEY
        );
        Cache::put($store_key, $strive, config('cache.ttl.strive') ?? $this->logStoreTTL);
    }

    /**
     * @param Model $strive
     *
     * @return void
     */
    private function updateStriveInfoInCache(Model $strive):void
    {
        $store_key = str_replace(
            ['{reference}', '{channel}', '{trigger}'],
            [$strive->reference, $strive->channel_id, $strive->event_id],
            self::STRIVE_REFERENCE_CHANNEL_KEY
        );
        Cache::put($store_key, $strive, config('cache.ttl.strive') ?? $this->logStoreTTL);
    }
}

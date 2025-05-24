<?php

namespace App\Repositories;

use App\Models\Attempt;
use App\Services\LoggerService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AttemptRepo
{
    public $model;
    const ATTEMPT_MSISDN_CHANNEL_KEY = "attempt_{msisdn}_{channel}_{trigger}";

    private $logStoreTTL = 15 * 24 * 60 * 60;


    public function __construct(Attempt $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $msisdn
     * @param string $eventName
     * @param string $channelName
     *
     * @return string|null
     */
    public function getUUIDByMsisdn(string $msisdn, string $eventName, string $channelName): string|null
    {
        return $this->model::whereMsisdn($msisdn)->whereEvent($eventName)->whereChannel($channelName)->whereView(1)->first()->uuid ?? null;
    }

    /**
     * @param int $id
     *
     * @return Model|null
     */
    public function getAttemptInfoById(int $id): Model|null
    {
        return $this->model::with("groups")->whereId($id)->first() ?? null;
    }

    /**
     * @param string $msisdn
     * @param string $channel
     * @param int $event_id
     * @param bool $checkReject
     * @param string $instance_type
     *
     * @return Model
     */
    public function getLastAttempt(string $msisdn, int $channel_id, int $event_id, bool $checkReject = false, string $instance_type = "read"): Model|null
    {
        try {
            //Cache check starts from here
            $get_key = str_replace(
                ['{msisdn}', '{channel}', '{trigger}'],
                [$msisdn, $channel_id, $event_id],
                self::ATTEMPT_MSISDN_CHANNEL_KEY
            );
            if (Cache::has($get_key)) {
                $data = Cache::get($get_key);
                if (!is_null($data))
                    return $data;
                else
                    return null;
            } else {
                return $this->model::on('mysql::' . $instance_type)
                    ->with("groups")
                    ->where([
                        'msisdn' => $msisdn,
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
                ->with("groups")
                ->where([
                    'msisdn' => $msisdn,
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
            $attempt = $this->model::on('mysql::write')->create($request);
            if(isset($request['event_id']) && isset($request['channel_id'])) {
                $this->storeAttemptInfoInCache($request['msisdn'], $request['channel_id'], $request['channel_id'], $attempt);
            }
            return $attempt;

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
            $attempt = $this->model::on('mysql::write')->where($param, $value)->update($request);
            if($attempt) {
                $attemptModel = $this->model->where($param, $value)->first();
                $this->updateAttemptInfoInCache($attemptModel);
            }
            return $attempt;
        } catch (\Exception $e) {
            $loggerService = app(LoggerService::class);
            $loggerService->exception($e->getMessage());
            throw new Exception("Attempt update exception");
            // $attempt = $this->model::on('mysql::write')->where($param, $value)->update($request);
            // return $attempt;
        }

    }

    /**
     * @param string $msisdn
     * @param int $channel
     * @param int $trigger
     * @param Model $attempt
     *
     * @return void
     */
    private function storeAttemptInfoInCache(string $msisdn, int $channel, int $trigger, Model $attempt) : void
    {
        $store_key = str_replace(
            ['{msisdn}', '{channel}', '{trigger}'],
            [$msisdn, $channel, $trigger],
            self::ATTEMPT_MSISDN_CHANNEL_KEY
        );
        Cache::put($store_key, $attempt, config('cache.ttl.attempt') ?? $this->logStoreTTL);
    }

    /**
     * @param Model $attempt
     *
     * @return void
     */
    private function updateAttemptInfoInCache(Model $attempt):void
    {
        $store_key = str_replace(
            ['{msisdn}', '{channel}', '{trigger}'],
            [$attempt->msisdn, $attempt->channel_id, $attempt->event_id],
            self::ATTEMPT_MSISDN_CHANNEL_KEY
        );
        Cache::put($store_key, $attempt, config('cache.ttl.attempt') ?? $this->logStoreTTL);
    }
}

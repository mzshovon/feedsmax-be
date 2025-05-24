<?php

namespace App\Repositories;

use App\Models\Hit;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HitRepo
{
    const HIT_MSISDN_CHANNEL_KEY = "hit_{msisdn}_{channel}_{trigger}";
    const TRIGGER_CACHE_KEY = 'trigger_records';

    private $logStoreTTL = 15 * 24 * 60 * 60;

    public Model $model;

    public function __construct(Hit $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $msisdn
     * @param int $trigger_id
     * @param int $channel_id
     * @param int|null $attempt_id
     *
     * @return Model
     * @throws Exception
     */
    public function save(string $msisdn, int $trigger_id, int $channel_id, int|null $attempt_id): Model
    {
        try {
            $data = [
                "msisdn" => $msisdn,
                "trigger_id" => $trigger_id,
                "channel_id" => $channel_id,
                "attempt_id" => $attempt_id,
                // 'attempt_date' => is_null($attempt_id) ? null : Carbon::now()
            ];
            $hit = $this->model::on('mysql::write')->create($data);
            // if (!is_null($attempt_id)) {
            //     $this->storeHitInfoInCache($msisdn, $channel_id, $trigger_id, $hit);
            // }
            return $hit;
        } catch (\Exception $ex) {
            throw new Exception("Hit Store Failed!");
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
            $hit = $this->model::on('mysql::write')->where($param, $value)->update($request);
            // if($hit) {
            //     $hitModel = $this->model->where($param, $value)->first();
            //     $this->updateHitInfoInCache($hitModel);
            // }
            return $hit;
        } catch (\Exception $ex) {
            throw new Exception("Hit Update Failed!");
        }

    }

    /**
     * @param string $msisdn
     * @param string $channel
     * @param int $trigger_id
     * @param bool $checkReject
     * @param string $instance_type
     *
     * @return Model|null
     */
    public function getLastAttemptView(string $msisdn, string $channel, int $trigger_id, bool $checkReject = false, string $instance_type = "read"): Model|null
    {
        try {
            //Cache check starts from here
            $get_key = str_replace(
                ['{msisdn}', '{channel}', '{trigger}'],
                [$msisdn, $channel, $trigger_id],
                self::HIT_MSISDN_CHANNEL_KEY
            );
            if (Cache::has($get_key)) {
                $data = Cache::get($get_key);
                if (!is_null($data))
                    return $data;
                else
                    return null;
            } else {
                return $this->model::on('mysql::' . $instance_type)
                    ->where([
                        'msisdn' => $msisdn,
                        'channel_id' => $channel,
                        'trigger_id' => $trigger_id
                    ])
                    ->whereNotNull('attempt_id')
                    ->when($checkReject, function($q) {
                        $q->latest('created_at');
                    })
                    ->when(!$checkReject, function($q) {
                        $q->latest('attempt_date');
                    })
                    ->first();
            }
        } catch (\Exception $ex) {
            $loggerService = app(LoggerService::class);
            $loggerService->exception($ex->getMessage());
            return $this->model::on('mysql::' . $instance_type)
                ->where([
                    'msisdn' => $msisdn,
                    'channel_id' => $channel,
                    'trigger_id' => $trigger_id
                ])
                ->whereNotNull('attempt_id')
                ->when($checkReject, function($q) {
                    $q->latest('created_at');
                })
                ->when(!$checkReject, function($q) {
                    $q->latest('attempt_date');
                })
                ->first();
        }

    }

    private function storeHitInfoInCache(string $msisdn, int $channel, int $trigger, Model $hit)
    {
        $store_key = str_replace(
            ['{msisdn}', '{channel}', '{trigger}'],
            [$msisdn, $channel, $trigger],
            self::HIT_MSISDN_CHANNEL_KEY
        );
        return Cache::put($store_key, $hit, config('cache.ttl.hit') ?? $this->logStoreTTL);
    }

    /**
     * @param Model $hit
     *
     * @return void
     */
    private function updateHitInfoInCache(Model $hit):void
    {
        $store_key = str_replace(
            ['{msisdn}', '{channel}', '{trigger}'],
            [$hit->msisdn, $hit->channel_id, $hit->trigger_id],
            self::HIT_MSISDN_CHANNEL_KEY
        );
        Cache::put($store_key, $hit, config('cache.ttl.hit') ?? $this->logStoreTTL);
    }
}

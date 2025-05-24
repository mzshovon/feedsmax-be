<?php

namespace App\Repositories;

use App\Models\SurveyQuota;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SurveyQuotaRepo
{
    public Model $model;

    protected string $cache_key_for_quota = "{quota_type}_wise_quota_for_{quota_value}_exceed";

    public function __construct(SurveyQuota $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $quota_type
     * @param string $quota_value
     *
     * @return bool
     */
    public function quotaCheck(string $quota_type, string $quota_value) : bool
    {
        $cache_key = str_replace(
            ['{quota_type}', '{quota_value}'],
            [$quota_type, $quota_value],
            $this->cache_key_for_quota
        );
        try {
            if(Cache::has($cache_key) && Cache::get($cache_key)) {
                return false;
            }
            // Dynamically create function to be invoked
            $func_name = "{$quota_type}QuotaCheck";
            return $this->$func_name($quota_type, $quota_value);

        } catch (\Illuminate\Database\QueryException $qe) {
            Cache::put($cache_key, true, config("cache.ttl.{$this->model->getTable()}"));
            return false;
        } catch (\Exception $e) {
            throw new Exception("Survey Quota Exception");
        }
    }

    /**
     * @param string $quota_type
     * @param string $quota_value
     * @param string $instance_type
     *
     * @return bool
     */
    private function sessionQuotaCheck(string $quota_type, string $quota_value, string $instance_type = "read") : bool
    {
        return $this->model::on('mysql::' . $instance_type)
                ->where("type", $quota_type)
                ->where("param", $quota_value)
                ->decrement("count");
    }

    /**
     * @param string $type
     * @param string $param
     * @param string $instance_type
     *
     * @return bool
     */
    private function locationQuotaCheck(string $type, string $param, string $instance_type = "read") : bool
    {
        return false;
    }
}

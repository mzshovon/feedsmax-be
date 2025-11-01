<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PolicyRepo
{
    CONST POLICIES_CACHE_KEY = 'policies_for_event_id_';
    CONST POLICIES_TTL = 60 * 60;
    const IDENTIFIER = 'policy';

    /**
     * @var Model
     */
    public Model $model;

    public function __construct(Policy $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $event_id
     * @param string $instance_type
     * @return array
     */
    public function policies(int $event_id, string $instance_type = 'write'): array
    {
        try {
            //Cache check starts from here
            if(Cache::has(self::POLICIES_CACHE_KEY.$event_id)){
                $data = Cache::get(self::POLICIES_CACHE_KEY.$event_id);
                if(!is_null($data))
                    return json_decode($data, true);
            } else{
                $ttl = config('cache.ttl.policies') ?? self::POLICIES_TTL;
                $policies = $this->model::on('mysql::' . $instance_type)
                    ->select(['func','args'])
                    ->where([
                        'event_id' => $event_id,
                        'enabled' => 1
                    ])->get()->toArray();
                if(!empty($policies)){
                    Cache::put(self::POLICIES_CACHE_KEY.$event_id, json_encode($policies), $ttl);
                }
                return $policies;
            }
        } catch (\Exception $ex) {
            $policies = $this->model::on('mysql::' . $instance_type)
            ->select(['func','args'])
            ->where([
                'event_id' => $event_id,
                'enabled' => 1
            ])->get()->toArray();
            return $policies;
        }

    }


    /**
     * @param int $event_id
     * @param string $instance_type
     * @return array
     */
    public function policiesForCMS(int $event_id, string $instance_type = 'read'): array{
        $policies = $this->model::on('mysql::' . $instance_type)
            ->where([
                'event_id' => $event_id,
            ])->get()->toArray();
        return $policies;
    }

    /**
     * @param string|null|null $funcName
     * @param array $selected_columns
     * @param string $instance_type
     * 
     * @return array
     */
    public function getPolicies(
        array $selected_columns = ["*"],
        string|null $funcName = null,
        string $instance_type = 'read'
        ): array{
        $policy = $this->model::on('mysql::' . $instance_type)
            ->when($funcName, function($q) use ($funcName){
                $q->where("func", $funcName);
            })
            ->get($selected_columns)
            ->toArray();
        return $policy;
    }

    /**
     * @param string|null $funcName
     * @param string $instance_type
     *
     * @return null
     */
    public function singlepolicyFetchByGivenParam(array $compareColumnValues, array $selected_columns = ["*"], string $instance_type = 'read'): null|Model
    {
        $query = $this->model::on('mysql::' . $instance_type);
        foreach ($compareColumnValues as $column => $value) {
            $query->where($column, $value);
        }
        $policy = $query->first();
        return $policy;
    }

        /**
     * @param int $policy_id
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model
     */
    public function getPolicyById(
        int $policy_id, 
        $instance_type = "read", 
        array $selected_columns = ["*"]
        ): Model|null
    {
        $channel = $this->model::on('mysql::' . $instance_type)
            ->select($selected_columns)
            ->where("id", $policy_id)
            ->first();
        return $channel;
    }

    /**
     * @param array $request
     *
     * @return Model
     */
    public function storePolicy(array $request): Model
    {
        // $userName = $request['user_name'] ?? null;
        // unset($request['user_name']);
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $store = $this->model::on('mysql::write')->create($request);

            // event(new PopulateChangeLog(
            //     CrudEnum::Create->value. "_" .self::IDENTIFIER,
            //     $this->model->getTable(),
            //     $userName,
            //     null,
            //     json_encode($request)
            // ));
            return $store;
        } catch (\Exception $e) {
            $store = $this->model::on('mysql::write')->create($request);

            // event(new PopulateChangeLog(
            //     CrudEnum::Create->value. "_" .self::IDENTIFIER,
            //     $this->model->getTable(),
            //     $userName,
            //     null,
            //     json_encode($request)
            // ));
            return $store;
        }
    }

    /**
     * @param string $param
     * @param string $value
     * @param array $request
     *
     * @return bool
     */
    public function updatePolicyById(string $param, string $value, array $request): bool
    {
        // $userName = $request['user_name'] ?? null;
        // unset($request['user_name']);
        // $existing = $this->model::on('mysql::read')->where($param, $value)->first();
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $policy = $this->model::on('mysql::write')->where($param, $value)->update($request);

            // event(new PopulateChangeLog(
            //     CrudEnum::Update->value. "_" .self::IDENTIFIER,
            //     $this->model->getTable(),
            //     $userName,
            //     json_encode($existing->attributesToArray()),
            //     json_encode($request)
            // ));
            return $policy;
        } catch (\Exception $e) {
            $policy = $this->model::on('mysql::write')->where($param, $value)->update($request);

            // event(new PopulateChangeLog(
            //     CrudEnum::Update->value. "_" .self::IDENTIFIER,
            //     $this->model->getTable(),
            //     $userName,
            //     json_encode($existing->attributesToArray()),
            //     json_encode($request)
            // ));
            return $policy;
        }
    }

    /**
     * @param int $policyId
     * @param array $request
     *
     * @return bool
     */
    public function deletePolicyById(int $policyId,array $request): bool
    {
        // $userName = $request['user_name'] ?? null;
        // unset($request['user_name']);
        // $existing = $this->model::on('mysql::read')->where("id", $policyId)->first();
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $event = $this->model::on('mysql::write')->where("id", $policyId)->delete();

            // event(new PopulateChangeLog(
            //     CrudEnum::Delete->value. "_" .self::IDENTIFIER,
            //     $this->model->getTable(),
            //     $userName,
            //     json_encode($existing->attributesToArray()),
            //     null
            // ));
            return $event;
        } catch (\Exception $e) {
            $event = $this->model::on('mysql::write')->where("id", $policyId)->delete();

            // event(new PopulateChangeLog(
            //     CrudEnum::Delete->value. "_" .self::IDENTIFIER,
            //     $this->model->getTable(),
            //     $userName,
            //     json_encode($existing->attributesToArray()),
            //     null
            // ));
            return $event;
        }
    }
}

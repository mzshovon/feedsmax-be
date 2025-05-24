<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\Rule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RulesRepo
{
    CONST RULES_CACHE_KEY = 'rules_for_trigger_id_';
    CONST RULES_TTL = 60 * 60;
    const IDENTIFIER = 'rule';

    /**
     * @var Model
     */
    public Model $model;

    public function __construct(Rule $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $trigger_id
     * @param string $instance_type
     * @return array
     */
    public function rules(int $trigger_id, string $instance_type = 'write'): array{
        try {
            //Cache check starts from here
            if(Cache::has(self::RULES_CACHE_KEY.$trigger_id)){
                $data = Cache::get(self::RULES_CACHE_KEY.$trigger_id);
                if(!is_null($data))
                    return json_decode($data, true);
            }else{
                $ttl = config('cache.ttl.rules') ?? self::RULES_TTL;
                $rules = $this->model::on('mysql::' . $instance_type)
                    ->select(['func','args'])
                    ->where([
                        'trigger_id' => $trigger_id,
                        'enabled' => 1
                    ])->get()->toArray();
                if(!empty($rules)){
                    Cache::put(self::RULES_CACHE_KEY.$trigger_id, json_encode($rules), $ttl);
                }
                return $rules;
            }
        } catch (\Exception $ex) {
            $rules = $this->model::on('mysql::' . $instance_type)
            ->select(['func','args'])
            ->where([
                'trigger_id' => $trigger_id,
                'enabled' => 1
            ])->get()->toArray();
            return $rules;
        }

    }


    /**
     * @param int $trigger_id
     * @param string $instance_type
     * @return array
     */
    public function rulesForCMS(int $trigger_id, string $instance_type = 'read'): array{
        $rules = $this->model::on('mysql::' . $instance_type)
            ->where([
                'trigger_id' => $trigger_id,
            ])->get()->toArray();
        return $rules;
    }

    /**
     * @param string|null $funcName
     * @param string $instance_type
     *
     * @return array
     */
    public function ruleGetByFuncNameForAttach(string|null $funcName = null, array $selected_columns = ["*"], string $instance_type = 'read'): array{
        $rule = $this->model::on('mysql::' . $instance_type)
            ->where([
                // 'enabled' => 0,
                'trigger_id' => 0,
            ])
            ->when($funcName, function($q) use ($funcName){
                $q->where("func", $funcName);
            })
            ->get($selected_columns)
            ->toArray();
        return $rule;
    }

    /**
     * @param string|null $funcName
     * @param string $instance_type
     *
     * @return null
     */
    public function singleRuleFetchByGivenParam(array $compareColumnValues, array $selected_columns = ["*"], string $instance_type = 'read'): null|Model
    {
        $query = $this->model::on('mysql::' . $instance_type);
        foreach ($compareColumnValues as $column => $value) {
            $query->where($column, $value);
        }
        $rule = $query->first();
        return $rule;
    }

    /**
     * @param array $request
     *
     * @return Model
     */
    public function storeRule(array $request): Model
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
     * @param string $param
     * @param string $value
     * @param array $request
     *
     * @return bool
     */
    public function updateRuleById(string $param, string $value, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where($param, $value)->first();
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $rule = $this->model::on('mysql::write')->where($param, $value)->update($request);

            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $rule;
        } catch (\Exception $e) {
            $rule = $this->model::on('mysql::write')->where($param, $value)->update($request);

            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $rule;
        }
    }

    /**
     * @param int $ruleId
     * @param array $request
     *
     * @return bool
     */
    public function deleteRuleById(int $ruleId,array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where("id", $ruleId)->first();
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $event = $this->model::on('mysql::write')->where("id", $ruleId)->delete();

            event(new PopulateChangeLog(
                CrudEnum::Delete->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                null
            ));
            return $event;
        } catch (\Exception $e) {
            $event = $this->model::on('mysql::write')->where("id", $ruleId)->delete();

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
}

<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\Bucket;
use App\Models\Group;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GroupRepo
{
    public Model $model;

    const GROUP_CACHE_KEY = 'group_records_for_';
    const QUESTION_TABLE_NAME = 'questionnaires';
    const IDENTIFIER = 'group';

    public function __construct(Bucket $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return array
     */
    public function getGroups($instance_type = "read", array $selected_columns = ["*"]): array
    {
        $group = $this->model::on('mysql::' . $instance_type)->select($selected_columns)->get()->toArray();
        return $group;
    }

    /**
     * @param int $group_id
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model
     */
    public function getBucketById(int $group_id, $instance_type = "read", array $selected_columns = ["*"]): Model|null
    {
        $cacheKey = self::GROUP_CACHE_KEY . $group_id;
        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
            if (!is_null($data))
                return $data;
        } else {
            $ttl = config('cache.ttl.groups');
            $group = $this->model::on('mysql::' . $instance_type)
                ->select($selected_columns)
                ->where("id", $group_id)
                ->first();
            if ($group) {
                Cache::put($cacheKey, $group, $ttl);
            }
            return $group;
        }

    }

    /**
     * @param array $request
     *
     * @return Model
     */
    public function storeGroup(array $request): Model
    {
        try {
            $userName = $request['user_name'] ?? null;
            unset($request['user_name']);
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
        } catch (\Exception $th) {
            throw new Exception("Group Store Failed!");
        }
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function updateGroupById(string $param, string $value, array $request): bool
    {
        try {
            $userName = $request['user_name'] ?? null;
            unset($request['user_name']);

            deleteCacheDataByTableName($this->model->getTable());
            $existing = $this->model::on('mysql::read')->where($param, $value)->first();
            $group = $this->model::on('mysql::write')->where($param, $value)->update($request);

            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $group;
        } catch (\Exception $ex) {
            throw new Exception("Group Update Failed!");
        }

    }

    /**
     * @param int $groupId
     * @param array $request
     *
     * @return bool
     */
    public function deleteGroupById(int $groupId, array $request): bool
    {
        try {
            $userName = $request['user_name'] ?? null;
            unset($request['user_name']);
            deleteCacheDataByTableName($this->model->getTable());
            $existing = $this->model::on('mysql::read')->where("id", $groupId)->first();
            $group = $this->model::on('mysql::write')->where("id", $groupId)->delete();

            event(new PopulateChangeLog(
                CrudEnum::Delete->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                null
            ));
            return $group;
        } catch (\Exception $th) {
            throw new Exception("Group Delete Failed!");
        }

    }

    /**
     * @param int $groupId
     * @param array $questionIds
     * @param string $userName
     * @param string $instance_type
     *
     * @return array
     */
    public function attachQuestions(int $groupId, array $questionIds, string $userName, $instance_type = "read"): array
    {
        $group = $this->model::on('mysql::'.$instance_type)->find($groupId);
        $questionSyncOperation = $group->questionnaires()->sync($questionIds);
        deleteCacheDataByTableName(self::QUESTION_TABLE_NAME);

        event(new PopulateChangeLog(
            CrudEnum::Update->value. "_" .self::IDENTIFIER. "_attach_questions",
            $this->model->getTable(),
            $userName,
            json_encode($group->questionnaires->pluck("id")->toArray()),
            null
        ));
        return $questionSyncOperation;
    }
}

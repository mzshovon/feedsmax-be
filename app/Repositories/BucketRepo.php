<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\Bucket;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BucketRepo
{
    public Model $model;

    const BUCKET_CACHE_KEY = 'bucket_records_for_';
    const QUESTION_TABLE_NAME = 'questionnaires';
    const IDENTIFIER = 'bucket';

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
    public function getBuckets($instance_type = "read", array $selected_columns = ["*"]): array
    {
        $bucket = $this->model::on('mysql::' . $instance_type)->select($selected_columns)->get()->toArray();
        return $bucket;
    }

    /**
     * @param int $bucket_id
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model
     */
    public function getBucketById(int $bucket_id, $instance_type = "read", array $selected_columns = ["*"]): Model|null
    {
        $cacheKey = self::BUCKET_CACHE_KEY . $bucket_id;
        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
            if (!is_null($data))
                return $data;
        } else {
            $ttl = config('cache.ttl.buckets');
            $bucket = $this->model::on('mysql::' . $instance_type)
                ->select($selected_columns)
                ->where("id", $bucket_id)
                ->first();
            if ($bucket) {
                Cache::put($cacheKey, $bucket, $ttl);
            }
            return $bucket;
        }

    }

    /**
     * @param array $request
     *
     * @return Model
     */
    public function storeBucket(array $request): Model
    {
        try {
            $userName = $request['user_name'] ?? null;
            unset($request['user_name']);
            // deleteCacheDataByTableName($this->model->getTable());
            $store = $this->model::on('mysql::write')->create($request);

            // event(new PopulateChangeLog(
            //     CrudEnum::Create->value. "_" .self::IDENTIFIER,
            //     $this->model->getTable(),
            //     $userName,
            //     null,
            //     json_encode($request)
            // ));
            return $store;
        } catch (\Exception $th) {
            throw new Exception("Bucket Store Failed!");
        }
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function updateBucketById(string $param, string $value, array $request): bool
    {
        try {
            $userName = $request['user_name'] ?? null;
            unset($request['user_name']);

            // deleteCacheDataByTableName($this->model->getTable());
            $existing = $this->model::on('mysql::read')->where($param, $value)->first();
            $bucket = $this->model::on('mysql::write')->where($param, $value)->update($request);

            // event(new PopulateChangeLog(
            //     CrudEnum::Update->value. "_" .self::IDENTIFIER,
            //     $this->model->getTable(),
            //     $userName,
            //     json_encode($existing->attributesToArray()),
            //     json_encode($request)
            // ));
            return $bucket;
        } catch (\Exception $ex) {
            throw new Exception("Bucket Update Failed!");
        }

    }

    /**
     * @param int $bucketId
     * @param array $request
     *
     * @return bool
     */
    public function deleteBucketById(int $bucketId, array $request): bool
    {
        try {
            $userName = $request['user_name'] ?? null;
            unset($request['user_name']);
            // deleteCacheDataByTableName($this->model->getTable());
            $existing = $this->model::on('mysql::read')->where("id", $bucketId)->first();
            $bucket = $this->model::on('mysql::write')->where("id", $bucketId)->delete();

            // event(new PopulateChangeLog(
            //     CrudEnum::Delete->value. "_" .self::IDENTIFIER,
            //     $this->model->getTable(),
            //     $userName,
            //     json_encode($existing->attributesToArray()),
            //     null
            // ));
            return $bucket;
        } catch (\Exception $th) {
            throw new Exception("Bucket Delete Failed!");
        }

    }

    /**
     * @param int $bucketId
     * @param array $questionIds
     * @param string $userName
     * @param string $instance_type
     *
     * @return array
     */
    public function attachQuestions(int $bucketId, array $questionIds, string $userName, $instance_type = "read"): array
    {
        $bucket = $this->model::on('mysql::'.$instance_type)->find($bucketId);
        $questionSyncOperation = $bucket->questionnaires()->sync($questionIds);
        // deleteCacheDataByTableName(self::QUESTION_TABLE_NAME);

        // event(new PopulateChangeLog(
        //     CrudEnum::Update->value. "_" .self::IDENTIFIER. "_attach_questions",
        //     $this->model->getTable(),
        //     $userName,
        //     json_encode($bucket->questionnaires->pluck("id")->toArray()),
        //     null
        // ));
        return $questionSyncOperation;
    }
}

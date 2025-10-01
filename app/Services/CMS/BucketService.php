<?php

namespace App\Services\CMS;

use App\Entity\BucketResponseEntity;
use App\Repositories\BucketRepo;
use App\Repositories\QuestionRepo;
use App\Services\Contracts\CMS\BucketServiceInterface;
use Illuminate\Database\Eloquent\Model;

class BucketService implements BucketServiceInterface
{
    // Your service class code here
	public function __construct(
        private readonly BucketRepo $bucketRepo,
        private readonly QuestionRepo $questionRepo
    ){}

	/**
	 * @param string|null $columns
	 * 
	 * @return array
	 */
	public function get(?string $columns = null): array
    {
        $columns = !empty($columns) ? explode(",", $columns) : ["*"];
        $data = $this->bucketRepo->getBuckets("read", $columns);
        return $data;
    }

	public function getBucketById(int $id)
    {
        $data = $this->response($this->bucketRepo->getBucketById($id));
        return $data;
    }

	/**
	 * @param array $request
	 *
	 * @return bool
	 */
	public function store(array $request) : bool
    {
        $storeData = $this->bucketRepo->storeBucket($request);
        if ($storeData) {
            return true;
        }
        return false;
    }

	/**
	 * @param array $request
	 * @param int $id
	 *
	 * @return bool
	 */
	public function update(array $request, int $id) : bool
    {
        $fillableData = $this->fillableData($request);

        $updateData = $this->bucketRepo->updateBucketById("id", $id, $fillableData);
        if ($updateData) {
            return true;
        }
        return false;
    }

    /**
     * @param array $request
     *
     * @return array
     */
    private function fillableData(array $request): array{
        $data = [];
        $fillable = ['user_name', 'id', 'name', 'description', 'status', 'quota', 'served'];
        foreach($request as $key => $value){
            if(in_array($key, $fillable)){
                $data[$key] = $value;
            }
        }
        return $data;
    }

	/**
	 * @param int $id
	 * @param array $request
	 *
	 * @return bool
	 */
	public function delete(int $id, array $request) : bool
    {
        return $this->bucketRepo->deleteBucketById($id, $request);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getQuestionsByBucketId(int $id): array
    {
        return $this->questionRepo->getQuestionListForCMSByBucketId($id);
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function attachQuestions(array $request): bool
    {
        $syncChanges = $this->bucketRepo->attachQuestions($request['bucket_id'], $request['questions'], $request['user_name'], "write");
        if(!empty($syncChanges)) {
            return true;
        }
        return false;
    }

    /**
     * @param Model|null $bucket
     *
     * @return array
     */
    private function response(Model|null $bucket): array
    {
        $data = [];
        if ($bucket) {
            $data = (new BucketResponseEntity())
                ->setName($bucket->name)
                ->setDescription($bucket->description)
                ->setStatus($bucket->status)
                ->setQuota($bucket->quota ?? null)
                ->setServed($bucket->served ?? null)
                // ->npsQuestionId($bucket->nps_ques_id ?? null)
                // ->promoterRange($bucket->promoter_range ?? null)
                ->build();
        }

        return $data;
    }

}
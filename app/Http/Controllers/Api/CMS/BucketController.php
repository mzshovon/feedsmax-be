<?php

namespace App\Http\Controllers\Api\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteRequest;
use App\Http\Requests\BucketStoreRequest;
use App\Http\Requests\BucketUpdateRequest;
use App\Http\Requests\QuestionsAttachToBucketRequest;
use App\Services\Contracts\CMS\BucketServiceInterface;
use App\Traits\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class BucketController extends Controller
{
    use ApiResponse;

    protected $repo;

    public function __construct(BucketServiceInterface $repo)
    {
        $this->repo = $repo;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $columns = request()->get('columns') ?? null;
            $data = $this->repo->get($columns);
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(BucketStoreRequest $request)
    {
        try {
            $data = $this->repo->store($request->all());
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = $this->repo->getBucketById($id);
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BucketUpdateRequest $request, string $id)
    {
        try {
            $data = $this->repo->update($request->all(), $id);
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteRequest $request, int $id)
    {
        try {
            $data = $this->repo->delete($id, $request->all());
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }

    public function getQuestionsByBucket($bucketId)
    {
        try {
            $data = $this->repo->getQuestionsByBucketId($bucketId);
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }

    public function attachQuestionsToBucket(QuestionsAttachToBucketRequest $request)
    {
        try {
            $data = $this->repo->attachQuestions($request->all());
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }
}

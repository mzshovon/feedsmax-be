<?php

namespace App\Http\Controllers\Api\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\{DeleteRequest, PolicyStoreRequest, PolicyUpdateRequest};
use App\Services\Contracts\CMS\PolicyServiceInterface;
use App\Traits\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class PolicyController extends Controller
{
    use ApiResponse;

    private $repo;

    public function __construct(PolicyServiceInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        try {
            $columns = request()->get("columns") ?? null;
            $data = $this->repo->getPolicyList($columns);
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(PolicyStoreRequest $request)
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
    public function show(int $id)
    {
        try {
            $data = $this->repo->getPolicyById($id);
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PolicyUpdateRequest $request)
    {
        try {
            $data = $this->repo->update($request->all());
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
}

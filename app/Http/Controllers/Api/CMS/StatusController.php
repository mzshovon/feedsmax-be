<?php

namespace App\Http\Controllers\Api\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteRequest;
use App\Http\Requests\StatusStoreRequest;
use App\Http\Requests\StatusUpdateRequest;
use App\Services\Contracts\CMS\StatusServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StatusController extends Controller
{
    use ApiResponse;

    private $repo;

    public function __construct(StatusServiceInterface $repo)
    {
        $this->repo = $repo;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->repo->get();
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StatusStoreRequest $request)
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
            $data = $this->repo->getStatusById($id);
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StatusUpdateRequest $request, int $statusId)
    {
        try {
            $data = $this->repo->update($request->all(), $statusId);
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

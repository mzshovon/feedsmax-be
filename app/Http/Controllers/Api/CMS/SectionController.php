<?php

namespace App\Http\Controllers\Api\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteRequest;
use App\Http\Requests\SectionStoreRequest;
use App\Http\Requests\SectionUpdateRequest;
use App\Services\Contracts\CMS\SectionServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SectionController extends Controller
{
    use ApiResponse;

    private $repo;

    public function __construct(SectionServiceInterface $repo)
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
     * Show the form for creating a new resource.
     */
    public function create()
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
    public function store(SectionStoreRequest $request)
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
            $data = $this->repo->getSectionById($id);
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SectionUpdateRequest $request, int $channelId)
    {
        try {
            $data = $this->repo->update($request->all(), $channelId);
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

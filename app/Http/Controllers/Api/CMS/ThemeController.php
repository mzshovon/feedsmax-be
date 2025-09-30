<?php

namespace App\Http\Controllers\Api\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThemeDeleteRequest;
use App\Http\Requests\ThemeStoreRequest;
use App\Http\Requests\ThemeUpdateRequest;
use App\Services\Contracts\CMS\ThemeServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeController extends Controller
{
    use ApiResponse;

    private $repo;


    public function __construct(ThemeServiceInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->get("search") ?? null;
            $columns = $request->get("columns") ?? null;
            $data = $this->repo->get($search, $columns);
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), $ex->getTrace(), $ex->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ThemeStoreRequest $request)
    {
        try {
            $data = $this->repo->store($request->all());
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), $ex->getTrace(), $ex->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try {
            $data = $this->repo->getThemeById($id);
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), $ex->getTrace(), $ex->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ThemeUpdateRequest $request, int $id)
    {
        try {
            $data = $this->repo->update($request->all(), $id);
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), $ex->getTrace(), $ex->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThemeDeleteRequest $request, string $id)
    {
        try {
            $data = $this->repo->delete($id, $request->all());
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), $ex->getTrace(), $ex->getCode());
        }
    }
}

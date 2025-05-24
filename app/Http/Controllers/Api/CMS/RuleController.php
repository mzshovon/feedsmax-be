<?php

namespace App\Http\Controllers\Api\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChannelStoreRequest;
use App\Http\Requests\ChannelUpdateRequest;
use App\Http\Requests\DeleteRequest;
use App\Http\Requests\RuleUpdateRequest;
use App\Services\Contracts\CMS\RuleServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RuleController extends Controller
{
    use ApiResponse;

    private $repo;

    public function __construct(RuleServiceInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getRulesForSelection()
    {
        try {
            $data = $this->repo->getRulesNameForSelection();
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace(), 500);
        }
    }

    public function updateRule(RuleUpdateRequest $request)
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeedbackRequest;
use App\Services\Contracts\QuestionServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
// use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QuestionController extends Controller
{
    use ApiResponse;
    private QuestionServiceInterface $repo;

    public function __construct(QuestionServiceInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function questions(Request $request): JsonResponse
    {
        try {
            $attemptId = $request->attemptId ?? null;
            $channel = $request->channel ?? null;
            $msisdn = $request->msisdn ?? null;
            $data = $this->repo->questionList($attemptId, $channel, $msisdn, $request->url());
            return $this->success($data, !empty($data) ? Response::HTTP_OK : Response::HTTP_GONE);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * @param FeedbackRequest $request
     * @return JsonResponse
     */
    public function feedback(FeedbackRequest $request): JsonResponse
    {
        try {
            $data = $this->repo->processResponseFromQuestion($request->all());
            return $this->success($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }
}

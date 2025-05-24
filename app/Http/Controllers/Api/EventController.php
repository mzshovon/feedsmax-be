<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Services\Contracts\TriggerServiceInterface;
use App\Traits\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class EventController extends Controller
{
    use ApiResponse;
    private TriggerServiceInterface $service;

    public function __construct(TriggerServiceInterface $service)
    {
        $this->service = $service;
    }

    public function trigger(EventRequest $request, $channel, $event)
    {
        try {
            $data = $this->service->trigger($channel, $event, $request->all());
            return $this->success($data, $data['match'] ? Response::HTTP_CREATED : Response::HTTP_ALREADY_REPORTED);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getTrace());
        }
    }
}

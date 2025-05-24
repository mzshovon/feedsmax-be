<?php

namespace App\Services\CMS;

use App\Repositories\AttemptRepo;
use App\Repositories\FeedbackRepo;
use App\Services\Contracts\CMS\FeedbackServiceInterface;

class FeedbackService implements FeedbackServiceInterface
{
    // Your service class code here
	public function __construct(
        private readonly FeedbackRepo $feedbackRepo,
        private readonly AttemptRepo $attemptRepo,
    ){}

	public function get(){}

	public function getFeedbackById(int $id){}

	public function store(array $request){
        $attemptId = $request['attempt_id'];
        $context = $request['context'];
        $feedback = $request['feedback'];
        $status = $request['status'];
        $comment = $request['comment'] ?? null;
        array_walk($feedback, function(&$response) use ($attemptId, $context, $status){
            $response['attempt_id'] = $attemptId;
            $response['context'] = $context;
            $response['status'] = $status;
        });
        $storeFeedback = $this->feedbackRepo->storeFeedback($feedback);
        if($storeFeedback) {
            if($comment){
                $this->attemptRepo->update("id", $attemptId, ["remarks" => $comment]);
            }
            return true;
        }
        return false;
    }

	public function update(array $request, int $id){}

	public function delete(int $id){}

}

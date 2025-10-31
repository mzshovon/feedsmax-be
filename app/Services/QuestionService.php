<?php

namespace App\Services;

use App\Entity\QuestionResponseEntity;
use App\Repositories\{BucketRepo, QuestionRepo};
use App\Services\Contracts\QuestionServiceInterface;
use Exception;

class QuestionService implements QuestionServiceInterface
{
    const SCORE_IDENTIFIER = "rating";
    const CHANNEL_INDEX_IN_URL = 7;
    const EVENT_INDEX_IN_URL = 8;

    public function __construct(
        private readonly QuestionRepo $questionRepo,
        private readonly AttemptService $attemptService,
        private readonly ResponseService $responseService,
        private readonly BucketRepo $bucketRepo,
    ) {}

    /**
     * @param int $striveId
     * @param string $channel
     * @param string $referenceId
     * @param string $url
     * 
     * @return array
     */
    public function questionList(
        int $striveId, 
        string $channel, 
        string $referenceId, 
        string $url
    ): array
    {
        try {
            $questionList = [];
            $scoreRangeField = [];
            $parsedUrl = explode("/", $url);
            $redirectionLink = "www.youtube.com";
            $channel = $parsedUrl[self::CHANNEL_INDEX_IN_URL];
            $event = $parsedUrl[self::EVENT_INDEX_IN_URL];
            [
                $eventId, 
                $bucketId, 
                $pagination, 
                $channelName, 
                $eventName, 
                $questions,
                $language
            ] = $this->attemptService->getEventInfoFromId($striveId);
            if(($channelName === $channel) && ($eventName === $event) ) {
                if ($eventId && $striveId && !empty($questions)) {
                    [$questionList, $scoreRangeField] = $this->scoreAndQuestionDivider($questions, $bucketId);
                }
            }
            return $this->response(
                $eventId,
                $striveId,
                $questionList,
                $scoreRangeField,
                $pagination ?? 1,
                $redirectionLink
            );
        } catch (\Exception $e) {
            throw new Exception("No Question Found!");
        }

    }

    /**
     * @throws Exception
     */
    public function processFeedback(array $request): bool
    {
        try {
            $this->responseService->store($request);
            return true;
        } catch (\Exception $ex) {
            throw new Exception("Invalid Response Request!");
        }

    }

    /**
     * @param int|null $eventId
     * @param int|null $striveId
     * @param array $questions
     * @param array $scoreRangeField
     * @param array $theme
     * @param int|null $pagination
     * @param string $redirectionLink
     *
     * @return array
     */
    private function response(
        int|null $eventId,
        int|null $striveId,
        array $questions,
        array $scoreRangeField,
        ?int $pagination = 1,
        string $redirectionLink,
        ): array
    {
        $data = [];
        if (!empty($questions)) {
            $FieldTypes = getSelectionTypes();
            $data = (new QuestionResponseEntity())
                ->setEventId($eventId)
                ->setStriveId($striveId)
                ->setRedirectionLink($redirectionLink)
                ->setPagination($pagination)
                ->setFieldTypes($FieldTypes)
                ->setScoreRangeField($scoreRangeField)
                ->setQuestions($questions)
                ->build();
        }
        return $data;
    }

    /**
     * @param array $questions
     * @param int $bucketId
     *
     * @return array
     */
    private function scoreAndQuestionDivider(array $questions, int $bucketId) : array
    {
        $questionSet = [];
        $scoreRangeField = [];
        foreach ($questions as $question) {
            $questionList = [];
            if($question) {
                $questionList = [
                    "id" => $question['id'],
                    "question_en" => $question['question_en'],
                    "question_another_lang" => $question['question_another_lang'],
                    "field_type" => $question['field_type'],
                    "options" => $question['options'],
                    "ref_id" => $question['ref_id'],
                    "ref_val" => $question['ref_val'],
                    "parent_id" => $question['parent_id'],
                    "status" => $question['status'],
                    "order" => $question['order'],
                ];
            }
            if(str_contains($question['field_type'], SELF::SCORE_IDENTIFIER)) {
                $scoreRangeField = $questionList;
            } else {
                $questionSet[$question['score_range'] ?? "0-0"][] = $questionList;
            }
        }
        return [$questionSet, $scoreRangeField];
    }
}

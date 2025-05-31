<?php

namespace App\Services;

use App\Entity\QuestionResponseEntity;
use App\Enums\FieldType;
use App\Repositories\GroupRepo;
use App\Repositories\QuestionRepo;
use App\Repositories\RedirectionRepo;
use App\Services\Contracts\QuestionServiceInterface;
use Exception;

class QuestionService implements QuestionServiceInterface
{
    const SCORE_IDENTIFIER = "rating";

    private array|null $scoreRangeField;

    const CHANNEL_INDEX_IN_URL = 7;
    const EVENT_INDEX_IN_URL = 8;

    public function __construct(
        private readonly QuestionRepo $questionRepo,
        private readonly AttemptService $attemptService,
        private readonly ResponseService $responseService,
        private readonly RedirectionRepo $redirectionRepo,
        private readonly GroupRepo $groupRepo,
    ) {
        $this->scoreRangeField = config('app.nps') ?? null;
    }

    /**
     * @param int $striveId
     * @param string $channel
     * @param string $referenceId
     * @param string $url
     * 
     * @return array
     */
    public function questionList(int $striveId, string $channel, string $referenceId, string $url): array
    {
        try {
            $questionList = [];
            $scoreRangeField = [];
            $parsedUrl = explode("/", $url);
            $redirectionLink = "www.youtube.com";
            $channel = $parsedUrl[self::CHANNEL_INDEX_IN_URL];
            $event = $parsedUrl[self::EVENT_INDEX_IN_URL];

            // TODO: Need to Update the Indexing & Redis Queue
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
                    // $redirectionLink = $this->redirectionRepo->getRedirectionLinkByStriveId($striveId, $channel);
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
        $new = [];
        $scoreRangeField = [];
        foreach ($questions as $question) {
            if(str_contains($question['field_type'], SELF::SCORE_IDENTIFIER)) {
                $scoreRangeField = $question;
            } else {
                $new[$question['score_range'] ?? "0-0"][] = $question;
            }
        }
        // $groupInfo = $this->groupRepo->getBucketById($bucketId);
        // if($groupInfo && $groupInfo->topQuestion) {
        //     // If top question id found
        //     $scoreRangeField = json_decode($groupInfo->topQuestion->toJson(), true); // Typo set for array to covert array model -> json -> array
        //     if(!isset($scoreRangeField['nps_rating_mapping'])) {
        //         $scoreRangeField['nps_rating_mapping'] = config('app.nps.nps_rating_mapping');
        //     }
        // } else if ($groupInfo && $groupInfo->type && !$groupInfo->topQuestion) {
        //     // If type  found but top question id not found
        //     $scoreRangeField = config("app.".strtolower($groupInfo->type)) ?? $this->scoreRangeField;
        // } else {
        //     // If type and top question id not found
        //     $scoreRangeField = $this->scoreRangeField;
        // }
        return [$new, $scoreRangeField];
    }
}

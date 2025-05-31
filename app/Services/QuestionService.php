<?php

namespace App\Services;

use App\Entity\QuestionResponseEntity;
use App\Enums\ChoiceType;
use App\Repositories\GroupRepo;
use App\Repositories\QuestionRepo;
use App\Repositories\RedirectionRepo;
use App\Services\CMS\ThemeService;
use App\Services\Contracts\QuestionServiceInterface;
use Exception;

class QuestionService implements QuestionServiceInterface
{
    private array|null $nps;

    const CHANNEL_INDEX_IN_URL = 7;
    const EVENT_INDEX_IN_URL = 8;

    public function __construct(
        private readonly QuestionRepo $questionRepo,
        private readonly AttemptService $attemptService,
        private readonly ResponseService $responseService,
        private readonly RedirectionRepo $redirectionRepo,
        private readonly GroupRepo $groupRepo,
    ) {
        $this->nps = config('app.nps') ?? null;
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
            $questions = [];
            $nps = [];
            $parsedUrl = explode("/", $url);
            $redirectionLink = "";

            $channel = $parsedUrl[self::CHANNEL_INDEX_IN_URL];
            $event = $parsedUrl[self::EVENT_INDEX_IN_URL];

            dd($channel, $event);

            // TODO: Need to Update the Indexing & Redis Queue
            [$eventId, $groupId, $pagination, $channelName, $eventName, $language] = $this->attemptService->getEventInfoFromId($striveId);

            if(($channelName === $channel) && ($eventName === $event)) {
                if ($eventId && $striveId) {
                    $questions = $this->questionRepo->getAllQuestionsByGroupId($groupId);
                    [$questions, $nps] = $this->npsAndQuestionDivider($questions, $groupId);
                    $redirectionLink = $this->redirectionRepo->getRedirectionLinkByAttemptId($striveId, $channel);
                }
            }

            return $this->response(
                $triggerId,
                $attemptId,
                $questions,
                $nps,
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
     * @param int|null $triggerId
     * @param int|null $attemptId
     * @param array $questions
     * @param array $nps
     * @param array $theme
     * @param int|null $pagination
     * @param string $redirectionLink
     *
     * @return array
     */
    private function response(
        int|null $triggerId,
        int|null $attemptId,
        array $questions,
        array $nps,
        ?int $pagination = 1,
        string $redirectionLink,
        ): array
    {
        $data = [];
        if (!empty($questions)) {
            $choiceTypes = getSelectionTypes();
            $data = (new QuestionResponseEntity())
                ->setTriggerId($triggerId)
                ->setAttemptId($attemptId)
                ->setRedirectionLink($redirectionLink)
                ->setPagination($pagination)
                ->setChoiceTypes($choiceTypes)
                ->setNps($nps)
                ->setQuestions($questions)
                ->build();
        }
        return $data;
    }

    /**
     * @param array $questions
     * @param int $groupId
     *
     * @return array
     */
    private function npsAndQuestionDivider(array $questions, int $groupId) : array
    {
        $new = [];
        $nps = [];
        foreach ($questions as $question) {
            if(isset($question['range']) && $question['selection_type'] !== ChoiceType::NPS->value)
                $new[$question['range']][] = $question;
        }
        $groupInfo = $this->groupRepo->getBucketById($groupId);
        if($groupInfo && $groupInfo->topQuestion) {
            // If top question id found
            $nps = json_decode($groupInfo->topQuestion->toJson(), true); // Typo set for array to covert array model -> json -> array
            if(!isset($nps['nps_rating_mapping'])) {
                $nps['nps_rating_mapping'] = config('app.nps.nps_rating_mapping');
            }
        } else if ($groupInfo && $groupInfo->type && !$groupInfo->topQuestion) {
            // If type  found but top question id not found
            $nps = config("app.".strtolower($groupInfo->type)) ?? $this->nps;
        } else {
            // If type and top question id not found
            $nps = $this->nps;
        }
        return [$new, $nps];
    }
}

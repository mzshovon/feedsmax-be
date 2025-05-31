<?php

namespace App\Entity;


class QuestionResponseEntity
{
    /**
     * @var array
     */
    private array $data;

    public function __construct()
    {
        return $this;
    }

    /**
     * @param string $triggerId
     * @return $this
     */
    public function setEventId(string $triggerId): QuestionResponseEntity{
        $this->data['trigger_id'] = $triggerId;
        return $this;
    }

    /**
     * @param string $triggerId
     * @return $this
     */
    public function setStriveId(string $attemptId): QuestionResponseEntity{
        $this->data['attempt_id'] = $attemptId;
        return $this;
    }

    /**
     * @param string $uuid
     * @return $this
     */
    public function setUuid(string $uuid): QuestionResponseEntity{
        $this->data['uuid'] = $uuid;
        return $this;
    }

    /**
     * @param array $choiceTypes
     *
     * @return $this
     */
    public function setChoiceTypes(array $choiceTypes): QuestionResponseEntity{
        $this->data['choice_types'] = $choiceTypes;
        return $this;
    }
    /**
     * @param array|null $nps
     *
     * @return $this
     */
    public function setNps(array|null $nps): QuestionResponseEntity{
        $this->data['nps'] = $nps;
        return $this;
    }
    /**
     * @param array $questions
     * @return $this
     */
    public function setQuestions(array $questions): QuestionResponseEntity{
        $this->data['questions'] = $questions;
        return $this;
    }

    /**
     * @param int $numOfQuestions
     * @return $this
     */
    public function setPagination(int $numOfQuestions): QuestionResponseEntity{
        $this->data['num_of_questions'] = $numOfQuestions;
        return $this;
    }

    /**
     * @param string $redirectionLink
     * @return $this
     */
    public function setRedirectionLink(string $redirectionLink): QuestionResponseEntity{
        $this->data['redirection_link'] = !empty($redirectionLink) ? $redirectionLink : null;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array{
        return $this->data;
    }

}

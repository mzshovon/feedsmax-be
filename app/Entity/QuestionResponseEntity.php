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
     * @param string $eventId
     * @return $this
     */
    public function setEventId(string $eventId): QuestionResponseEntity{
        $this->data['event_id'] = $eventId;
        return $this;
    }

    /**
     * @param string $eventId
     * @return $this
     */
    public function setStriveId(string $striveId): QuestionResponseEntity{
        $this->data['strive_id'] = $striveId;
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
     * @param array $fieldTypes
     *
     * @return $this
     */
    public function setFieldTypes(array $fieldTypes): QuestionResponseEntity{
        $this->data['choice_types'] = $fieldTypes;
        return $this;
    }
    /**
     * @param array|null $scoreRangeField
     *
     * @return $this
     */
    public function setScoreRangeField(array|null $scoreRangeField): QuestionResponseEntity{
        $this->data['scoreRangeField'] = $scoreRangeField;
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
     * @param int $pagination
     * @return $this
     */
    public function setPagination(int $pagination): QuestionResponseEntity{
        $this->data['pagination'] = $pagination;
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

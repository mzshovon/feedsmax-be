<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class QuestionResponseEntityForCMS
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
     * @param string $question_en
     *
     * @return $this
     */
    public function setQuestionEn(string $question_en): QuestionResponseEntityForCMS{
        $this->data['question_en'] = $question_en;
        return $this;
    }

    /**
     * @param string $question_bn
     *
     * @return $this
     */
    public function setQuestionBn(string $question_bn): QuestionResponseEntityForCMS{
        $this->data['question_bn'] = $question_bn;
        return $this;
    }

    /**
     * @param string|null $selection_type
     *
     * @return $this
     */
    public function setSelectionType(string|null $selection_type): QuestionResponseEntityForCMS{
        $this->data['selection_type'] = $selection_type;
        return $this;
    }

    /**
     * @param string|null $options
     *
     * @return $this
     */
    public function setOptions(string|null $options): QuestionResponseEntityForCMS{
        $this->data['options'] = $options;
        return $this;
    }

    /**
     * @param string|null $range
     *
     * @return $this
     */
    public function setRange(string|null $range): QuestionResponseEntityForCMS{
        $this->data['range'] = $range;
        return $this;
    }

    /**
     * @param Model|null $parent_question
     *
     * @return $this
     */
    public function setParentQuestion(Model|null $parent_question): QuestionResponseEntityForCMS{
        $this->data['parent_question'] = $parent_question;
        return $this;
    }

    /**
     * @param int $order
     *
     * @return $this
     */
    public function setOrder(int $order): QuestionResponseEntityForCMS{
        $this->data['order'] = $order;
        return $this;
    }

    /**
     * @param array $num_of_questions
     * @return $this
     */
    public function setStatus(int $status): QuestionResponseEntityForCMS{
        $this->data['status'] = $status;
        return $this;
    }

    /**
     * @param int $is_required
     * @return $this
     */
    public function setIsRequired(int $is_required): QuestionResponseEntityForCMS{
        $this->data['is_required'] = $is_required;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array{
        return $this->data;
    }

}

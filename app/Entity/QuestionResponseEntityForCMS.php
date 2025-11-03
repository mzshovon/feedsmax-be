<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Collection;
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
    public function setAnotherLang(string $question_another_lang): QuestionResponseEntityForCMS{
        $this->data['question_another_lang'] = $question_another_lang;
        return $this;
    }

    /**
     * @param string|null $field_type
     *
     * @return $this
     */
    public function setFieldType(string|null $field_type): QuestionResponseEntityForCMS{
        $this->data['field_type'] = $field_type;
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
    public function setScoreRange(string|null $range): QuestionResponseEntityForCMS{
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
     * @param Collection|null $child_questions
     *
     * @return $this
     */
    public function setChildrenQuestion(Collection|null $child_questions): QuestionResponseEntityForCMS{
        $this->data['child_questions'] = $child_questions;
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
     * @param bool $required
     * @return $this
     */
    public function setRequired(bool $required): QuestionResponseEntityForCMS{
        $this->data['required'] = $required;
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array{
        return $this->data;
    }

}

<?php

namespace App\Services\CMS;

use App\Entity\QuestionResponseEntityForCMS;
use App\Enums\ChoiceType;
use App\Enums\FieldType;
use App\Repositories\QuestionRepo;
use App\Services\Contracts\CMS\QuestionServiceInterface;
use Illuminate\Database\Eloquent\Model;

class QuestionService implements QuestionServiceInterface
{
    private array $selectionListForAutoOptionsAdd = [];

    public function __construct(
        private QuestionRepo $questionRepo
    ) {
        // $this->selectionListForAutoOptionsAdd = [
        //     FieldType::Rating1To5Number->value,
        //     ChoiceType::Score0To10Number->value,
        //     ChoiceType::RatingYesNo->value,
        //     ChoiceType::RatingNoYes->value,
        //     ChoiceType::RatingYesNoNeutral->value,
        // ];
    }

    /**
     * @return array
     */
    public function get(?string $columns = null): array
    {
        $columns = !empty($columns) ? explode(",", $columns) : ["*"];
        $data = $this->questionRepo->getQuestionList("read", $columns);
        return $data;
    }

    /**
     * @return array
     */
    public function getQuestionById(int $questionId): array
    {
        $data = $this->response($this->questionRepo->getQuestionById($questionId));
        return $data;
    }

    public function store(array $request): bool
    {
        if(isset($request['options'])){
            $request['options'] = json_encode($request['options']);
        }
        // From CMS it will be confusing for user to add options for NPS rating. It will be manipulate from Feedsmax-Backend.
        if(!isset($request['options']) && in_array($request['field_type'], $this->selectionListForAutoOptionsAdd)) {
            $request['options'] = json_encode(config("question.default_option_map.{$request['field_type']}.options")) ?? null;
        }

        if(array_key_exists("nps_rating_mapping",$request)){
            $request['nps_rating_mapping'] = json_encode($request['nps_rating_mapping']);
        }
        $storeData = $this->questionRepo->storeQuestion($request);
        if ($storeData) {
            return true;
        }
        return false;
    }

    public function update(array $request, int $questionId): bool
    {
        // For updating existing record it will and populate data if not present.
        if(!isset($request['options']) && isset($request['field_type']) && in_array($request['field_type'], $this->selectionListForAutoOptionsAdd)) {
            $question = $this->questionRepo->getQuestionById($questionId);
            if(!$question->options || empty($question->options)) {
                $request['options'] = json_encode(config("question.default_option_map.{$request['field_type']}.options")) ?? null;
            }
        }

        $fillableData = $this->fillableData($request);

        $storeData = $this->questionRepo->updateQuestionById("id", $questionId, $fillableData);
        if ($storeData) {
            return true;
        }
        return false;
    }

    /**
     * @param array $request
     *
     * @return array
     */
    private function fillableData(array $request): array{
        $data = [];
        $fillable = [
            'user_name',
            'id',
            'question_en',
            'question_another_lang',
            'field_type',
            'options',
            'score_range',
            'parent_id',
            'ref_id',
            'ref_val',
            'order',
            'status',
            'required'
        ];
        foreach($request as $key => $value){
            if(in_array($key, $fillable)){
                if($key == 'nps_rating_mapping')
                    $data[$key] = json_encode($value);
                else
                    $data[$key] = $value;
            }
        }
        return $data;
    }


    /**
     * @param int $questionId
     * @param array $request
     *
     * @return bool
     */
    public function delete(int $questionId, array $request): bool
    {
        return $this->questionRepo->deleteQuestionById($questionId, $request);
    }

    /**
     * @param Model|null $question
     *
     * @return array
     */
    private function response(Model|null $question): array
    {
        $data = [];
        if ($question) {
            $data = (new QuestionResponseEntityForCMS())
                ->setQuestionEn($question->question_en)
                ->setAnotherLang($question->question_another_lang)
                ->setFieldType($question->field_type)
                ->setOptions($question->options)
                ->setScoreRange($question->score_range)
                ->setOrder($question->order)
                ->setStatus($question->status)
                ->setParentQuestion($question->parent)
                ->setChildrenQuestion($question->children)
                ->setRequired($question->required)
                ->build();
        }

        return $data;
    }
}

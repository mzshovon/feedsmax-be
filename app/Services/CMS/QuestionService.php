<?php

namespace App\Services\CMS;

use App\Entity\QuestionResponseEntityForCMS;
use App\Enums\ChoiceType;
use App\Repositories\QuestionRepo;
use App\Services\Contracts\CMS\QuestionServiceInterface;
use Illuminate\Database\Eloquent\Model;

class QuestionService implements QuestionServiceInterface
{
    private array $selectionListForAutoOptionsAdd = [];

    public function __construct(
        private QuestionRepo $questionRepo
    ) {
        $this->selectionListForAutoOptionsAdd = [
            ChoiceType::Rating1To5Number->value,
            ChoiceType::Score0To10Number->value,
            ChoiceType::RatingYesNo->value,
            ChoiceType::RatingNoYes->value,
            ChoiceType::RatingYesNoNeutral->value,
        ];
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $data = $this->questionRepo->getQuestionList();
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
        // From CMS it will be confusing for user to add options for NPS rating. It will be manipulate from CFL-Backend.
        if(!isset($request['options']) && in_array($request['selection_type'], $this->selectionListForAutoOptionsAdd)) {
            $request['options'] = json_encode(config("question.default_option_map.{$request['selection_type']}.options")) ?? null;
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
        if(!isset($request['options']) && in_array($request['selection_type'], $this->selectionListForAutoOptionsAdd)) {
            $question = $this->questionRepo->getQuestionById($questionId);
            if(!$question->options || empty($question->options)) {
                $request['options'] = json_encode(config("question.default_option_map.{$request['selection_type']}.options")) ?? null;
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
            'question_bn',
            'selection_type',
            'options',
            'range',
            'parent_id',
            'order',
            'status',
            'nps_rating_mapping',
            'is_required'
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
                ->setQuestionBn($question->question_bn)
                ->setSelectionType($question->selection_type)
                ->setOptions($question->options)
                ->setRange($question->range)
                ->setOrder($question->order)
                ->setStatus($question->status)
                ->setParentQuestion($question->parent)
                ->setIsRequired($question->is_required)
                ->build();
        }

        return $data;
    }
}

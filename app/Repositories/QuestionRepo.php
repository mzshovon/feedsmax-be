<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\Questionnaire;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class QuestionRepo
{
    public $model;
    public $currentEnvironment;
    // public array $comparedEnvironment = ['production', 'local'];
    private string $question_cache_key = 'questions_for_group_';
    const IDENTIFIER = 'question';

    public function __construct(Questionnaire $model)
    {
        $this->model = $model;
        $this->currentEnvironment = config('app.env');
    }

    public function getAllQuestionsByGroupId(int $groupId, string $instance_type= "read"): array
    {
        try {
            $questionsFromCache = $this->getQuestionsFromCacheByGroupId($groupId);
            if (!empty($questionsFromCache)) {
                return $questionsFromCache;
            }
            $questions = $this->model::on('mysql::' . $instance_type)->with('children')
                ->whereRelation('groups', 'groups.id', $groupId)
                ->where('status', 1)
                ->orderBy('order')
                ->get()
                // ->groupBy('range')
                ->toArray();
            if (!empty($questions)) {
                $this->storeQuestionsInCacheByGroupId($groupId, $questions);
                return $questions;
            }

            return [];
        } catch (\Exception $ex) {
            $questions = $this->model::on('mysql::' . $instance_type)->with('children')
                ->whereRelation('groups', 'groups.id', $groupId)
                ->where('status', 1)
                ->orderBy('order')
                ->get()
                // ->groupBy('range')
                ->toArray();
            if (!empty($questions)) {
                return $questions;
            }

            return [];
        }

    }

    /**
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return array
     */
    public function getQuestionList($instance_type = "read", array $selected_columns = ["*"]): array
    {
        $channel = $this->model::on('mysql::' . $instance_type)->select($selected_columns)->get()->toArray();
        return $channel;
    }

    /**
     * @param int $channel_id
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model
     */
    public function getQuestionById(int $question_id, $instance_type = "read", array $selected_columns = ["*"]): Model|null
    {
        $channel = $this->model::on('mysql::' . $instance_type)
            ->select($selected_columns)
            ->where("id", $question_id)
            ->first();
        return $channel;
    }

    /**
     * @param int $groupId
     *
     * @return array
     */
    public function getQuestionListForCMSByGroupId(int $groupId): array
    {
        $questions = $this->model::with('children')
            ->whereRelation('groups', 'groups.id', $groupId)
            ->orderBy('order')
            ->get()
            ->toArray();

        return $questions;
    }

        /**
     * @param array $request
     *
     * @return Model
     */
    public function storeQuestion(array $request): Model
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $store = $this->model::on('mysql::write')->create($request);

            event(new PopulateChangeLog(
                CrudEnum::Create->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                null,
                json_encode($request)
            ));
            return $store;

        } catch (\Exception $e) {
            $store = $this->model::on('mysql::write')->create($request);

            event(new PopulateChangeLog(
                CrudEnum::Create->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                null,
                json_encode($request)
            ));
            return $store;
        }
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function updateQuestionById(string $param, string $value, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where($param, $value)->first();
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $question = $this->model::on('mysql::write')->where($param, $value)->update($request);

            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $question;

        } catch (\Exception $e) {
            $question = $this->model::on('mysql::write')->where($param, $value)->update($request);

            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $question;
        }
    }

    /**
     * @param int $questionId
     * @param array $request
     *
     * @return bool
     */
    public function deleteQuestionById(int $questionId, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where("id", $questionId)->first();

        try {
            deleteCacheDataByTableName($this->model->getTable());
            $question = $this->model::on('mysql::write')->where("id", $questionId)->delete();

            event(new PopulateChangeLog(
                CrudEnum::Delete->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                null
            ));
            return $question;

        } catch (\Exception $e) {
            $question = $this->model::on('mysql::write')->where("id", $questionId)->delete();

            event(new PopulateChangeLog(
                CrudEnum::Delete->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                null
            ));
            return $question;
        }
    }
    /**
     * @param int $groupId
     * @return array
     */
    private function getQuestionsFromCacheByGroupId(int $groupId): array
    {
        try {
            if (Cache::has($this->question_cache_key . $groupId)) {
                return json_decode(Cache::get($this->question_cache_key . $groupId), true);
            }
            return [];
        } catch (\Exception $ex) {
            return [];
        }

    }

    /**
     * @param int $groupId
     *
     * @return array|bool
     */
    private function storeQuestionsInCacheByGroupId(int $groupId, array $questions): void
    {
        $ttl = config('cache.ttl.questions');
        Cache::put($this->question_cache_key . $groupId, json_encode($questions), $ttl);
    }
}

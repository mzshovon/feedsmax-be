<?php

namespace App\Repositories;

use App\Models\Feedback;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class FeedbackRepo
{
    public Model $model;

    public function __construct(Feedback $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return array
     */
    public function getFeedback($instance_type = "read", array $selected_columns = ["*"]): array
    {
        $feedback = $this->model::on('mysql::' . $instance_type)
                    ->select($selected_columns)
                    ->get()
                    ->toArray();
        return $feedback;
    }

    /**
     * @param int $feedback_id
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model
     */
    public function getFeedbackById(int $feedback_id, $instance_type = "read", array $selected_columns = ["*"]): Model|null
    {
        $feedback = $this->model::on('mysql::' . $instance_type)
            ->select($selected_columns)
            ->where("id", $feedback_id)
            ->first();
        return $feedback;
    }

    /**
     * @param array $request
     *
     * @return Collection
     */
    public function storeFeedback(array $request): Collection
    {
        return collect($request)->each(function ($feedback) {
            $this->model::on('mysql::write')->create($feedback);
        });
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function updateFeedbackById(string $param, string $value, array $request): bool
    {
        $feedback = $this->model::on('mysql::write')->where($param, $value)->update($request);
        return $feedback;
    }

    /**
     * @param int $feedback_id
     *
     * @return bool
     */
    public function deleteFeedbackById(int $feedback_id): bool
    {
        $feedback = $this->model::on('mysql::write')->where("id", $feedback_id)->delete();
        return $feedback;
    }
}

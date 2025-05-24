<?php

namespace App\Repositories;

use App\Models\SentimentMapper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SentimentRepo
{
    public Model $model;

    public function __construct(SentimentMapper $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return array
     */
    public function getSentiment($instance_type = "read", array $selected_columns = ["*"]): array
    {
        $section = $this->model::on('mysql::' . $instance_type)
                    ->select($selected_columns)
                    ->get()
                    ->toArray();
        return $section;
    }

    /**
     * @param int $sentiment_id
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model
     */
    public function getSentimentById(int $sentiment_id, $instance_type = "read", array $selected_columns = ["*"]): Model|null
    {
        $section = $this->model::on('mysql::' . $instance_type)
            ->select($selected_columns)
            ->where("id", $sentiment_id)
            ->first();
        return $section;
    }

    /**
     * @param array $request
     *
     * @return Collection
     */
    public function storeSentiment(array $requests): Collection
    {
        return collect($requests)->each(function ($request) {
            $this->model::on('mysql::write')->create($request);
        });
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function updateSentimentById(string $param, string $value, array $request): bool
    {
        $section = $this->model::on('mysql::write')->where($param, $value)->update($request);
        return $section;
    }

    /**
     * @param int $sentiment_id
     *
     * @return bool
     */
    public function deleteSentimentById(int $sentiment_id): bool
    {
        $section = $this->model::on('mysql::write')->where("id", $sentiment_id)->delete();
        return $section;
    }
}

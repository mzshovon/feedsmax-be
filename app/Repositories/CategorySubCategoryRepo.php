<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\CategorySubCategory;
use Illuminate\Database\Eloquent\Model;

class CategorySubCategoryRepo
{
    public Model $model;
    const IDENTIFIER = 'categories_sub_categories';

    public function __construct(CategorySubCategory $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return array
     */
    public function getCategorySubCategories($instance_type = "read", array $selected_columns = ["*"]): array
    {
        $channel = $this->model::on('mysql::' . $instance_type)
                    ->with('children')
                    ->select($selected_columns)
                    ->get()
                    ->toArray();
        return $channel;
    }

    /**
     * @param int $cat_id
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model
     */
    public function getCategoryOrSubCategoryById(int $cat_id, $instance_type = "read", array $selected_columns = ["*"]): Model|null
    {
        $channel = $this->model::on('mysql::' . $instance_type)
            ->select($selected_columns)
            ->where("id", $cat_id)
            ->first();
        return $channel;
    }

    /**
     * @param array $request
     *
     * @return Model
     */
    public function storeCategorySubCategory(array $request): Model
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $store = $this->model::on('mysql::write')->create($request);

        event(new PopulateChangeLog(
            CrudEnum::Create->value. "_" .self::IDENTIFIER,
            $this->model->getTable(),
            $userName,
            null,
            json_encode($request)
        ));
        return $store;    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function updateCategorySubCategoryById(string $param, string $value, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where($param, $value)->first();
        $channel = $this->model::on('mysql::write')->where($param, $value)->update($request);
        if($existing) {
            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                $existing ? json_encode($existing->attributesToArray()) : "",
                json_encode($request)
            ));
        }
        return $channel;
    }

    /**
     * @param int $cat_id
     * @param array $request
     *
     * @return bool
     */
    public function deleteCategorySubCategoryById(int $cat_id, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where("id", $cat_id)->first();
        $event = $this->model::on('mysql::write')->where("id", $cat_id)->delete();

        event(new PopulateChangeLog(
            CrudEnum::Delete->value. "_" .self::IDENTIFIER,
            $this->model->getTable(),
            $userName,
            json_encode($existing->attributesToArray()),
            null
        ));
        return $event;
    }
}

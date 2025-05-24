<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SectionRepo
{
    public Model $model;

    const IDENTIFIER = 'section';

    public function __construct(Section $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return array
     */
    public function getSections($instance_type = "read", array $selected_columns = ["*"]): array
    {
        $section = $this->model::on('mysql::' . $instance_type)
                    ->select($selected_columns)
                    ->get()
                    ->toArray();
        return $section;
    }

    /**
     * @param int $section_id
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model
     */
    public function getSectionById(int $section_id, $instance_type = "read", array $selected_columns = ["*"]): Model|null
    {
        $section = $this->model::on('mysql::' . $instance_type)
            ->select($selected_columns)
            ->where("id", $section_id)
            ->first();
        return $section;
    }

    /**
     * @param array $request
     *
     * @return Model
     */
    public function storeSection(array $request): Model
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
        return $store;
    }

    /**
     * @param array $request
     *
     * @return bool
     */
    public function updateSectionById(string $param, string $value, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where($param, $value)->first();
        $section = $this->model::on('mysql::write')->where($param, $value)->update($request);

        event(new PopulateChangeLog(
            CrudEnum::Update->value. "_" .self::IDENTIFIER,
            $this->model->getTable(),
            $userName,
            json_encode($existing->attributesToArray()),
            json_encode($request)
        ));
        return $section;
    }

    /**
     * @param int $section_id
     * @param array $request
     *
     * @return bool
     */
    public function deleteSectionById(int $section_id, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where("id", $section_id)->first();
        $section = $this->model::on('mysql::write')->where("id", $section_id)->delete();

        event(new PopulateChangeLog(
            CrudEnum::Delete->value. "_" .self::IDENTIFIER,
            $this->model->getTable(),
            $userName,
            json_encode($existing->attributesToArray()),
            null
        ));
        return $section;
    }
}

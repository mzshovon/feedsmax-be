<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ThemeRepo
{
    public Model $model;

    const IDENTIFIER = 'theme';
    const RESERVED_THEME_NAME = 'default';

    public function __construct(Theme $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return array
     */
    public function getThemes($instance_type = "read", array $selected_columns = ["*"]): array
    {
        $theme = $this->model::on('mysql::' . $instance_type)
                ->with("channels")
                ->select($selected_columns)
                ->get()
                ->toArray();
        return $theme;
    }

    /**
     * @param string $columnName
     * @param mixed $columnValue
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model
     */
    public function getThemeByColumn(string $columnName = "id", $columnValue = "", $instance_type = "read", array $selected_columns = ["*"]): Model|null
    {
        $theme = $this->model::on('mysql::' . $instance_type)
            ->with("channels")
            ->select($selected_columns)
            ->where($columnName, $columnValue)
            ->first();
        return $theme;
    }

    /**
     * @param array $request
     *
     * @return Model
     */
    public function storeTheme(array $request): Model
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        try {
            deleteCacheDataByTableName("channels");
            deleteCacheDataByTableName("questionnaires");
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
    public function updateThemeById(string $param, string $value, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where($param, $value)->first();
        try {
            deleteCacheDataByTableName("channels");
            deleteCacheDataByTableName("questionnaires");
            $theme = $this->model::on('mysql::write')->where($param, $value)->update($request);
            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $theme;

        } catch (\Exception $e) {
            $theme = $this->model::on('mysql::write')->where($param, $value)->update($request);

            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $theme;
        }
    }

    /**
     * @param int $themeId
     * @param array $request
     *
     * @return bool
     */
    public function deleteThemeById(int $themeId, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where("id", $themeId)->first();
        try {
            deleteCacheDataByTableName("channels");
            deleteCacheDataByTableName("questionnaires");
            $event = $this->model::on('mysql::write')->where("id", $themeId)->delete();

            event(new PopulateChangeLog(
                CrudEnum::Delete->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                null
            ));
            return $event;

        } catch (\Exception $e) {
            $event = $this->model::on('mysql::write')->where("id", $themeId)->delete();

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

    /**
     * @param string|array $encodedThemeData
     * @param string $themeTag
     *
     * @return array
     */
    private function decodeThemeDataToMatch(string|array $encodedThemeData, string $themeTag): array
    {
        $decodedThemeData = (is_array($encodedThemeData)) ? $encodedThemeData : json_decode($encodedThemeData, true);
        if (array_key_exists($themeTag, $decodedThemeData)) {
            return $decodedThemeData[$themeTag];
        }
        return [];
    }

    /**
     * @param array $event
     *
     * @return array
     */
    private function hashmapThemeRecord(array $event): array
    {
        $data = [];
        foreach ($event as $key => $value) {
            $data[$value['tag']] = $value;
        }
        return $data;
    }
}

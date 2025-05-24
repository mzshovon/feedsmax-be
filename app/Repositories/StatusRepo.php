<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class StatusRepo
{
    public Model $model;

    const IDENTIFIER = 'status';

    public function __construct(Status $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return array
     */
    public function getStatus($instance_type = "read", array $selected_columns = ["*"]): array
    {
        $status = $this->model::on('mysql::' . $instance_type)
                    ->select($selected_columns)
                    ->get()
                    ->toArray();
        return $status;
    }

    /**
     * @param int $status_id
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model
     */
    public function getStatusById(int $status_id, $instance_type = "read", array $selected_columns = ["*"]): Model|null
    {
        $status = $this->model::on('mysql::' . $instance_type)
            ->select($selected_columns)
            ->where("id", $status_id)
            ->first();
        return $status;
    }

    /**
     * @param array $request
     *
     * @return Model
     */
    public function storeStatus(array $request): Model
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
    public function updateStatusById(string $param, string $value, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where($param, $value)->first();
        $status = $this->model::on('mysql::write')->where($param, $value)->update($request);

        event(new PopulateChangeLog(
            CrudEnum::Update->value. "_" .self::IDENTIFIER,
            $this->model->getTable(),
            $userName,
            json_encode($existing->attributesToArray()),
            json_encode($request)
        ));
        return $status;
    }

    /**
     * @param int $status_id
     * @param array $request
     *
     * @return bool
     */
    public function deleteStatusById(int $status_id, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where("id", $status_id)->first();
        $status = $this->model::on('mysql::write')->where("id", $status_id)->delete();

        event(new PopulateChangeLog(
            CrudEnum::Delete->value. "_" .self::IDENTIFIER,
            $this->model->getTable(),
            $userName,
            json_encode($existing->attributesToArray()),
            null
        ));
        return $status;
    }
}

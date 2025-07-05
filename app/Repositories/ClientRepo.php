<?php

namespace App\Repositories;

use App\Enums\CrudEnum;
use App\Events\PopulateChangeLog;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ClientRepo
{
    public Model $model;

    const CLIENT_CACHE_KEY = 'client_records';
    const IDENTIFIER = 'client';

    public function __construct(Client $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return array
     */
    public function getClients($instance_type = "read", array $selected_columns = ["*"]): array
    {
        $client = $this->model::on('mysql::' . $instance_type)->select($selected_columns)->get()->toArray();
        return $client;
    }

    /**
     * @param int $client_id
     * @param string $instance_type
     * @param array $selected_columns
     *
     * @return Model
     */
    public function getClientById(int $client_id, $instance_type = "read", array $selected_columns = ["*"]): Model|null
    {
        $client = $this->model::on('mysql::' . $instance_type)
            ->with('subscriptions')
            ->select($selected_columns)
            ->where("id", $client_id)
            ->first();
        return $client;
    }

    /**
     * @param array $request
     *
     * @return Model
     */
    public function storeClient(array $request): Model
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
    public function updateClientById(string $param, string $value, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where($param, $value)->first();
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $client = $this->model::on('mysql::write')->where($param, $value)->update($request);

            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $client;

        } catch (\Exception $e) {
            $client = $this->model::on('mysql::write')->where($param, $value)->update($request);

            event(new PopulateChangeLog(
                CrudEnum::Update->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                json_encode($request)
            ));
            return $client;
        }
    }

    /**
     * @param int $clientId
     * @param array $request
     *
     * @return bool
     */
    public function deleteClientById(int $clientId, array $request): bool
    {
        $userName = $request['user_name'] ?? null;
        unset($request['user_name']);
        $existing = $this->model::on('mysql::read')->where("id", $clientId)->first();
        try {
            deleteCacheDataByTableName($this->model->getTable());
            $event = $this->model::on('mysql::write')->where("id", $clientId)->delete();

            event(new PopulateChangeLog(
                CrudEnum::Delete->value. "_" .self::IDENTIFIER,
                $this->model->getTable(),
                $userName,
                json_encode($existing->attributesToArray()),
                null
            ));
            return $event;

        } catch (\Exception $e) {
            $event = $this->model::on('mysql::write')->where("id", $clientId)->delete();

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
} 
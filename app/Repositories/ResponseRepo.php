<?php

namespace App\Repositories;

use App\Models\Response;
use Exception;

class ResponseRepo
{
    public $model;
    public $currentEnvironment;
    public array $comparedEnvironment = ['production', 'local'];

    public function __construct(Response $model)
    {
        $this->model = $model;
        $this->currentEnvironment = config('app.env');
    }

    /**
     * @param array $request
     * @param string $uuid
     * @param string $msisdn
     *
     * @return bool
     */
    public function store(array $request) : bool
    {
        try {
            $response = $this->model::on('mysql::write')->insert($request);
            return $response;
        } catch (\Exception $x) {
            throw new Exception("Hit Update Failed!");
        }
    }
}

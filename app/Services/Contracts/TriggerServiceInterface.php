<?php

namespace App\Services\Contracts;

interface TriggerServiceInterface {

    public function trigger(string $channel, string $event, array $request) : array;

}

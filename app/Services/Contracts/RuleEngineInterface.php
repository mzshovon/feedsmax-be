<?php

namespace App\Services\Contracts;

interface RuleEngineInterface
{
    public function match(array $request, ...$args): bool;
}

<?php

namespace App\Services\RuleService;

use App\Services\Contracts\RuleEngineInterface;

class MockRandom implements RuleEngineInterface
{
    public function match(array $request, ...$args): bool
    {
        return array_rand([true, false]);
    }
}

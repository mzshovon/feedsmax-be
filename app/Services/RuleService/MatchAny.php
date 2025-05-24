<?php

namespace App\Services\RuleService;

use App\Services\Contracts\RuleEngineInterface;

class MatchAny implements RuleEngineInterface
{
    public function match(array $request, ...$args): bool
    {
        return true;
    }
}

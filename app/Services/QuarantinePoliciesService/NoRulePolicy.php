<?php

namespace App\Services\RuleService;

use App\Services\Contracts\RuleEngineInterface;

class NoRulePolicy implements RuleEngineInterface
{
    public function match(array $request, ...$args): bool
    {
        return true;
    }
}

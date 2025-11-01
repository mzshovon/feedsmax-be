<?php

namespace App\Services\RuleService;

use App\Services\Contracts\RuleEngineInterface;

class MockSurveyDay implements RuleEngineInterface
{
    public function match(array $request, ...$args): bool
    {
        return true;
    }
}

<?php

namespace App\Services\PolicyService;

use App\Services\Contracts\RuleEngineInterface;

class SurveySessionCustomer implements RuleEngineInterface
{
    public function match(array $request, ...$args): bool
    {
        return true;
    }
}

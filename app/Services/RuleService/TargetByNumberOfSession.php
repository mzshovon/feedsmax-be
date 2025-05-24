<?php

namespace App\Services\RuleService;

use App\Enums\Comparator;
use App\Services\Contracts\RuleEngineInterface;

class TargetByNumberOfSession implements RuleEngineInterface
{

    public function match(array $request, ...$args): bool
    {
        return true;

        //TODO:

    }
}

<?php

namespace App\Services\Contracts\CMS;

interface RuleServiceInterface
{
    public function getRulesNameForSelection(): array;
    public function update(array $request): bool;
    public function delete(int $ruleId, array $request): bool;
}

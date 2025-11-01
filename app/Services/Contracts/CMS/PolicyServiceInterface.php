<?php

namespace App\Services\Contracts\CMS;

interface PolicyServiceInterface
{
    public function getPolicyList(?string $columns = null): array;
    public function getPolicyById(int $policyId): array;
    public function store(array $request): bool;
    public function update(array $request): bool;
    public function delete(int $ruleId, array $request): bool;
}

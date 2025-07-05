<?php

namespace App\Services\Contracts\CMS;

interface ClientServiceInterface
{
    public function get(): array;
    public function getClientById(int $clientId): array;
    public function store(array $request): bool;
    public function update(array $request, int $clientId): bool;
    public function delete(int $clientId, array $request): bool;
} 
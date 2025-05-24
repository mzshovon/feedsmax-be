<?php

namespace App\Services\Contracts\CMS;

interface StatusServiceInterface
{
    public function get(): array;
    public function getStatusById(int $channelId): array;
    public function store(array $request): bool;
    public function update(array $request, int $channelId): bool;
    public function delete(int $channelId, array $request): bool;
}

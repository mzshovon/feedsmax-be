<?php

namespace App\Services\Contracts\CMS;

interface ChannelServiceInterface
{
    public function get(): array;
    public function getChannelById(int $channelId): array;
    public function store(array $request): bool;
    public function update(array $request, int $channelId): bool;
    public function delete(int $channelId, array $request): bool;
}

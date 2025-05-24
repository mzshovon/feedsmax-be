<?php

namespace App\Services\Contracts\CMS;

interface EventServiceInterface
{
    public function get(): array;
    public function store(array $request): bool;
    public function update(array $request, int $eventId): bool;
    public function delete(int $id, array $request): bool;
    public function getEventById(int $eventId): array;
    public function getEventsByChannelTag(string $channelTag): array;
    public function getRulesByEventId(int $eventId): array;
    public function getQuestionsByEventId(int $eventId): array;
    public function attachRule(array $request): bool;
}

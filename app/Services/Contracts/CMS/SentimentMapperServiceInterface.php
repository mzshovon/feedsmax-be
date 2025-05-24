<?php

namespace App\Services\Contracts\CMS;

interface SentimentMapperServiceInterface
{
    public function get(): array;
    public function store(array $request): bool;
    public function update(array $request, int $channelId): bool;
    public function delete(int $channelId): bool;
    public function getSentimentRecordById(int $channelId): array;
    public function getListOfSentimentCategories(): array;
}

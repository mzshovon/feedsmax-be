<?php

namespace App\Services\Contracts\CMS;

interface CategorySubCategoryServiceInterface
{
    public function get(): array;
    public function store(array $request): bool;
    public function update(array $request, int $channelId): bool;
    public function delete(int $channelId, array $request): bool;
    public function getCategorySubCategoryById(int $channelId): array;
}

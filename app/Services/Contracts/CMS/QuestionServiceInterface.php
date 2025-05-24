<?php

namespace App\Services\Contracts\CMS;

interface QuestionServiceInterface
{
    public function get(): array;
    public function getQuestionById(int $questionId): array;
    public function store(array $request): bool;
    public function update(array $request, int $questionId): bool;
    public function delete(int $questionId, array $request): bool;
}

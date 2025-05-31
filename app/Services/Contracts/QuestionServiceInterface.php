<?php

namespace App\Services\Contracts;

interface QuestionServiceInterface {
    public function questionList(int $striveId, string $channel, string $msisdn, string $url):array;
    public function processFeedback(array $request):bool;
}

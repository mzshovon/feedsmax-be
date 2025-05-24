<?php

namespace App\Services\Contracts;

interface QuestionServiceInterface {

    public function questionList(int $attemptId, string $channel, string $msisdn, string $url):array;
    public function processResponseFromQuestion(array $request):bool;

}

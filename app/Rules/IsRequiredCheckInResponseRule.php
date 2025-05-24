<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsRequiredCheckInResponseRule implements ValidationRule
{
    private string $failedMessage = "Please fill up the required field.";
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($value as $question) {
            if(isset($question["is_required"]) && $question["is_required"] === 1) {
                if(count($question['choice']) == 0) {
                    $fail($this->failedMessage);
                } else if(
                    !isset($question['choice'][0]['value']) ||
                    !$question['choice'][0]['value'] ||
                    empty($question['choice'][0]['value']
                )) {
                    $fail($this->failedMessage);
                }
            }
        }
    }
}

<?php

namespace App\Rules;

use App\Enums\ChoiceType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ResponseFlatMaxCountRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($value as $response) {
            if(isset($response['selection_type']) && (
                $response['selection_type'] == ChoiceType::TextArea->value ||
                $response['selection_type'] == ChoiceType::Input->value
                )) {
                $field_value = $response["choice"][0]['value'];
                $length = mb_strlen($field_value);
                if($length > 1000){
                    $fail("Character Limit Reached {$length}.");
                }
            }
        }
    }
}

<?php

namespace App\Rules;

use App\Models\Theme;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RestrictDefaultThemeUpdateOrDeleteRule implements ValidationRule
{
    const RESERVED_THEME_NAME = 'default';

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $theme = Theme::whereId($value)->first();

        if($theme && $theme->name == self::RESERVED_THEME_NAME) {
            $fail("Default theme has been restricted for updating or delete");
        }
    }
}

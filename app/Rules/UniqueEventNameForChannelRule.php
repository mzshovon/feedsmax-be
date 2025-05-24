<?php

namespace App\Rules;

use App\Models\Trigger;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueEventNameForChannelRule implements ValidationRule
{
    private string $eventName;

    function __construct($eventName)
    {
        $this->eventName = $eventName;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(Trigger::whereEvent($this->eventName)->whereChannelId($value)->exists()){
            $fail("Already event name {$this->eventName} is tagged with same channel");
        }
    }
}

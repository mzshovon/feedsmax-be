<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    use FormValidationResponse;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "device_id" => "nullable|string|max:300",
            "platform" => "string|max:10",
            "app_name" => "string|max:20",
            "app_version" => "string|max:20",
            "os_version" => "string|max:20",
            "device_name" => "string|max:120",
            "model" => "string|max:200",
            "network" => "string|max:10",
            "redirection_link" => "nullable|string|max:500",
            "session_time" => [
                "nullable",
                "integer",
                function ($attribute, $value, $fail) {
                    $modulus_of = config("app.session_time_modulus_of") ?? 5;
                    if ($value % $modulus_of !== 0) {
                        $fail("The {$attribute} must be a multiple of {$modulus_of}.");
                    }
                },
            ],
            "extra" => "nullable|array",
            "lang" => "nullable|string|in:en,bn",
        ];
    }
}

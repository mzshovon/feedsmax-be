<?php

namespace App\Http\Requests;

use App\Rules\RestrictDefaultThemeUpdateOrDeleteRule;
use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class ThemeDeleteRequest extends FormRequest
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
            "id" => [
                "required",
                "integer",
                new RestrictDefaultThemeUpdateOrDeleteRule
            ],
            // "user_name" => "required|string|exists:vw_app_users,user_name",
        ];
    }

    protected function prepareForValidation()
    {
        $urlSegments = explode("/", $this->url());
        $idFromUrl = end($urlSegments);

        if ($idFromUrl != $this->id) {
            $this->merge([
                'id' => 'invalid', // Force an invalid value to trigger validation error
            ]);
        }
    }
}

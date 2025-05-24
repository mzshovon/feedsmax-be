<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class ChannelStoreRequest extends FormRequest
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
            "user_name" => "required|string|exists:vw_app_users,user_name",
            "tag" => "required|string|unique:channels,tag",
            "name" => "required|string|unique:channels,name",
            "app_key" => "required|string",
            "app_secret" => "required|string",
            "jwks" => "nullable|string",
            "status" => "required|integer",
            "num_of_questions" => "nullable|integer",
            "theme" => "required|integer|exists:themes,id",
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class ChannelUpdateRequest extends FormRequest
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
            // "user_name" => "required|string|exists:vw_app_users,user_name",
            "tag" => "nullable|string|unique:channels,tag,".$this->id,
            "name" => "nullable|string|unique:channels,name,".$this->id,
            "app_key" => "nullable|string",
            "app_secret" => "nullable|string",
            "jwks" => "nullable|string",
            "status" => "nullable|integer",
            "num_of_questions" => "nullable|integer",
        ];
    }
}

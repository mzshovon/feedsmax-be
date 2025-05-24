<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class EventUpdateRequest extends FormRequest
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
            "type" => "nullable|string",
            "context" => "nullable|string|in:transactional,non-transactional",
            "description" => "nullable|string",
            "channel_id" => "nullable|integer",
            "group_id" => "nullable|integer",
        ];
    }
}

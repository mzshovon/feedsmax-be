<?php

namespace App\Http\Requests;

use App\Rules\UniqueEventNameForChannelRule;
use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class EventStoreRequest extends FormRequest
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
            "type" => "required|string",
            "event" => "required|string",
            "context" => "required|string|in:transactional,non-transactional",
            "description" => "nullable|string",
            "channel_id" => ["required", "integer", new UniqueEventNameForChannelRule($this->event)],
            "group_id" => "required|integer|exists:groups,id",
        ];
    }
}

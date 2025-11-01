<?php

namespace App\Http\Requests;

use App\Rules\UniqueEventNameForChannelRule;
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
            "type" => "nullable|string",
            "context" => "nullable|string|in:visitor,subscriber,rate",
            "description" => "nullable|string",
            "name" => "nullable|string",
            "channel_id" => [
                "required_with:name",
                "integer",
                new UniqueEventNameForChannelRule($this->name),
            ],
            "bucket_id" => "nullable|integer|exists:buckets,id",
            "client_id" => "nullable|integer|exists:clients,id",
        ];
    }
}

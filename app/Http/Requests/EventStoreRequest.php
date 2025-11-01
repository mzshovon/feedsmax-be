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
            "type" => "required|string",
            "name" => "required|string",
            "context" => "required|string|in:visitor,subscriber,rate",
            "description" => "nullable|string",
            "channel_id" => ["required", "integer", new UniqueEventNameForChannelRule($this->name)],
            "bucket_id" => "required|integer|exists:buckets,id",
            "client_id" => "required|integer|exists:clients,id",
        ];
    }
}

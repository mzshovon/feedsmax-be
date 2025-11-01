<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PolicyUpdateRequest extends FormRequest
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
            "id" => "required|integer|exists:quarantine_policies,id",
            "name" => "nullable|string",
            "call_object_notation" => [
                "nullable",
                "string",
                "unique:quarantine_policies,call_object_notation"
            ],
            "args" => "nullable|array",
            "definition" => "nullable|string",
            "status" => "nullable|integer|in:0,1",
            "args" => "nullable|array",
            "definition" => "nullable|string",
            "enabled" => "nullable|integer|in:0,1",
        ];
    }
}

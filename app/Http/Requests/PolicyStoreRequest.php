<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PolicyStoreRequest extends FormRequest
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
            "name" => "required|string",
            "call_object_notation" => [
                "required",
                "string",
                "unique:quarantine_policies,call_object_notation"
            ],
            "args" => "required|array",
            "definition" => "nullable|string",
            "status" => "required|integer|in:0,1",
        ];
    }
}

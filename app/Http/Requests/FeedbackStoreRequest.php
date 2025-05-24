<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class FeedbackStoreRequest extends FormRequest
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
            "attempt_id" => "required|integer|exists:attempts,id",
            "context" => "required|string|in:transactional,non_transactional",
            "feedback" => "required|array",
            "comment" => "nullable|string",
            "status" => "required|string",
        ];
    }
}

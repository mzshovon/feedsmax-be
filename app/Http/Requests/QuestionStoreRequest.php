<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class QuestionStoreRequest extends FormRequest
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
            "question_en" => "required|string",
            "question_another_lang" => "required|string",
            "field_type" => "required|string|in:".implode(",",getFieldTypes()),
            "options" => "nullable|array",
            "score_range" => "nullable|string",
            "parent_id" => "nullable|integer|exists:questionnaires,id",
            "order" => "required|integer",
            "status" => "required|integer|in:0,1",
            "required" => "nullable|boolean",
        ];
    }
}

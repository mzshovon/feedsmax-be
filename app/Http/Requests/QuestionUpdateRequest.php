<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class QuestionUpdateRequest extends FormRequest
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
            "question_en" => "nullable|string",
            "question_another_lang" => "nullable|string",
            "field_type" => "nullable|string|in:".implode(",",getFieldTypes()),
            "options" => "nullable|array",
            "score_range" => "nullable|string",
            "parent_id" => "nullable|integer|exists:questionnaires,id",
            "order" => "nullable|integer",
            "status" => "nullable|integer|in:0,1",
            "required" => "nullable|boolean",
        ];
    }
}

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
            "user_name" => "required|string|exists:vw_app_users,user_name",
            "question_en" => "nullable|string",
            "question_bn" => "nullable|string",
            "selection_type" => "nullable|string|in:".implode(",",getSelectionTypes()),
            "options" => "nullable|array",
            "range" => "nullable|string",
            "parent_id" => "nullable|integer|exists:questionnaires,id",
            "order" => "nullable|integer",
            "status" => "nullable|integer|in:0,1",
            "nps_rating_mapping" => "nullable|array",
            "is_required" => "nullable|boolean",
        ];
    }
}

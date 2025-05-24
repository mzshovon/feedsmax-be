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
            "user_name" => "required|string|exists:vw_app_users,user_name",
            "question_en" => "required|string",
            "question_bn" => "required|string",
            "selection_type" => "required|string|in:".implode(",",getSelectionTypes()),
            "options" => "nullable|array",
            "range" => "nullable|string",
            "parent_id" => "nullable|integer|exists:questionnaires,id",
            "order" => "required|integer",
            "status" => "required|integer|in:0,1",
            "nps_rating_mapping" => "nullable|array",
            "is_required" => "nullable|boolean",
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RuleAttachToEventRequest extends FormRequest
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
            "trigger_id" => "required|integer|exists:triggers,id",
            "rule" => [
                "required",
                "string",
                Rule::exists("rules", "func")->where(function($q){
                    return $q->where('trigger_id', 0);
                })
            ],
            "args" => "nullable|array",
        ];
    }
}

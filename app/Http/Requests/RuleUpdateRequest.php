<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RuleUpdateRequest extends FormRequest
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
            "id" => "required|integer|exists:rules,id",
            "func" => [
                "nullable",
                "string",
                Rule::unique("rules", "func")->where(function($q){
                    return $q->whereNot('trigger_id', 0)->whereNot('id', $this->id);
                })
            ],
            "args" => "nullable|array",
            "definition" => "nullable|string",
            "enabled" => "nullable|integer|in:0,1",
        ];
    }
}

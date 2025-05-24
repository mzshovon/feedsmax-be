<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class GroupStoreRequest extends FormRequest
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
            "name" => "required|string|unique:groups,name",
            "status" => "integer|in:0,1",
            "type" => "nullable|string|in:nps,NPS,csat,CSAT,ces,CES",
            "nps_ques_id" => "required|integer|exists:questionnaires,id",
            "promoter_range" => "required|string|min:1|max:10"
        ];
    }
}

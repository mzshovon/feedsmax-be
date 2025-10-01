<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class BucketUpdateRequest extends FormRequest
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
            // "user_name" => "required|string|exists:vw_app_users,user_name",
            "name" => "nullable|string|unique:buckets,name,".$this->id,
            "status" => "integer|in:0,1",
            "type" => "nullable|string|in:nps,NPS,csat,CSAT,ces,CES",
        ];
    }
}

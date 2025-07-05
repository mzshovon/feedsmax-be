<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientUpdateRequest extends FormRequest
{
    use FormValidationResponse;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $clientId = $this->route('clientId');
        
        return [
            "user_name" => "required|string|exists:vw_app_users,user_name",
            "company_tag" => ["required", "string", Rule::unique('clients', 'company_tag')->ignore($clientId)],
            "company_name" => "required|string|max:100",
            "contact_name" => "nullable|string|max:100",
            "email" => ["required", "email", Rule::unique('clients', 'email')->ignore($clientId)],
            "phone" => "nullable|string|max:13",
            "address" => "nullable|string",
            "client_key" => "required|string|max:200",
            "client_secret" => "required|string|max:200",
            "status" => "required|boolean",
        ];
    }
} 
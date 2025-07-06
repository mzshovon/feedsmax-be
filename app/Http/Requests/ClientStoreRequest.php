<?php

namespace App\Http\Requests;

use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class ClientStoreRequest extends FormRequest
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
            "company_tag" => "required|string|unique:clients,company_tag",
            "company_name" => "required|string|max:100",
            "contact_name" => "nullable|string|max:100",
            "email" => "required|email|unique:clients,email",
            "phone" => "nullable|string|max:13",
            "address" => "nullable|string",
            "client_key" => "required|string|max:200",
            "client_secret" => "required|string|max:200",
            "status" => "required|boolean",
        ];
    }
} 
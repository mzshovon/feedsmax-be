<?php

namespace App\Http\Requests;

use App\Enums\SentimentCategory;
use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class SentimentStoreRequest extends FormRequest
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
            "sentiment_category" => "required|string|in:".implode(",",array_column(SentimentCategory::cases(), 'value')),
            "keywords" => "required|array",
        ];
    }
}

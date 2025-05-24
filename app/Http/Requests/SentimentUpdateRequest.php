<?php

namespace App\Http\Requests;

use App\Enums\SentimentCategory;
use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class SentimentUpdateRequest extends FormRequest
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
            "sentiment_category" => "nullable|string|in:".implode(",",array_column(SentimentCategory::cases(), 'value')),
            "keywords" => "required_with:sentiment_category|string",
        ];
    }
}

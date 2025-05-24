<?php

namespace App\Http\Requests;

use App\Rules\IsRequiredCheckInResponseRule;
use App\Rules\ResponseFlatMaxCountRule;
use App\Traits\FormValidationResponse;
use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    use FormValidationResponse;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Code here ....
        return [];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => ['required', Password::defaults(), 'confirmed'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

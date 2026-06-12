<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email_or_phone' => ['required', 'string', 'max:255'],
            'password'       => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return ['email_or_phone' => 'email or phone number'];
    }
}

<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $level = $this->input('level');

        $rules = [
            // ── Core ──────────────────────────────────────────────────────
            'alumni_status'     => ['required', Rule::in(['Yes', 'No'])],
            'email'             => ['required', 'email', 'max:255', 'unique:users,email'],
            'first_name'        => ['required', 'string', 'max:100'],
            'last_name'         => ['required', 'string', 'max:100'],
            'middle_name'       => ['nullable', 'string', 'max:100'],
            'suffix'            => ['nullable', 'string', 'max:20'],
            'sex'               => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'civil_status'      => ['required', Rule::in(['Single', 'Married', 'Widowed', 'Separated', 'Other'])],
            'spouse_name'       => ['nullable', 'required_if:civil_status,Married', 'string', 'max:200'],
            'religion'          => ['required', Rule::in(['Catholic', 'Protestant', 'Iglesia ni Cristo', 'Islam', 'Seventh Day Adventist', "Jehovah's Witness", 'Other'])],
            'birthday'          => ['required', 'date', 'before:today'],
            'age'               => ['required', 'integer', 'min:5', 'max:120'],
            'home_municipality' => ['required', 'string', 'max:100'],
            'home_barangay'     => ['required', 'string', 'max:100'],
            'permanent_address' => ['nullable', 'string', 'max:255'],
            'phone'             => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'facebook_account'  => ['required', 'string', 'max:255'],
            'preferred_contact' => ['required', Rule::in(['Email', 'Phone', 'FB/Messenger', 'Other'])],
            'level'             => ['required', Rule::in(['Elementary', 'Junior High School', 'Senior High School', 'College'])],
            'program'           => ['required', 'string', 'max:50'],
            'highest_degree'    => ['required', 'string', 'max:100'],
            'graduation_year'   => ['required', 'integer', 'min:1941', 'max:' . date('Y')],
            'honors'            => ['nullable', 'string', 'max:255'],
            'board_exam'        => ['nullable', 'string', 'max:255'],
            'other_schools'     => ['nullable', 'string', 'max:1000'],
            'present_occupation'=> ['required', 'string', 'max:255'],
            'other_experiences' => ['nullable', 'string', 'max:1000'],
            'company_address'   => ['nullable', 'string', 'max:255'],
            'comments'          => ['nullable', 'string', 'max:2000'],
            'consent'           => ['required', Rule::in(['Yes'])],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
        ];

        // ── College-only fields ───────────────────────────────────────────
        if ($level === 'College') {
            $rules['academic_performance']  = ['required', Rule::in(['Excellent', 'Very Good', 'Good', 'Other'])];
            $rules['employment_status']     = ['required', Rule::in(['Employed', 'Unemployed', 'Other'])];
            $rules['time_to_first_job']     = ['required', 'string', 'max:100'];
            $rules['job_related']           = ['required', Rule::in(['Yes', 'No', 'Other'])];
            $rules['changes_needed']        = ['nullable', 'array'];
            $rules['changes_needed.*']      = ['string', 'max:100'];
            $rules['employment_type']       = ['nullable', 'required_if:employment_status,Employed',
                                               Rule::in(['Locally', 'Abroad', 'Self-employed', 'Other'])];
            $rules['unemployment_reason']   = ['nullable', 'required_if:employment_status,Unemployed', 'string', 'max:255'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'consent.in'             => 'You must consent to the data privacy policy to register.',
            'password.confirmed'     => 'Passwords do not match.',
            'spouse_name.required_if'=> 'Spouse name is required when civil status is Married.',
        ];
    }
}

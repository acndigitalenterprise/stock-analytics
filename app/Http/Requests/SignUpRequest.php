<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Form request for user sign up
 * 
 * Handles validation for new user registration with email verification
 */
class SignUpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-Z\s\.\-\']+$/', // Only letters, spaces, dots, hyphens, apostrophes
            ],
            'email' => [
                'required',
                'email:rfc', // Removed DNS validation to prevent server timeouts
                'max:255',
                'unique:users,email',
                'not_regex:/[<>\"\'&]/', // Prevent HTML injection
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'password_confirmation' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Full name is required.',
            'full_name.min' => 'Full name must be at least 2 characters.',
            'full_name.max' => 'Full name must not exceed 100 characters.',
            'full_name.regex' => 'Full name can only contain letters, spaces, dots, hyphens, and apostrophes.',
            
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address must not exceed 255 characters.',
            'email.unique' => 'Email Address Already Registered',
            'email.not_regex' => 'Email address contains invalid characters.',
            
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            
            'password_confirmation.required' => 'Password confirmation is required.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'full_name' => 'full name',
            'email' => 'email address',
            'password' => 'password',
            'password_confirmation' => 'password confirmation',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Sanitize and normalize data
            'full_name' => $this->sanitizeName($this->input('full_name')),
            'email' => strtolower(trim($this->input('email'))),
        ]);
    }

    /**
     * Sanitize full name input
     * 
     * @param string|null $name
     * @return string|null
     */
    private function sanitizeName(?string $name): ?string
    {
        if (empty($name)) {
            return $name;
        }

        // Remove extra spaces and convert to title case
        $name = trim(preg_replace('/\s+/', ' ', $name));
        
        // Basic XSS prevention
        $name = strip_tags($name);
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        
        return ucwords(strtolower($name));
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Log validation failures for security monitoring
        \Log::warning('Sign up validation failed', [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['password', 'password_confirmation']),
        ]);

        // Check if email validation failed and add specific message
        $errors = $validator->errors();
        
        if ($errors->has('email')) {
            // If email error exists, replace generic message with specific one
            if ($errors->first('email') === 'Email Address Already Registered') {
                $errors->add('signup_error', 'Email Address Already Registered');
            } else {
                $errors->add('signup_error', 'Please correct the errors below and try again.');
            }
        } else {
            $errors->add('signup_error', 'Please correct the errors below and try again.');
        }

        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
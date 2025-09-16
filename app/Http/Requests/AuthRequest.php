<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Form request for authentication operations
 * 
 * Handles validation for sign-in, sign-up, and password reset operations
 * with enhanced security measures.
 */
class AuthRequest extends FormRequest
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
        $action = $this->route()->getName();
        
        return match($action) {
            'auth.signin' => $this->signinRules(),
            'auth.signup' => $this->signupRules(),
            'auth.forgot' => $this->forgotPasswordRules(),
            'auth.reset' => $this->resetPasswordRules(),
            default => [],
        };
    }

    /**
     * Get validation rules for sign-in
     * 
     * @return array
     */
    private function signinRules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                'not_regex:/[<>\"\'&]/', // Prevent HTML injection
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:255',
            ],
        ];
    }

    /**
     * Get validation rules for sign-up
     * 
     * @return array
     */
    private function signupRules(): array
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
            'mobile_number' => [
                'required',
                'string',
                'regex:/^(\+62|62|08)\d{8,12}$/', // Indonesian mobile format
                'unique:users,mobile_number',
            ],
            'password' => [
                'sometimes',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed',
            ],
        ];
    }

    /**
     * Get validation rules for forgot password
     * 
     * @return array
     */
    private function forgotPasswordRules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                'exists:users,email',
                'not_regex:/[<>\"\'&]/', // Prevent HTML injection
            ],
        ];
    }

    /**
     * Get validation rules for password reset
     * 
     * @return array
     */
    private function resetPasswordRules(): array
    {
        return [
            'token' => [
                'required',
                'string',
                'size:60', // Laravel's default token length
            ],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                'exists:users,email',
                'not_regex:/[<>\"\'&]/', // Prevent HTML injection
            ],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed',
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
            'email.unique' => 'This email address is already registered.',
            'email.exists' => 'No account found with this email address.',
            'email.not_regex' => 'Email address contains invalid characters.',
            
            'mobile_number.required' => 'Mobile number is required.',
            'mobile_number.regex' => 'Please enter a valid Indonesian mobile number (e.g., +6281234567890 or 081234567890).',
            'mobile_number.unique' => 'This mobile number is already registered.',
            
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least :min characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.uncompromised' => 'The password has been compromised in a data breach. Please choose a different password.',
            
            'token.required' => 'Reset token is required.',
            'token.size' => 'Invalid reset token format.',
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
            'mobile_number' => 'mobile number',
            'email' => 'email address',
            'password' => 'password',
            'token' => 'reset token',
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
            'mobile_number' => $this->sanitizeMobileNumber($this->input('mobile_number')),
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
     * Sanitize mobile number input
     * 
     * @param string|null $mobile
     * @return string|null
     */
    private function sanitizeMobileNumber(?string $mobile): ?string
    {
        if (empty($mobile)) {
            return $mobile;
        }

        // Remove all non-numeric characters except + at the start
        $mobile = preg_replace('/[^\d+]/', '', $mobile);
        
        // Normalize Indonesian mobile numbers
        if (str_starts_with($mobile, '08')) {
            $mobile = '+62' . substr($mobile, 1);
        } elseif (str_starts_with($mobile, '62') && !str_starts_with($mobile, '+62')) {
            $mobile = '+' . $mobile;
        }
        
        return $mobile;
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
        \Log::warning('Auth validation failed', [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'action' => $this->route()->getName(),
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['password', 'password_confirmation', 'token']),
        ]);

        parent::failedValidation($validator);
    }
}
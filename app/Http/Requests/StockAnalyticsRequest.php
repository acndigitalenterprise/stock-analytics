<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form request for stock analytics submissions
 * 
 * Provides comprehensive validation for stock analysis requests
 * with security measures and sanitization.
 */
class StockAnalyticsRequest extends FormRequest
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
            'mobile_number' => [
                'nullable',
                'string',
                'regex:/^(\+62|62|08)\d{8,12}$/', // Indonesian mobile format
                'unique:users,mobile_number',
            ],
            'email' => [
                'required',
                'email:rfc', // Removed DNS validation to prevent server timeouts
                'max:255',
                'not_regex:/[<>\"\'&]/', // Prevent HTML injection
            ],
            'stock_code' => [
                'required',
                'string',
                'min:3',
                'max:10',
                'regex:/^[A-Z0-9\.]+$/', // Only uppercase letters, numbers, and dots
            ],
            'timeframe' => [
                'required',
                Rule::in(['1h', '1d']),
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
            
            'mobile_number.regex' => 'Please enter a valid Indonesian mobile number (e.g., +6281234567890 or 081234567890).',
            'mobile_number.unique' => 'This mobile number is already registered.',
            
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address must not exceed 255 characters.',
            'email.not_regex' => 'Email address contains invalid characters.',
            
            'stock_code.required' => 'Stock code is required.',
            'stock_code.min' => 'Stock code must be at least 3 characters.',
            'stock_code.max' => 'Stock code must not exceed 10 characters.',
            'stock_code.regex' => 'Stock code can only contain uppercase letters, numbers, and dots.',
            
            'timeframe.required' => 'Timeframe is required.',
            'timeframe.in' => 'Timeframe must be either 1h or 1d.',
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
            'stock_code' => 'stock code',
            'timeframe' => 'timeframe',
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
            'mobile_number' => $this->sanitizeMobileNumber($this->input('mobile_number')),
            'email' => strtolower(trim($this->input('email'))),
            'stock_code' => strtoupper(trim($this->input('stock_code'))),
            'timeframe' => strtolower(trim($this->input('timeframe'))),
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
        \Log::warning('Stock analytics validation failed', [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'errors' => $validator->errors()->toArray(),
            'input' => $this->except(['password', 'password_confirmation']),
        ]);

        parent::failedValidation($validator);
    }
}
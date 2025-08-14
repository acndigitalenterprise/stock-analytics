<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function signin(Request $request)
    {
        try {
            \Log::info('Sign-in attempt', [
                'email' => $request->email,
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);

            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if ($user && Hash::check($validated['password'], $user->password)) {
                // Clear any existing session data first
                session()->flush();
                session()->regenerate();
                
                // Refresh user data from database to ensure latest info
                $freshUser = User::find($user->id);
                
                // Set fresh user session
                session(['user' => $freshUser]);
                
                // Debug log
                \Log::info('User logged in successfully:', [
                    'email' => $freshUser->email,
                    'role' => $freshUser->role,
                    'id' => $freshUser->id
                ]);
                
                return redirect()->route('stock-analytics.admin');
            }

            \Log::warning('Sign-in failed: Invalid credentials', [
                'email' => $validated['email'],
                'user_found' => $user ? 'yes' : 'no'
            ]);

            return redirect()->back()->withErrors(['signin_error' => 'Incorrect username or password'])->withInput(['email' => $validated['email']]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Sign-in validation failed', [
                'email' => $request->email,
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Sign-in error', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['signin_error' => 'An error occurred during sign-in. Please try again.'])->withInput(['email' => $request->email]);
        }
    }

    public function signup(Request $request)
    {
        // Check if email already exists
        $emailExists = User::where('email', $request->email)->exists();
        $mobileExists = User::where('mobile_number', $request->mobile_number)->exists();
        
        $errors = [];
        if ($emailExists) {
            $errors['email'] = 'Email Address already registered';
        }
        if ($mobileExists) {
            $errors['mobile_number'] = 'Mobile Number already registered';
        }
        
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email',
        ]);

        $password = Str::random(10);

        $user = User::create([
            'name' => $validated['full_name'],
            'mobile_number' => $validated['mobile_number'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role' => 'user',
        ]);

        // Send email with login info
        $this->sendSignupEmail($user, $password);

        return redirect()->route('stock-analytics.registration-success');
    }

    public function logout(Request $request)
    {
        // Completely clear session
        session()->flush();
        session()->regenerate();
        
        return redirect()->route('stock-analytics.index');
    }

    private function sendSignupEmail($user, $password)
    {
        $data = [
            'user' => $user,
            'password' => $password,
        ];

        Mail::send('emails.signup', $data, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Welcome to Stock Analytics');
        });
    }

    public function showForgotPassword()
    {
        return view('forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email address not found in our system. Please check the email address or sign up for a new account.');
        }

        // Generate reset token
        $token = Str::random(64);
        
        // Store token in database (you might want to create a password_resets table)
        // For now, we'll store it in the user's remember_token field
        $user->update(['remember_token' => $token]);

        // Send reset email
        $this->sendResetPasswordEmail($user, $token);

        return redirect()->back()->with('success', 'Password reset link has been sent to your email. Please check your inbox and follow the instructions.');
    }

    public function showResetPassword($token)
    {
        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return redirect()->route('stock-analytics.index')->with('error', 'Invalid or expired reset token. Please request a new password reset link.');
        }

        return view('reset-password', compact('token'));
    }

    public function resetPassword(Request $request)
    {
        // Debug: Log the request data
        \Log::info('Reset password request', [
            'token' => $request->input('token'),
            'has_password' => $request->has('password'),
            'has_password_confirmation' => $request->has('password_confirmation'),
        ]);

        $validated = $request->validate([
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $user = User::where('remember_token', $validated['token'])->first();

        if (!$user) {
            \Log::warning('Invalid reset token', ['token' => $validated['token']]);
            return redirect()->back()->with('error', 'Invalid or expired reset token. Please request a new password reset link.');
        }

        // Check if new password is different from old password
        if (Hash::check($validated['password'], $user->password)) {
            return redirect()->back()->with('error', 'New password must be different from your current password. Please choose a different password.');
        }

        // Update password and clear token
        $user->update([
            'password' => Hash::make($validated['password']),
            'remember_token' => null,
        ]);

        return redirect()->route('stock-analytics.index')->with('success', 'Password reset successfully! You can now sign in with your new password.');
    }

    private function sendResetPasswordEmail($user, $token)
    {
        $data = [
            'user' => $user,
            'token' => $token,
            'resetUrl' => route('stock-analytics.reset-password', $token),
        ];

        Mail::send('emails.reset-password', $data, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Reset Your Password - Stock Analytics');
        });
    }
}

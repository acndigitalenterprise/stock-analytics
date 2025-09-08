<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SettingsController extends Controller
{
    public function __construct()
    {
        \Log::info('🔧 SettingsController CONSTRUCTOR called');
    }
    
    public function profile(Request $request)
    {
        \Log::info('🟢 GET /settings - profile method called');
        \Log::info('HIT /settings');
        
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect()->route('auth.signin.page');
        }
        $user = User::find($user->id);
        return view('Settings.settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        \Log::info('🔴🔴🔴 POST /settings - updateProfile method called!!! 🔴🔴🔴');
        \Log::info('🚀🚀🚀 SETTINGS updateProfile METHOD CALLED! 🚀🚀🚀');
        \Log::info('🚀 SETTINGS UPDATE POST REQUEST RECEIVED!');
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request URL: ' . $request->url());
        \Log::info('Request all data:', $request->all());
        
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect()->route('auth.signin.page');
        }
        $user = User::find($user->id);
        
        // Debug: Check if form data exists
        if (empty($request->except(['_token']))) {
            \Log::error('No form data received (except CSRF token)');
            return back()->with('error', 'No form data received');
        }
        
        // Handle profile information update
        \Log::info('🔍 Settings validation starting');
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'mobile_number' => 'nullable|string|max:20|unique:users,mobile_number,' . $user->id,
                'new_password' => 'nullable|string|min:6', // Changed from min:8 to min:6 for consistency
            ], [
                'email.unique' => 'Email Address Already Registered',
                'mobile_number.unique' => 'Mobile Number Already Registered',
                'new_password.min' => 'Password must be at least 6 characters long',
            ]);
            \Log::info('✅ Settings validation passed', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Settings validation FAILED', ['errors' => $e->errors()]);
            return back()
                ->withErrors($e->errors())
                ->withInput();
        }
        
        // Update user data
        $user->name = $validated['full_name'];
        $user->email = $validated['email'];
        $user->mobile_number = !empty(trim($validated['mobile_number'] ?? '')) ? trim($validated['mobile_number']) : null;
        
        // Handle password change if provided
        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }
        
        $saveResult = $user->save();
        \Log::info('💾 User save result:', ['success' => $saveResult, 'user_id' => $user->id]);
        
        // Update session with fresh user data
        $request->session()->put('user', $user->fresh());
        
        // Update all requests for this user to reflect new profile data
        $requestUpdateCount = \App\Models\Request::where('user_id', $user->id)->update([
            'full_name' => $user->name,
            'email' => $user->email,
            'mobile_number' => $user->mobile_number ?? null,
        ]);
        \Log::info('📝 Updated requests count:', ['count' => $requestUpdateCount]);
        
        $message = 'Profile updated successfully.';
        if (!empty($validated['new_password'])) {
            $message .= ' Password changed successfully.';
        }
        
        // Check if request expects JSON (AJAX request)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return back()->with('success', $message);
    }
}
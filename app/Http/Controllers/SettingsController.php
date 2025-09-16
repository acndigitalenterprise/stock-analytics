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
    
    // DEBUG METHOD - Remove after fixing
    public function debugForm(Request $request)
    {
        \Log::info('🐛 DEBUG FORM DATA', [
            'method' => $request->method(),
            'all_data' => $request->all(),
            'except_token' => $request->except(['_token']),
            'headers' => $request->headers->all(),
            'user_session' => session('user')->id ?? 'no session'
        ]);
        
        return response()->json([
            'success' => true,
            'data_received' => $request->all(),
            'form_data' => $request->except(['_token'])
        ]);
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
        try {
            \Log::info('🔧 Settings Update Started', [
                'user_id' => session('user')->id ?? 'unknown',
                'method' => $request->method(),
                'url' => $request->url(),
                'data_keys' => array_keys($request->except(['_token', 'debug_timestamp'])),
                'has_data' => !empty($request->except(['_token', 'debug_timestamp']))
            ]);
            
            // Check authentication
            $sessionUser = $request->session()->get('user');
            if (!$sessionUser) {
                \Log::warning('Settings update attempted without session');
                return redirect()->route('auth.signin.page')->with('error', 'Please login first');
            }
            
            // Get fresh user data from database
            $user = User::find($sessionUser->id);
            if (!$user) {
                \Log::error('User not found in database', ['user_id' => $sessionUser->id]);
                return redirect()->route('auth.signin.page')->with('error', 'User account not found');
            }
            
            // Verify user can only update their own profile
            if ($user->id !== $sessionUser->id) {
                \Log::warning('Unauthorized profile update attempt', [
                    'session_user_id' => $sessionUser->id,
                    'target_user_id' => $user->id
                ]);
                return redirect()->route('settings')->with('error', 'Unauthorized access');
            }
            
            // Check if form data exists
            $formData = $request->except(['_token', 'debug_timestamp']);
            if (empty($formData)) {
                \Log::error('No form data received', ['all_data' => $request->all()]);
                return redirect()->route('settings')->with('error', 'No form data received. Please try again.');
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
            return redirect()->route('settings')
                ->withErrors($e->errors())
                ->withInput();
        }
        
            // Update user data
            \Log::info('💾 Updating user data');
            $user->name = $validated['full_name'];
            $user->email = $validated['email'];
            $user->mobile_number = !empty(trim($validated['mobile_number'] ?? '')) ? trim($validated['mobile_number']) : null;
            
            // Handle password change if provided
            if (!empty($validated['new_password'])) {
                \Log::info('🔐 Password change requested');
                $user->password = Hash::make($validated['new_password']);
            }
            
            $saveResult = $user->save();
            \Log::info('✅ User data saved successfully', [
                'user_id' => $user->id,
                'save_result' => $saveResult,
                'updated_fields' => ['name', 'email', 'mobile_number', !empty($validated['new_password']) ? 'password' : null]
            ]);
            
            // Update session with fresh user data
            $request->session()->put('user', $user->fresh());
        
        // Update all requests for this user to reflect new profile data
        $requestUpdateCount = \App\Models\Request::where('user_id', $user->id)->update([
            'full_name' => $user->name,
            'email' => $user->email,
            'mobile_number' => $user->mobile_number ?? '',
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

            return redirect()->route('settings')->with('success', $message);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Validation failed', ['errors' => $e->errors()]);
            return redirect()->route('settings')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('💥 Settings update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => session('user')->id ?? 'unknown'
            ]);
            
            return redirect()->route('settings')
                ->with('error', 'Failed to update profile: ' . $e->getMessage())
                ->withInput();
        }
    }
}
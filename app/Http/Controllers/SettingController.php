<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SettingController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect()->route('auth.signin.page');
        }
        $user = User::find($user->id);
        return view('Settings.settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect()->route('auth.signin.page');
        }
        $user = User::find($user->id);
        
        // Validate using same pattern as AdminController@updateUser
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|string|max:20',
            'new_password' => 'nullable|string|min:6',
        ], [
            'email.unique' => 'Email Address Already Registered',
            'mobile_number.unique' => 'Mobile Number Already Registered',
        ]);

        // Handle password field (same pattern as AdminController)
        if (!empty($validated['new_password'])) {
            $validated['password'] = bcrypt($validated['new_password']);
            unset($validated['new_password']);
        }

        // Map full_name to name field and handle nullable mobile_number (same pattern as AdminController)
        $validated['name'] = $validated['full_name'];
        $validated['mobile_number'] = $validated['mobile_number'] ?? null;
        unset($validated['full_name']);

        $user->update($validated);
        
        // Update session
        $request->session()->put('user', $user);
        
        // Update all requests for this user to reflect new profile data
        \App\Models\Request::where('user_id', $user->id)->update([
            'full_name' => $user->name,
            'email' => $user->email,
            'mobile_number' => $user->mobile_number ?? null,
        ]);
        
        $message = 'Profile updated successfully.';
        if ($request->filled('new_password')) {
            $message .= ' Password changed successfully.';
        }
        
        return back()->with('success', $message);
    }
}
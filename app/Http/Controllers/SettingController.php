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
            return redirect()->route('stock-analytics.signin');
        }
        $user = User::find($user->id);
        return view('setting.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->session()->get('user');
        if (!$user) {
            return redirect()->route('stock-analytics.signin');
        }
        $user = User::find($user->id);
        
        // Handle profile information update
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|string|max:20|unique:users,mobile_number,' . $user->id,
        ], [
            'email.unique' => 'Email Address Already Registered',
            'mobile_number.unique' => 'Mobile Number Already Registered',
        ]);
        
        $user->name = $validated['full_name'];
        $user->email = $validated['email'];
        $user->mobile_number = $validated['mobile_number'] ?? null;
        
        // Handle password change if provided
        if ($request->filled('new_password')) {
            $passwordValidated = $request->validate([
                'new_password' => 'required|min:8',
            ]);
            
            $user->password = Hash::make($passwordValidated['new_password']);
        }
        
        $user->save();
        
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

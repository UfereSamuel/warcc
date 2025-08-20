<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Staff;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the staff login page (Microsoft SSO)
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Redirect to Microsoft SSO
     */
    public function redirectToMicrosoft()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    /**
     * Handle Microsoft SSO callback
     */
    public function handleMicrosoftCallback()
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();

            // Debug: Log the Microsoft user details
            \Log::info('Microsoft User Details', [
                'id' => $microsoftUser->id,
                'email' => $microsoftUser->email,
                'name' => $microsoftUser->name
            ]);

            // Find staff by Microsoft ID or email
            $staff = Staff::where('microsoft_id', $microsoftUser->id)
                         ->orWhere('email', $microsoftUser->email)
                         ->first();

            if (!$staff) {
                return redirect()->route('auth.login')
                    ->with('error', 'Your account is not registered in the system. Please contact the administrator.');
            }

            // Update Microsoft ID if not set
            if (!$staff->microsoft_id) {
                $staff->microsoft_id = $microsoftUser->id;
                $staff->save();
            }

            // Update last login
            $staff->last_login = now();
            $staff->save();

            // Login the staff member
            Auth::guard('staff')->login($staff);

            // Redirect based on admin status
            if ($staff->is_admin) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Welcome back, ' . $staff->full_name . '!');
            } else {
                return redirect()->intended(route('staff.dashboard'))
                    ->with('success', 'Welcome back, ' . $staff->full_name . '!');
            }

        } catch (\Exception $e) {
            return redirect()->route('auth.login')
                ->with('error', 'Authentication failed. Please try again.');
        }
    }

    /**
     * Show the super admin login page
     */
    public function showAdminLogin()
    {
        return view('auth.admin-login');
    }

    /**
     * Handle super admin login
     */
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Check if this is the super admin
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Find corresponding staff record
        $staff = Staff::where('email', $request->email)->first();

        if (!$staff || !$staff->is_admin) {
            return back()->withErrors([
                'email' => 'You do not have administrative privileges.',
            ])->onlyInput('email');
        }

        // Update last login
        $staff->last_login = now();
        $staff->save();

        // Login the staff member
        Auth::guard('staff')->login($staff);

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Welcome back, Administrator!');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $staffName = Auth::guard('staff')->user()->full_name ?? 'User';

        Auth::guard('staff')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Goodbye, ' . $staffName . '. You have been logged out successfully.');
    }
}

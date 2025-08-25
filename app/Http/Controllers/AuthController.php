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
            $staff = Staff::with('position')->where('microsoft_id', $microsoftUser->id)
                         ->orWhere('email', $microsoftUser->email)
                         ->first();

            if (!$staff) {
                // Auto-register new staff from Microsoft SSO
                $staff = $this->createStaffFromMicrosoftUser($microsoftUser);
                
                // Log new staff registration
                \Log::info('New staff auto-registered from Microsoft SSO', [
                    'email' => $microsoftUser->email,
                    'name' => $microsoftUser->name,
                    'staff_id' => $staff->staff_id
                ]);
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

            // Check if profile needs completion
            if ($this->requiresProfileCompletion($staff)) {
                return redirect()->route('staff.profile.complete')
                    ->with('info', 'Welcome! Please complete your profile to access the system.');
            }

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
                ->with('error', 'Authentication failed:' .$e->getMessage());
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
        $staff = Staff::with('position')->where('email', $request->email)->first();

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

    /**
     * Create a new staff record from Microsoft SSO user data
     */
    private function createStaffFromMicrosoftUser($microsoftUser)
    {
        // Parse name parts
        $nameParts = explode(' ', trim($microsoftUser->name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Generate unique staff ID
        $staffId = $this->generateUniqueStaffId();

        // Create new staff record with minimal required data
        $staff = Staff::create([
            'staff_id' => $staffId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $microsoftUser->email,
            'microsoft_id' => $microsoftUser->id,
            'position_id' => null, // Will be set during profile completion
            'hire_date' => now()->toDateString(),
            'status' => 'active',
            'is_admin' => false,
            'annual_leave_balance' => 28,
        ]);

        return $staff;
    }

    /**
     * Generate a unique staff ID
     */
    private function generateUniqueStaffId()
    {
        do {
            // Generate format: RCC-XXX (where XXX is a 3-digit number)
            $number = str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
            $staffId = 'RCC-' . $number;
        } while (Staff::where('staff_id', $staffId)->exists());

        return $staffId;
    }

    /**
     * Check if staff profile requires completion
     */
    private function requiresProfileCompletion(Staff $staff)
    {
        // Check if any required fields are missing or have default values
        $requiredFields = [
            'position_id' => [null, ''],
            'phone' => [null, ''],
            'gender' => [null, ''],
        ];

        foreach ($requiredFields as $field => $invalidValues) {
            if (in_array($staff->$field, $invalidValues)) {
                return true;
            }
        }

        return false;
    }
}

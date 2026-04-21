<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Check if this is the first user registration
        $isFirstUser = !User::hasUsers();
        
        return view('auth.register', compact('isFirstUser'));
    }

    public function complete(): View
    {
        return view('auth.register-complete');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $isFirstUser = !User::hasUsers();
        
        $rules = [
            'fname' => ['required', 'string', 'max:255'],
            'mname' => ['nullable', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        // Only require role selection if not the first user
        if (!$isFirstUser) {
            $rules['role'] = ['required', 'in:'.implode(',', User::APPROVABLE_ROLES)];
        }

        $request->validate($rules);

        // Build full name
        $fullName = $request->fname;
        if ($request->mname) {
            $fullName .= ' ' . $request->mname;
        }
        $fullName .= ' ' . $request->lname;

        // Determine role and activation status
        if ($isFirstUser) {
            $role = User::ROLE_ADMIN;
            $isActive = true;
            $isFirstUserFlag = true;
            $approvedAt = now();
        } else {
            $role = $request->role;
            $isActive = false; // Needs approval
            $isFirstUserFlag = false;
            $approvedAt = null;
        }

        $user = User::create([
            'name' => $fullName,
            'fname' => $request->fname,
            'mname' => $request->mname,
            'lname' => $request->lname,
            'dob' => $request->dob,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'is_active' => $isActive,
            'is_first_user' => $isFirstUserFlag,
            'approved_at' => $approvedAt,
        ]);

        event(new Registered($user));
        $request->session()->regenerateToken();

        // Only login if user is active (first user only)
        if ($isActive) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect(RouteServiceProvider::HOME)->with('status', 'Welcome! You have been registered as the system administrator.');
        }

        return redirect()->route('register.complete')->with('status', 'Your registration has been submitted successfully and is pending approval. You can sign in once an administrator activates your account.');
    }
}

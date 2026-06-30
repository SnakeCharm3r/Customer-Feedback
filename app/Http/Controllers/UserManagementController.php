<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display list of all users
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::with('approvedBy')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('fname', 'like', "%{$s}%")
                  ->orWhere('lname', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_active', false)->where('is_first_user', false);
            }
        }

        $users = $query->paginate(20)->withQueryString();

        $stats = [
            'total'   => User::count(),
            'active'  => User::where('is_active', true)->count(),
            'pending' => User::where('is_active', false)->where('is_first_user', false)->count(),
            'admins'  => User::whereIn('role', ['admin', 'qa_hod'])->count(),
        ];

        $pendingUsers = collect();
        $pendingCount = 0;

        if (Auth::user()?->canManageUsers()) {
            $pendingQuery = User::where('is_active', false)
                ->where('is_first_user', false)
                ->orderBy('created_at', 'asc');

            $pendingCount = (clone $pendingQuery)->count();
            $pendingUsers = $pendingQuery->limit(5)->get();
        }

        return view('users.index', compact('users', 'pendingUsers', 'pendingCount', 'stats'));
    }

    /**
     * Display pending approval users
     */
    public function pending(): View
    {
        $this->authorize('approve', User::class);

        $pendingUsers = User::where('is_active', false)
            ->where('is_first_user', false)
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('users.pending', compact('pendingUsers'));
    }

    /**
     * Show user details
     */
    public function show(User $user): View
    {
        $this->authorize('view', $user);

        return view('users.show', compact('user'));
    }

    /**
     * Show user edit form
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $departments = \Illuminate\Support\Facades\DB::table('departments')->orderBy('name')->get();
        
        // Get all unique roles from users table to support custom roles
        $allRoles = User::select('role')->distinct()->pluck('role')->filter()->toArray();
        
        // Merge with predefined approvable roles (use array_values to get the role strings, not keys)
        $roles = array_unique(array_merge($allRoles, array_values(User::APPROVABLE_ROLES)));

        return view('users.edit', compact('user', 'departments', 'roles'));
    }

    /**
     * Update user details
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        // Cannot update first user's email or role
        if ($user->is_first_user) {
            return back()->with('error', 'Cannot modify the system administrator\'s details.');
        }

        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'mname' => ['nullable', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'dob' => ['nullable', 'date'],
            'role' => ['required', 'string'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_active' => ['boolean'],
        ]);

        $user->update($validated);

        return redirect()->route('users.show', $user)
            ->with('status', "User {$user->getFullName()} has been updated successfully.");
    }

    /**
     * Approve a user and assign role
     */
    public function approve(Request $request, User $user): RedirectResponse
    {
        $this->authorize('approve', $user);

        // Cannot approve first user (already approved) or already active users
        if ($user->is_first_user || $user->is_active) {
            return back()->with('error', 'This user cannot be approved.');
        }

        $validated = $request->validate([
            'role' => ['required', 'in:' . implode(',', User::APPROVABLE_ROLES)],
        ]);

        $user->update([
            'role' => $validated['role'],
            'is_active' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('users.pending')
            ->with('status', "User {$user->getFullName()} has been approved as {$user->getRoleLabel()}.");
    }

    /**
     * Reject/Deactivate a user
     */
    public function deactivate(User $user): RedirectResponse
    {
        $this->authorize('deactivate', $user);

        // Cannot deactivate first user
        if ($user->is_first_user) {
            return back()->with('error', 'The system administrator cannot be deactivated.');
        }

        $user->update([
            'is_active' => false,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return redirect()->route('users.index')
            ->with('status', "User {$user->getFullName()} has been deactivated.");
    }

    /**
     * Activate a previously deactivated user
     */
    public function activate(User $user): RedirectResponse
    {
        $this->authorize('activate', $user);

        if ($user->is_active) {
            return back()->with('error', 'User is already active.');
        }

        $user->update([
            'is_active' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('status', "User {$user->getFullName()} has been activated.");
    }

    /**
     * Change user role
     */
    public function changeRole(Request $request, User $user): RedirectResponse
    {
        $this->authorize('changeRole', $user);

        // Cannot change first user's role
        if ($user->is_first_user) {
            return back()->with('error', 'Cannot change the system administrator\'s role.');
        }

        $validated = $request->validate([
            'role' => ['required', 'in:' . implode(',', User::APPROVABLE_ROLES)],
        ]);

        $oldRole = $user->getRoleLabel();
        $user->update(['role' => $validated['role']]);

        return redirect()->route('users.index')
            ->with('status', "User {$user->getFullName()} role changed from {$oldRole} to {$user->getRoleLabel()}.");
    }

    /**
     * Send password reset link to an existing user (used as invitation)
     */
    public function sendPasswordReset(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        // Cannot send reset to first user (they should know their password)
        if ($user->is_first_user) {
            \Log::warning('Attempted to send password reset to system administrator', [
                'user_id' => $user->id,
                'email' => $user->email,
                'attempted_by' => Auth::id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Cannot send password reset to the system administrator.'
            ], 403);
        }

        try {
            // Use Laravel's Password::sendResetLink which handles token generation and storage
            $status = \Illuminate\Support\Facades\Password::sendResetLink(['email' => $user->email]);

            if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
                \Log::info('Password reset invitation sent successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'sent_by' => Auth::id(),
                ]);
                return response()->json([
                    'success' => true,
                    'message' => "Invitation sent to {$user->email}. They can now set their password."
                ]);
            }

            // Log the actual failure reason
            $errorMessage = \Illuminate\Support\Facades\Password::getStatus($status);
            \Log::error('Failed to send password reset invitation', [
                'user_id' => $user->id,
                'email' => $user->email,
                'status' => $status,
                'error_message' => $errorMessage,
                'sent_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => "Failed to send invitation: {$errorMessage}",
                'debug_status' => $status,
            ], 500);

        } catch (\Exception $e) {
            // Log any exceptions during the process
            \Log::error('Exception while sending password reset invitation', [
                'user_id' => $user->id,
                'email' => $user->email,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'sent_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send invitation. Please check server logs for details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

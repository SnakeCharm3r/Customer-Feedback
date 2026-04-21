<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('approvedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $pendingUsers = collect();
        $pendingCount = 0;

        if (Auth::user()?->canManageUsers()) {
            $pendingQuery = User::where('is_active', false)
                ->where('is_first_user', false)
                ->orderBy('created_at', 'asc');

            $pendingCount = (clone $pendingQuery)->count();
            $pendingUsers = $pendingQuery->limit(5)->get();
        }

        return view('users.index', compact('users', 'pendingUsers', 'pendingCount'));
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
}

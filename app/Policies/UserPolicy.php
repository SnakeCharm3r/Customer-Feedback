<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->canManageUsers();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view their own profile
        // Admins and QA HOD can view all users
        return $user->id === $model->id || $user->canManageUsers();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->canManageUsers();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Cannot modify first user except by themselves
        if ($model->is_first_user && $user->id !== $model->id) {
            return false;
        }

        return $user->canManageUsers() || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete first user
        if ($model->is_first_user) {
            return false;
        }

        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }

        return $user->canManageUsers();
    }

    /**
     * Determine whether the user can approve new users.
     */
    public function approve(User $user): bool
    {
        return $user->canApproveUsers();
    }

    /**
     * Determine whether the user can deactivate users.
     */
    public function deactivate(User $user, User $model): bool
    {
        // Cannot deactivate first user
        if ($model->is_first_user) {
            return false;
        }

        // Cannot deactivate yourself
        if ($user->id === $model->id) {
            return false;
        }

        return $user->canActivateUsers();
    }

    /**
     * Determine whether the user can activate users.
     */
    public function activate(User $user, User $model): bool
    {
        return $user->canActivateUsers();
    }

    /**
     * Determine whether the user can change user roles.
     */
    public function changeRole(User $user, User $model): bool
    {
        // Cannot change first user's role
        if ($model->is_first_user) {
            return false;
        }

        // Only admin can assign admin role
        if (!$user->isAdmin()) {
            return false;
        }

        return $user->canManageUsers();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->canManageUsers();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Cannot delete first user
        if ($model->is_first_user) {
            return false;
        }

        return $user->isAdmin();
    }
}

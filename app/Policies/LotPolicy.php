<?php

namespace App\Policies;

use App\Models\Lot;
use App\Models\User;

class LotPolicy
{
    /**
     * Determine if the user can view any lots
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine if the user can view the lot
     */
    public function view(User $user, Lot $lot): bool
    {
        if (!$user->is_active) return false;

        // Admin can view all
        if ($user->role === 'admin') return true;

        // District users can only view their district
        if ($user->role === 'district_user') {
            return $user->tuman_id === $lot->tuman_id;
        }

        // Viewers can view all
        return true;
    }

    /**
     * Determine if the user can create lots
     */
    public function create(User $user): bool
    {
        return $user->is_active && in_array($user->role, ['admin']);
    }

    /**
     * Determine if the user can update the lot
     */
    public function update(User $user, Lot $lot): bool
    {
        if (!$user->is_active) return false;

        // Admin can update all
        if ($user->role === 'admin') return true;

        // District users can only update their district
        if ($user->role === 'district_user') {
            return $user->tuman_id === $lot->tuman_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the lot
     */
    public function delete(User $user, Lot $lot): bool
    {
        return $user->is_active && $user->role === 'admin';
    }

    /**
     * Determine if the user can restore the lot
     */
    public function restore(User $user, Lot $lot): bool
    {
        return $user->is_active && $user->role === 'admin';
    }

    /**
     * Determine if the user can permanently delete the lot
     */
    public function forceDelete(User $user, Lot $lot): bool
    {
        return $user->is_active && $user->role === 'admin';
    }
}

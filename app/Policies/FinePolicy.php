<?php

namespace App\Policies;

use App\Models\Fine;
use App\Models\User;

class FinePolicy
{
    /**
     * Determine whether the user can view the fine.
     */
    public function view(User $user, Fine $fine): bool
    {
        // Owner or staff can view
        return $user->id === $fine->user_id 
            || $user->hasRole(['admin', 'librarian']);
    }

    /**
     * Determine whether the user can pay the fine.
     */
    public function pay(User $user, Fine $fine): bool
    {
        // Owner can initiate payment, or staff can confirm
        return $user->id === $fine->user_id 
            || $user->hasRole(['admin', 'librarian']);
    }

    /**
     * Determine whether the user can waive the fine.
     */
    public function waive(User $user, Fine $fine): bool
    {
        // Only staff can waive
        return $user->hasRole(['admin', 'librarian']);
    }
}

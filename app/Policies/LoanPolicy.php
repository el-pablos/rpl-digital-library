<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    /**
     * Determine whether the user can view any loans.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the loan.
     */
    public function view(User $user, Loan $loan): bool
    {
        // Owner or staff can view
        return $user->id === $loan->user_id 
            || $user->hasRole(['admin', 'librarian']);
    }

    /**
     * Determine whether the user can cancel the loan.
     */
    public function cancel(User $user, Loan $loan): bool
    {
        // Only owner can cancel, and only if in requested status
        return $user->id === $loan->user_id 
            && $loan->status === Loan::STATUS_REQUESTED;
    }

    /**
     * Determine whether the user can renew the loan.
     */
    public function renew(User $user, Loan $loan): bool
    {
        // Only owner can renew
        return $user->id === $loan->user_id 
            && $loan->canRenew();
    }

    /**
     * Determine whether the user can approve/reject the loan.
     */
    public function manage(User $user, Loan $loan): bool
    {
        return $user->hasRole(['admin', 'librarian']);
    }
}

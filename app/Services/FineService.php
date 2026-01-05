<?php

namespace App\Services;

use App\Models\Fine;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FineService
{
    /**
     * Pay a fine.
     */
    public function payFine(Fine $fine): Fine
    {
        if ($fine->isPaid()) {
            throw new \Exception('Denda ini sudah dibayar.');
        }

        if ($fine->isWaived()) {
            throw new \Exception('Denda ini sudah dihapuskan.');
        }

        $fine->markAsPaid();

        return $fine->fresh();
    }

    /**
     * Waive a fine.
     */
    public function waiveFine(Fine $fine, string $reason = null): Fine
    {
        if ($fine->isPaid()) {
            throw new \Exception('Denda yang sudah dibayar tidak dapat dihapuskan.');
        }

        if ($fine->isWaived()) {
            throw new \Exception('Denda ini sudah dihapuskan.');
        }

        $fine->waive();

        if ($reason) {
            $fine->update(['reason' => $fine->reason . ' | Dihapuskan: ' . $reason]);
        }

        return $fine->fresh();
    }

    /**
     * Get unpaid fines for a user.
     */
    public function getUnpaidFines(User $user): Collection
    {
        return Fine::where('user_id', $user->id)
            ->where('status', Fine::STATUS_UNPAID)
            ->with('loan.book')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get total unpaid fines for a user.
     */
    public function getTotalUnpaidFines(User $user): int
    {
        return (int) Fine::where('user_id', $user->id)
            ->where('status', Fine::STATUS_UNPAID)
            ->sum('amount');
    }

    /**
     * Check if user has unpaid fines.
     */
    public function hasUnpaidFines(User $user): bool
    {
        return Fine::where('user_id', $user->id)
            ->where('status', Fine::STATUS_UNPAID)
            ->exists();
    }

    /**
     * Pay all fines for a user.
     */
    public function payAllFines(User $user): int
    {
        return DB::transaction(function () use ($user) {
            $unpaidFines = $this->getUnpaidFines($user);
            
            foreach ($unpaidFines as $fine) {
                $fine->markAsPaid();
            }

            return $unpaidFines->count();
        });
    }

    /**
     * Get fine statistics for dashboard.
     */
    public function getFineStatistics(): array
    {
        return [
            'total_unpaid' => (int) Fine::where('status', Fine::STATUS_UNPAID)->sum('amount'),
            'total_paid' => (int) Fine::where('status', Fine::STATUS_PAID)->sum('amount'),
            'total_unpaid_count' => Fine::where('status', Fine::STATUS_UNPAID)->count(),
            'total_paid_count' => Fine::where('status', Fine::STATUS_PAID)->count(),
            'paid_today' => Fine::where('status', Fine::STATUS_PAID)
                ->whereDate('paid_at', today())
                ->count(),
            'paid_today_amount' => (int) Fine::where('status', Fine::STATUS_PAID)
                ->whereDate('paid_at', today())
                ->sum('amount'),
            'waived_count' => Fine::where('status', Fine::STATUS_WAIVED)->count(),
        ];
    }

    /**
     * Get users with unpaid fines.
     */
    public function getUsersWithUnpaidFines(): Collection
    {
        return User::whereHas('fines', function ($query) {
            $query->where('status', Fine::STATUS_UNPAID);
        })
        ->withSum(['fines as total_unpaid' => function ($query) {
            $query->where('status', Fine::STATUS_UNPAID);
        }], 'amount')
        ->orderByDesc('total_unpaid')
        ->get();
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    use HasFactory;

    /**
     * Fine status constants.
     */
    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PAID = 'paid';
    public const STATUS_WAIVED = 'waived';

    protected $fillable = [
        'loan_id',
        'user_id',
        'amount',
        'reason',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Fine per day in Rupiah.
     */
    public const FINE_PER_DAY = 1000;

    /**
     * Maximum fine amount in Rupiah.
     */
    public const MAX_FINE = 50000;

    /**
     * Get the loan associated with this fine.
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Get the user who owes the fine.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the fine is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if the fine is unpaid.
     */
    public function isUnpaid(): bool
    {
        return $this->status === self::STATUS_UNPAID;
    }

    /**
     * Check if the fine is waived.
     */
    public function isWaived(): bool
    {
        return $this->status === self::STATUS_WAIVED;
    }

    /**
     * Mark the fine as paid.
     */
    public function markAsPaid(): void
    {
        $this->status = self::STATUS_PAID;
        $this->paid_at = Carbon::now();
        $this->save();
    }

    /**
     * Waive the fine.
     */
    public function waive(): void
    {
        $this->status = self::STATUS_WAIVED;
        $this->save();
    }

    /**
     * Calculate fine amount based on days overdue.
     */
    public static function calculateAmount(int $daysOverdue): int
    {
        if ($daysOverdue <= Loan::GRACE_PERIOD) {
            return 0;
        }

        $chargableDays = $daysOverdue - Loan::GRACE_PERIOD;
        $amount = $chargableDays * self::FINE_PER_DAY;

        return min($amount, self::MAX_FINE);
    }

    /**
     * Scope for unpaid fines.
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_UNPAID);
    }

    /**
     * Scope for paid fines.
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get formatted amount in Rupiah.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}

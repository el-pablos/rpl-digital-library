<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    use HasFactory;

    /**
     * Loan status constants.
     */
    public const STATUS_REQUESTED = 'requested';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'book_id',
        'request_date',
        'approval_date',
        'pickup_date',
        'due_date',
        'return_date',
        'status',
        'renewal_count',
        'notes',
        'approved_by',
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'approval_date' => 'datetime',
        'pickup_date' => 'datetime',
        'due_date' => 'date',
        'return_date' => 'datetime',
        'renewal_count' => 'integer',
    ];

    /**
     * Maximum number of renewals allowed.
     */
    public const MAX_RENEWALS = 5;

    /**
     * Default loan duration in days.
     */
    public const LOAN_DURATION = 7;

    /**
     * Grace period in days (no fine within this period).
     */
    public const GRACE_PERIOD = 3;

    /**
     * Get the user who borrowed the book.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book being borrowed.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the librarian who approved the loan.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the fine associated with this loan.
     */
    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    /**
     * Get notifications related to this loan.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Check if the loan is overdue.
     */
    public function isOverdue(): bool
    {
        if (!$this->due_date || $this->status === self::STATUS_RETURNED) {
            return false;
        }

        return Carbon::now()->startOfDay()->gt($this->due_date);
    }

    /**
     * Get days overdue (negative means days remaining).
     */
    public function getDaysOverdueAttribute(): int
    {
        if (!$this->due_date) {
            return 0;
        }

        $returnDate = $this->return_date ?? Carbon::now();
        return (int) $this->due_date->diffInDays($returnDate, false);
    }

    /**
     * Get days remaining until due date.
     */
    public function getDaysRemainingAttribute(): int
    {
        if (!$this->due_date || $this->status === self::STATUS_RETURNED) {
            return 0;
        }

        $diff = Carbon::now()->startOfDay()->diffInDays($this->due_date, false);
        return max(0, (int) $diff);
    }

    /**
     * Check if the loan can be renewed.
     */
    public function canRenew(): bool
    {
        return $this->status === self::STATUS_ACTIVE 
            && $this->renewal_count < self::MAX_RENEWALS 
            && !$this->isOverdue();
    }

    /**
     * Renew the loan (extend due date).
     */
    public function renew(): bool
    {
        if (!$this->canRenew()) {
            return false;
        }

        $this->due_date = $this->due_date->addDays(self::LOAN_DURATION);
        $this->renewal_count++;
        $this->save();

        return true;
    }

    /**
     * Scope for active loans.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for pending/requested loans.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REQUESTED);
    }

    /**
     * Scope for overdue loans.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereDate('due_date', '<', Carbon::now()->startOfDay());
    }

    /**
     * Scope for loans due soon (within X days).
     */
    public function scopeDueSoon(Builder $query, int $days = 3): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereDate('due_date', '>=', Carbon::now()->startOfDay())
            ->whereDate('due_date', '<=', Carbon::now()->addDays($days)->endOfDay());
    }

    /**
     * Scope for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for today's pickups.
     */
    public function scopeTodaysPickups(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED)
            ->whereDate('approval_date', Carbon::today());
    }
}

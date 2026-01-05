<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'is_read',
        'loan_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Notification types.
     */
    public const TYPE_LOAN_APPROVED = 'loan_approved';
    public const TYPE_LOAN_REJECTED = 'loan_rejected';
    public const TYPE_DUE_REMINDER = 'due_reminder';
    public const TYPE_OVERDUE = 'overdue';
    public const TYPE_RETURN_CONFIRMED = 'return_confirmed';
    public const TYPE_REVIEW_APPROVED = 'review_approved';
    public const TYPE_FINE_CREATED = 'fine_created';

    /**
     * Get the user who received the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related loan (if any).
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        $this->is_read = true;
        $this->save();
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(): void
    {
        $this->is_read = false;
        $this->save();
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications.
     */
    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for ordering by newest.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Get the notification icon based on type.
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_LOAN_APPROVED => 'check-circle',
            self::TYPE_LOAN_REJECTED => 'x-circle',
            self::TYPE_DUE_REMINDER => 'clock',
            self::TYPE_OVERDUE => 'alert-triangle',
            self::TYPE_RETURN_CONFIRMED => 'book',
            self::TYPE_REVIEW_APPROVED => 'star',
            self::TYPE_FINE_CREATED => 'dollar-sign',
            default => 'bell',
        };
    }

    /**
     * Get the notification color based on type.
     */
    public function getColorAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_LOAN_APPROVED, self::TYPE_RETURN_CONFIRMED, self::TYPE_REVIEW_APPROVED => 'green',
            self::TYPE_LOAN_REJECTED, self::TYPE_OVERDUE => 'red',
            self::TYPE_DUE_REMINDER => 'yellow',
            self::TYPE_FINE_CREATED => 'orange',
            default => 'blue',
        };
    }
}

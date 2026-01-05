<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'type',
        'score',
        'clicked',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'clicked' => 'boolean',
    ];

    /**
     * Recommendation types.
     */
    public const TYPE_CONTENT_BASED = 'content_based';
    public const TYPE_COLLABORATIVE = 'collaborative';
    public const TYPE_POPULARITY = 'popularity';
    public const TYPE_RECENCY = 'recency';

    /**
     * Weight for hybrid calculation.
     */
    public const WEIGHTS = [
        self::TYPE_CONTENT_BASED => 0.40,
        self::TYPE_COLLABORATIVE => 0.30,
        self::TYPE_POPULARITY => 0.20,
        self::TYPE_RECENCY => 0.10,
    ];

    /**
     * Get the user who received the recommendation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the recommended book.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Mark as clicked.
     */
    public function markAsClicked(): void
    {
        $this->clicked = true;
        $this->save();
    }

    /**
     * Scope for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for ordering by highest score.
     */
    public function scopeHighestScore(Builder $query): Builder
    {
        return $query->orderByDesc('score');
    }

    /**
     * Scope for a specific type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for not clicked recommendations.
     */
    public function scopeNotClicked(Builder $query): Builder
    {
        return $query->where('clicked', false);
    }
}

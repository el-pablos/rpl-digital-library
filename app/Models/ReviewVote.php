<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'vote_type',
    ];

    /**
     * Get the review that was voted on.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Get the user who voted.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this is a helpful vote.
     */
    public function isHelpful(): bool
    {
        return $this->vote_type === 'helpful';
    }

    /**
     * Check if this is a not helpful vote.
     */
    public function isNotHelpful(): bool
    {
        return $this->vote_type === 'not_helpful';
    }
}

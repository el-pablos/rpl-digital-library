<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Role constants.
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_LIBRARIAN = 'librarian';
    public const ROLE_MEMBER = 'member';

    /**
     * Maximum active loans per member.
     */
    public const MAX_ACTIVE_LOANS = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all loans for this user.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get active loans for this user.
     */
    public function activeLoans(): HasMany
    {
        return $this->hasMany(Loan::class)->whereIn('status', ['requested', 'approved', 'active']);
    }

    /**
     * Get all reviews written by this user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all review votes by this user.
     */
    public function reviewVotes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    /**
     * Get all notifications for this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications for this user.
     */
    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }

    /**
     * Get all fines for this user.
     */
    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    /**
     * Get unpaid fines for this user.
     */
    public function unpaidFines(): HasMany
    {
        return $this->hasMany(Fine::class)->where('status', 'unpaid');
    }

    /**
     * Get all recommendations for this user.
     */
    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user is a librarian.
     */
    public function isLibrarian(): bool
    {
        return $this->hasRole(self::ROLE_LIBRARIAN);
    }

    /**
     * Check if user is a member.
     */
    public function isMember(): bool
    {
        return $this->hasRole(self::ROLE_MEMBER);
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user can borrow books.
     */
    public function canBorrow(): bool
    {
        // Must be active
        if (!$this->isActive()) {
            return false;
        }

        // Must not have unpaid fines
        if ($this->unpaidFines()->exists()) {
            return false;
        }

        // Must not exceed max active loans
        if ($this->activeLoans()->count() >= self::MAX_ACTIVE_LOANS) {
            return false;
        }

        return true;
    }

    /**
     * Get the count of active loans.
     */
    public function getActiveLoansCountAttribute(): int
    {
        return $this->activeLoans()->count();
    }

    /**
     * Get total unpaid fines amount.
     */
    public function getTotalUnpaidFinesAttribute(): float
    {
        return $this->unpaidFines()->sum('amount');
    }

    /**
     * Check if user has ever borrowed a specific book.
     */
    public function hasBorrowedBook(int $bookId): bool
    {
        return $this->loans()
            ->where('book_id', $bookId)
            ->whereIn('status', ['active', 'returned'])
            ->exists();
    }

    /**
     * Scope for active users.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for users with a specific role.
     */
    public function scopeWithRole(Builder $query, string $role): Builder
    {
        return $query->role($role);
    }
}

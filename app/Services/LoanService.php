<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanService
{
    /**
     * Request a book loan.
     */
    public function requestLoan(User $user, Book $book): Loan
    {
        if (!$user->canBorrow()) {
            throw new \Exception('Anda tidak dapat meminjam buku saat ini. Periksa status keanggotaan, pinjaman aktif, atau denda yang belum dibayar.');
        }

        if (!$book->isAvailable()) {
            throw new \Exception('Buku tidak tersedia untuk dipinjam saat ini.');
        }

        // Check if user already has a pending or active loan for this book
        $existingLoan = Loan::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', [Loan::STATUS_REQUESTED, Loan::STATUS_APPROVED, Loan::STATUS_ACTIVE])
            ->first();

        if ($existingLoan) {
            throw new \Exception('Anda sudah memiliki pinjaman aktif atau pending untuk buku ini.');
        }

        return DB::transaction(function () use ($user, $book) {
            // Decrease available copies when requesting (reserve the book)
            $book->decrement('available_copies');

            return Loan::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'status' => Loan::STATUS_REQUESTED,
                'request_date' => Carbon::now(),
            ]);
        });
    }

    /**
     * Approve a loan request.
     */
    public function approveLoan(Loan $loan, User $approver): Loan
    {
        if ($loan->status !== Loan::STATUS_REQUESTED) {
            throw new \Exception('Pinjaman ini tidak dalam status pending.');
        }

        $loan->update([
            'status' => Loan::STATUS_APPROVED,
            'approval_date' => Carbon::now(),
            'approved_by' => $approver->id,
        ]);

        // Create notification for user
        Notification::create([
            'user_id' => $loan->user_id,
            'type' => Notification::TYPE_LOAN_APPROVED,
            'message' => "Peminjaman buku \"{$loan->book->title}\" telah disetujui. Silakan ambil dalam waktu 3 hari.",
            'loan_id' => $loan->id,
        ]);

        return $loan->fresh();
    }

    /**
     * Reject a loan request.
     */
    public function rejectLoan(Loan $loan, User $librarian = null, string $reason = ''): Loan
    {
        if ($loan->status !== Loan::STATUS_REQUESTED) {
            throw new \Exception('Pinjaman ini tidak dalam status pending.');
        }

        return DB::transaction(function () use ($loan, $reason) {
            $loan->update([
                'status' => Loan::STATUS_REJECTED,
                'notes' => $reason,
            ]);

            // Restore available copies since book was reserved
            $loan->book->increment('available_copies');

            // Create notification for user
            Notification::create([
                'user_id' => $loan->user_id,
                'type' => Notification::TYPE_LOAN_REJECTED,
                'message' => "Peminjaman buku \"{$loan->book->title}\" ditolak. Alasan: {$reason}",
                'loan_id' => $loan->id,
            ]);

            return $loan->fresh();
        });
    }

    /**
     * Process book pickup (mark loan as active).
     */
    public function processPickup(Loan $loan): Loan
    {
        if ($loan->status !== Loan::STATUS_APPROVED) {
            throw new \Exception('Pinjaman ini tidak dalam status disetujui.');
        }

        return DB::transaction(function () use ($loan) {
            $loan->update([
                'status' => Loan::STATUS_ACTIVE,
                'pickup_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays(Loan::LOAN_DURATION),
            ]);

            // Note: available_copies was already decreased in requestLoan()

            return $loan->fresh();
        });
    }

    /**
     * Process book return.
     */
    public function processReturn(Loan $loan): array
    {
        if (!in_array($loan->status, [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])) {
            throw new \Exception('Pinjaman ini tidak dalam status aktif.');
        }

        return DB::transaction(function () use ($loan) {
            $returnDate = Carbon::now();
            // Compare as dates only (due_date is cast as date, not datetime)
            $isLate = $returnDate->copy()->startOfDay()->gt($loan->due_date->copy()->startOfDay());
            $fine = null;

            $loan->update([
                'status' => Loan::STATUS_RETURNED,
                'return_date' => $returnDate,
            ]);

            // Increase available copies
            $loan->book->increment('available_copies');

            // Calculate fine if late (after grace period)
            if ($isLate) {
                // Use absolute value since diffInDays might return negative
                $daysLate = abs($returnDate->copy()->startOfDay()->diffInDays($loan->due_date->copy()->startOfDay()));
                $fineAmount = Fine::calculateAmount($daysLate);

                if ($fineAmount > 0) {
                    $fine = Fine::create([
                        'loan_id' => $loan->id,
                        'user_id' => $loan->user_id,
                        'amount' => $fineAmount,
                        'reason' => "Keterlambatan pengembalian: {$daysLate} hari",
                        'status' => Fine::STATUS_UNPAID,
                    ]);

                    // Notify user about fine
                    Notification::create([
                        'user_id' => $loan->user_id,
                        'type' => Notification::TYPE_FINE_CREATED,
                        'message' => "Anda dikenakan denda sebesar Rp " . number_format($fineAmount, 0, ',', '.') . " untuk keterlambatan pengembalian buku.",
                        'loan_id' => $loan->id,
                    ]);
                }
            }

            // Notify user about return
            Notification::create([
                'user_id' => $loan->user_id,
                'type' => Notification::TYPE_RETURN_CONFIRMED,
                'message' => "Pengembalian buku \"{$loan->book->title}\" telah dikonfirmasi.",
                'loan_id' => $loan->id,
            ]);

            return [
                'loan' => $loan->fresh(),
                'fine' => $fine,
                'is_late' => $isLate,
            ];
        });
    }

    /**
     * Renew a loan.
     */
    public function renewLoan(Loan $loan): Loan
    {
        if (!$loan->canRenew()) {
            throw new \Exception('Pinjaman ini tidak dapat diperpanjang. Mungkin sudah mencapai batas perpanjangan atau sudah terlambat.');
        }

        $loan->renew();

        return $loan->fresh();
    }

    /**
     * Cancel a loan request.
     */
    public function cancelLoan(Loan $loan): Loan
    {
        if ($loan->status !== Loan::STATUS_REQUESTED) {
            throw new \Exception('Hanya pinjaman dengan status pending yang dapat dibatalkan.');
        }

        return DB::transaction(function () use ($loan) {
            $loan->update([
                'status' => Loan::STATUS_CANCELLED,
                'notes' => 'Dibatalkan oleh anggota.',
            ]);

            // Restore available copies since book was reserved
            $loan->book->increment('available_copies');

            return $loan->fresh();
        });
    }

    /**
     * Mark overdue loans.
     */
    public function markOverdueLoans(): int
    {
        $overdueLoans = Loan::where('status', Loan::STATUS_ACTIVE)
            ->whereDate('due_date', '<', Carbon::now()->startOfDay())
            ->get();

        foreach ($overdueLoans as $loan) {
            $loan->update(['status' => Loan::STATUS_OVERDUE]);

            Notification::create([
                'user_id' => $loan->user_id,
                'type' => Notification::TYPE_OVERDUE,
                'message' => "Buku \"{$loan->book->title}\" sudah melewati batas waktu pengembalian. Segera kembalikan untuk menghindari denda.",
                'loan_id' => $loan->id,
            ]);
        }

        return $overdueLoans->count();
    }

    /**
     * Send due date reminders.
     */
    public function sendDueReminders(int $daysBeforeDue = 2): int
    {
        $dueSoonLoans = Loan::where('status', Loan::STATUS_ACTIVE)
            ->whereDate('due_date', '=', Carbon::now()->addDays($daysBeforeDue)->toDateString())
            ->get();

        foreach ($dueSoonLoans as $loan) {
            Notification::create([
                'user_id' => $loan->user_id,
                'type' => Notification::TYPE_DUE_REMINDER,
                'message' => "Buku \"{$loan->book->title}\" akan jatuh tempo dalam {$daysBeforeDue} hari. Silakan kembalikan atau perpanjang.",
                'loan_id' => $loan->id,
            ]);
        }

        return $dueSoonLoans->count();
    }

    /**
     * Get loan statistics for dashboard.
     */
    public function getLoanStatistics(): array
    {
        return [
            'total_active' => Loan::where('status', Loan::STATUS_ACTIVE)->count(),
            'total_overdue' => Loan::where('status', Loan::STATUS_OVERDUE)->count(),
            'pending_requests' => Loan::where('status', Loan::STATUS_REQUESTED)->count(),
            'awaiting_pickup' => Loan::where('status', Loan::STATUS_APPROVED)->count(),
            'returned_today' => Loan::where('status', Loan::STATUS_RETURNED)
                ->whereDate('return_date', Carbon::today())
                ->count(),
            'due_today' => Loan::where('status', Loan::STATUS_ACTIVE)
                ->whereDate('due_date', Carbon::today())
                ->count(),
        ];
    }
}

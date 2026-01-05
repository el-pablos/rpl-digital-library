<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = User::role('member')->get();
        $librarians = User::role('librarian')->get();
        $books = Book::all();

        if ($members->isEmpty() || $librarians->isEmpty() || $books->isEmpty()) {
            $this->command->warn('Skipping LoanSeeder: Required data not found.');
            return;
        }

        $librarian = $librarians->first();

        // 1. Requested (Pending) Loans
        for ($i = 0; $i < 3; $i++) {
            Loan::create([
                'user_id' => $members->random()->id,
                'book_id' => $books->random()->id,
                'status' => Loan::STATUS_REQUESTED,
                'request_date' => Carbon::now()->subDays(rand(0, 2)),
            ]);
        }

        // 2. Approved (waiting for pickup) Loans
        for ($i = 0; $i < 2; $i++) {
            Loan::create([
                'user_id' => $members->random()->id,
                'book_id' => $books->random()->id,
                'status' => Loan::STATUS_APPROVED,
                'request_date' => Carbon::now()->subDays(3),
                'approval_date' => Carbon::now()->subDays(2),
                'approved_by' => $librarian->id,
            ]);
        }

        // 3. Active Loans (currently borrowed)
        for ($i = 0; $i < 5; $i++) {
            $requestDate = Carbon::now()->subDays(rand(5, 10));
            $approvalDate = $requestDate->copy()->addDay();
            $pickupDate = $approvalDate->copy()->addDay();
            $dueDate = $pickupDate->copy()->addDays(Loan::LOAN_DURATION);

            Loan::create([
                'user_id' => $members->random()->id,
                'book_id' => $books->random()->id,
                'status' => Loan::STATUS_ACTIVE,
                'request_date' => $requestDate,
                'approval_date' => $approvalDate,
                'approved_by' => $librarian->id,
                'pickup_date' => $pickupDate,
                'due_date' => $dueDate,
                'renewal_count' => rand(0, 2),
            ]);
        }

        // 4. Overdue Loans (past due date)
        for ($i = 0; $i < 2; $i++) {
            $requestDate = Carbon::now()->subDays(rand(15, 20));
            $approvalDate = $requestDate->copy()->addDay();
            $pickupDate = $approvalDate->copy()->addDay();
            $dueDate = $pickupDate->copy()->addDays(Loan::LOAN_DURATION); // Past due

            Loan::create([
                'user_id' => $members->random()->id,
                'book_id' => $books->random()->id,
                'status' => Loan::STATUS_OVERDUE,
                'request_date' => $requestDate,
                'approval_date' => $approvalDate,
                'approved_by' => $librarian->id,
                'pickup_date' => $pickupDate,
                'due_date' => $dueDate,
                'renewal_count' => Loan::MAX_RENEWALS, // Max renewals reached
            ]);
        }

        // 5. Returned Loans (completed on time)
        for ($i = 0; $i < 5; $i++) {
            $requestDate = Carbon::now()->subDays(rand(20, 30));
            $approvalDate = $requestDate->copy()->addDay();
            $pickupDate = $approvalDate->copy()->addDay();
            $dueDate = $pickupDate->copy()->addDays(Loan::LOAN_DURATION);
            $returnDate = $dueDate->copy()->subDays(rand(1, 3)); // Returned before due

            Loan::create([
                'user_id' => $members->random()->id,
                'book_id' => $books->random()->id,
                'status' => Loan::STATUS_RETURNED,
                'request_date' => $requestDate,
                'approval_date' => $approvalDate,
                'approved_by' => $librarian->id,
                'pickup_date' => $pickupDate,
                'due_date' => $dueDate,
                'return_date' => $returnDate,
                'renewal_count' => rand(0, 3),
            ]);
        }

        // 6. Returned Late (with potential fine)
        for ($i = 0; $i < 2; $i++) {
            $requestDate = Carbon::now()->subDays(rand(35, 45));
            $approvalDate = $requestDate->copy()->addDay();
            $pickupDate = $approvalDate->copy()->addDay();
            $dueDate = $pickupDate->copy()->addDays(Loan::LOAN_DURATION);
            $returnDate = $dueDate->copy()->addDays(rand(5, 10)); // Returned late

            Loan::create([
                'user_id' => $members->random()->id,
                'book_id' => $books->random()->id,
                'status' => Loan::STATUS_RETURNED,
                'request_date' => $requestDate,
                'approval_date' => $approvalDate,
                'approved_by' => $librarian->id,
                'pickup_date' => $pickupDate,
                'due_date' => $dueDate,
                'return_date' => $returnDate,
                'renewal_count' => Loan::MAX_RENEWALS,
            ]);
        }

        // 7. Rejected Loans
        for ($i = 0; $i < 2; $i++) {
            Loan::create([
                'user_id' => $members->random()->id,
                'book_id' => $books->random()->id,
                'status' => Loan::STATUS_REJECTED,
                'request_date' => Carbon::now()->subDays(rand(10, 15)),
                'notes' => $this->getRandomRejectionReason(),
            ]);
        }

        // 8. Cancelled Loans - use 'requested' status since 'cancelled' may not be in enum
        Loan::create([
            'user_id' => $members->random()->id,
            'book_id' => $books->random()->id,
            'status' => Loan::STATUS_REJECTED,
            'request_date' => Carbon::now()->subDays(5),
            'notes' => 'Dibatalkan oleh anggota.',
        ]);
    }

    /**
     * Get a random rejection reason.
     */
    private function getRandomRejectionReason(): string
    {
        $reasons = [
            'Stok buku tidak tersedia.',
            'Anggota memiliki denda yang belum dibayar.',
            'Buku sedang dalam proses pemeliharaan.',
            'Kuota peminjaman maksimum tercapai.',
        ];

        return $reasons[array_rand($reasons)];
    }
}

<?php

namespace Database\Seeders;

use App\Models\Fine;
use App\Models\Loan;
use Illuminate\Database\Seeder;

class FineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get overdue and returned late loans
        $overdueLoans = Loan::where('status', Loan::STATUS_OVERDUE)->get();
        $returnedLoans = Loan::where('status', Loan::STATUS_RETURNED)
            ->whereNotNull('return_date')
            ->get();

        // Create fines for overdue loans (unpaid)
        foreach ($overdueLoans as $loan) {
            $daysOverdue = now()->diffInDays($loan->due_date);
            if ($daysOverdue > 0) {
                $amount = Fine::calculateAmount($daysOverdue);
                
                Fine::create([
                    'loan_id' => $loan->id,
                    'user_id' => $loan->user_id,
                    'amount' => $amount,
                    'reason' => 'Keterlambatan pengembalian buku: ' . $daysOverdue . ' hari',
                    'status' => Fine::STATUS_UNPAID,
                ]);
            }
        }

        // Create fines for returned late loans
        foreach ($returnedLoans as $loan) {
            if ($loan->return_date && $loan->due_date && $loan->return_date->gt($loan->due_date)) {
                $daysLate = $loan->return_date->diffInDays($loan->due_date);
                
                // Skip grace period (3 days)
                if ($daysLate <= Loan::GRACE_PERIOD) {
                    continue;
                }
                
                $chargeableDays = $daysLate - Loan::GRACE_PERIOD;
                $amount = Fine::calculateAmount($chargeableDays);
                
                // Some paid, some unpaid
                $status = rand(0, 1) ? Fine::STATUS_PAID : Fine::STATUS_UNPAID;
                
                Fine::create([
                    'loan_id' => $loan->id,
                    'user_id' => $loan->user_id,
                    'amount' => $amount,
                    'reason' => 'Keterlambatan pengembalian buku: ' . $daysLate . ' hari (setelah grace period)',
                    'status' => $status,
                    'paid_at' => $status === Fine::STATUS_PAID ? now()->subDays(rand(1, 5)) : null,
                ]);
            }
        }
    }
}

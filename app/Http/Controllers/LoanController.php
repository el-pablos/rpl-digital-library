<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(
        protected LoanService $loanService
    ) {}

    /**
     * Display user's loans (for members).
     */
    public function index(): View
    {
        $user = auth()->user();
        
        $activeLoans = $user->loans()
            ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])
            ->with('book')
            ->orderBy('due_date')
            ->get();

        $pendingLoans = $user->loans()
            ->whereIn('status', [Loan::STATUS_REQUESTED, Loan::STATUS_APPROVED])
            ->with('book')
            ->orderByDesc('created_at')
            ->get();

        $history = $user->loans()
            ->whereIn('status', [Loan::STATUS_RETURNED, Loan::STATUS_REJECTED])
            ->with('book')
            ->orderByDesc('updated_at')
            ->paginate(10);

        // Combine all loans for the view
        $loans = $user->loans()
            ->with('book')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('loans.index', compact('activeLoans', 'pendingLoans', 'history', 'loans'));
    }

    /**
     * Request a new loan.
     */
    public function store(Request $request, Book $book): RedirectResponse
    {
        try {
            $this->loanService->requestLoan(auth()->user(), $book);
            
            return redirect()
                ->route('loans.index')
                ->with('success', 'Permintaan peminjaman berhasil diajukan. Menunggu persetujuan pustakawan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel a loan request.
     */
    public function cancel(Loan $loan): RedirectResponse
    {
        $this->authorize('cancel', $loan);

        try {
            $this->loanService->cancelLoan($loan);
            
            return back()->with('success', 'Permintaan peminjaman berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Renew a loan.
     */
    public function renew(Loan $loan): RedirectResponse
    {
        $this->authorize('renew', $loan);

        try {
            $this->loanService->renewLoan($loan);
            
            return back()->with('success', 'Peminjaman berhasil diperpanjang.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show loan details.
     */
    public function show(Loan $loan): View
    {
        $this->authorize('view', $loan);

        $loan->load(['book', 'user', 'approver', 'fine']);

        return view('loans.show', compact('loan'));
    }
}

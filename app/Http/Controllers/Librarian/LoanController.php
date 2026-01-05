<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
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
     * Display all loans for management.
     */
    public function index(Request $request): View
    {
        $query = Loan::with('user', 'book', 'approver');

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Search
        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('book', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        // Date filter
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $loans = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('librarian.loans.index', compact('loans'));
    }

    /**
     * Display pending loan requests.
     */
    public function pending(): View
    {
        $loans = Loan::where('status', Loan::STATUS_REQUESTED)
            ->with('user', 'book')
            ->orderBy('created_at')
            ->paginate(15);

        return view('librarian.loans.index', compact('loans'));
    }

    /**
     * Display loans awaiting pickup.
     */
    public function awaitingPickup(): View
    {
        $loans = Loan::where('status', Loan::STATUS_APPROVED)
            ->with('user', 'book', 'approver')
            ->orderBy('approval_date')
            ->paginate(15);

        return view('librarian.loans.index', compact('loans'));
    }

    /**
     * Display active loans.
     */
    public function active(): View
    {
        $loans = Loan::whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])
            ->with('user', 'book')
            ->orderBy('due_date')
            ->paginate(15);

        return view('librarian.loans.index', compact('loans'));
    }

    /**
     * Approve a loan request.
     */
    public function approve(Loan $loan): RedirectResponse
    {
        try {
            $this->loanService->approveLoan($loan, auth()->user());
            
            return back()->with('success', 'Peminjaman berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject a loan request.
     */
    public function reject(Request $request, Loan $loan): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->loanService->rejectLoan($loan, auth()->user(), $validated['reason']);
            
            return back()->with('success', 'Peminjaman berhasil ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Process book pickup.
     */
    public function pickup(Loan $loan): RedirectResponse
    {
        try {
            $this->loanService->processPickup($loan);
            
            return back()->with('success', 'Pengambilan buku berhasil dikonfirmasi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Process book return.
     */
    public function return(Loan $loan): RedirectResponse
    {
        try {
            $result = $this->loanService->processReturn($loan);
            
            $message = 'Pengembalian buku berhasil dikonfirmasi.';
            if ($result['is_late'] && $result['fine']) {
                $message .= ' Denda sebesar Rp ' . number_format($result['fine']->amount, 0, ',', '.') . ' telah dikenakan.';
            }
            
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show loan details.
     */
    public function show(Loan $loan): View
    {
        $loan->load(['user', 'book', 'approver', 'fine']);

        return view('librarian.loans.show', compact('loan'));
    }
}

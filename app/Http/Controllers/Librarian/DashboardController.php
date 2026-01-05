<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Review;
use App\Services\FineService;
use App\Services\LoanService;
use App\Services\ReviewService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected LoanService $loanService,
        protected FineService $fineService,
        protected ReviewService $reviewService
    ) {}

    /**
     * Display librarian dashboard.
     */
    public function index(): View
    {
        // Loan statistics
        $loanStats = $this->loanService->getLoanStatistics();

        // Fine statistics
        $fineStats = $this->fineService->getFineStatistics();

        // Review statistics
        $reviewStats = $this->reviewService->getReviewStatistics();

        // Combined stats for view
        $stats = [
            'pending_requests' => Loan::where('status', Loan::STATUS_REQUESTED)->count(),
            'overdue_loans' => Loan::where('status', Loan::STATUS_OVERDUE)->count(),
            'pending_reviews' => Review::where('status', Review::STATUS_PENDING)->count(),
            'active_loans' => Loan::where('status', Loan::STATUS_ACTIVE)->count(),
            'ready_for_pickup' => Loan::where('status', Loan::STATUS_APPROVED)->count(),
            'unpaid_fines' => $fineStats['total_unpaid'] ?? 0,
            'returned_today' => Loan::where('status', Loan::STATUS_RETURNED)
                ->whereDate('return_date', today())
                ->count(),
        ];

        // Pending loan requests
        $pendingLoans = Loan::where('status', Loan::STATUS_REQUESTED)
            ->with('user', 'book')
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        // Loans awaiting pickup
        $awaitingPickup = Loan::where('status', Loan::STATUS_APPROVED)
            ->with('user', 'book')
            ->orderBy('approval_date')
            ->limit(5)
            ->get();

        // Overdue loans
        $overdueLoans = Loan::where('status', Loan::STATUS_OVERDUE)
            ->with('user', 'book')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Pending reviews
        $pendingReviews = Review::where('status', Review::STATUS_PENDING)
            ->with('user', 'book')
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        // Today's activities
        $todaysReturns = Loan::where('status', Loan::STATUS_RETURNED)
            ->whereDate('return_date', today())
            ->count();

        $todaysPickups = Loan::where('status', Loan::STATUS_ACTIVE)
            ->whereDate('pickup_date', today())
            ->count();

        return view('librarian.dashboard', compact(
            'stats',
            'loanStats',
            'fineStats',
            'reviewStats',
            'pendingLoans',
            'awaitingPickup',
            'overdueLoans',
            'pendingReviews',
            'todaysReturns',
            'todaysPickups'
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Review;
use App\Models\User;
use App\Models\Fine;
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
     * Display admin dashboard.
     */
    public function index(): View
    {
        // General statistics
        $stats = [
            'total_books' => Book::count(),
            'total_users' => User::count(),
            'total_members' => User::role('member')->count(),
            'active_loans' => Loan::where('status', Loan::STATUS_ACTIVE)->count(),
            'total_categories' => \App\Models\Category::count(),
        ];

        // Loan statistics
        $loanStats = $this->loanService->getLoanStatistics();

        // Fine statistics
        $fineStats = $this->fineService->getFineStatistics();

        // Review statistics
        $reviewStats = $this->reviewService->getReviewStatistics();

        // Recent activities
        $recentLoans = Loan::with('user', 'book')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentReviews = Review::with('user', 'book')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Recent books
        $recentBooks = Book::orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Recent users
        $recentUsers = User::orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Books with low stock
        $lowStockBooks = Book::where('available_copies', '<=', 2)
            ->where('total_copies', '>', 0)
            ->orderBy('available_copies')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'loanStats',
            'fineStats',
            'reviewStats',
            'recentLoans',
            'recentReviews',
            'recentBooks',
            'recentUsers',
            'lowStockBooks'
        ));
    }
}

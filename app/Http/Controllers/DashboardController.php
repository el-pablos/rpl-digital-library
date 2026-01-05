<?php

namespace App\Http\Controllers;

use App\Services\FineService;
use App\Services\RecommendationService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendationService,
        protected FineService $fineService
    ) {}

    /**
     * Display member dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Active loans
        $activeLoans = $user->loans()
            ->whereIn('status', ['active', 'overdue'])
            ->with('book')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Pending requests
        $pendingLoans = $user->loans()
            ->whereIn('status', ['requested', 'approved'])
            ->with('book')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Unpaid fines
        $unpaidFines = $this->fineService->getUnpaidFines($user);
        $totalUnpaidFines = $this->fineService->getTotalUnpaidFines($user);

        // Unread notifications
        $unreadNotifications = $user->notifications()
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Recommendations
        $recommendations = $this->recommendationService->getCachedRecommendations($user, 4);

        // Statistics
        $stats = [
            'total_borrowed' => $user->loans()->count(),
            'currently_borrowed' => $user->loans()->whereIn('status', ['active', 'overdue'])->count(),
            'reviews_written' => $user->reviews()->count(),
            'can_borrow' => $user->canBorrow(),
        ];

        return view('dashboard', compact(
            'activeLoans',
            'pendingLoans',
            'unpaidFines',
            'totalUnpaidFines',
            'unreadNotifications',
            'recommendations',
            'stats'
        ));
    }
}

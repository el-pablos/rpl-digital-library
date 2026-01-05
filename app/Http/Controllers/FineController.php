<?php

namespace App\Http\Controllers;

use App\Services\FineService;
use App\Models\Fine;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FineController extends Controller
{
    public function __construct(
        protected FineService $fineService
    ) {}

    /**
     * Display user's fines.
     */
    public function index(): View
    {
        $user = auth()->user();

        $unpaidFines = $this->fineService->getUnpaidFines($user);
        $totalUnpaid = $this->fineService->getTotalUnpaidFines($user);

        $paidFines = Fine::where('user_id', $user->id)
            ->whereIn('status', [Fine::STATUS_PAID, Fine::STATUS_WAIVED])
            ->with('loan.book')
            ->orderByDesc('paid_at')
            ->paginate(10);

        // Merge fines for the view
        $fines = Fine::where('user_id', $user->id)
            ->with('loan.book')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('fines.index', compact('unpaidFines', 'totalUnpaid', 'paidFines', 'fines'));
    }

    /**
     * Pay a fine.
     */
    public function pay(Fine $fine): RedirectResponse
    {
        $this->authorize('pay', $fine);

        try {
            $this->fineService->payFine($fine);
            
            return back()->with('success', 'Pembayaran denda berhasil dikonfirmasi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

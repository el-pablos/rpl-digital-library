<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Services\FineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FineController extends Controller
{
    public function __construct(
        protected FineService $fineService
    ) {}

    /**
     * Display all fines.
     */
    public function index(Request $request): View
    {
        $query = Fine::with('user', 'loan.book');

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Search by user
        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $fines = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $statistics = $this->fineService->getFineStatistics();

        return view('librarian.fines.index', compact('fines', 'statistics'));
    }

    /**
     * Display unpaid fines.
     */
    public function unpaid(): View
    {
        $fines = Fine::where('status', Fine::STATUS_UNPAID)
            ->with('user', 'loan.book')
            ->orderByDesc('created_at')
            ->paginate(15);

        $statistics = $this->fineService->getFineStatistics();

        return view('librarian.fines.index', compact('fines', 'statistics'));
    }

    /**
     * Mark fine as paid.
     */
    public function pay(Fine $fine): RedirectResponse
    {
        try {
            $this->fineService->payFine($fine);
            
            return back()->with('success', 'Pembayaran denda berhasil dikonfirmasi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Waive a fine.
     */
    public function waive(Request $request, Fine $fine): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->fineService->waiveFine($fine, $validated['reason'] ?? null);
            
            return back()->with('success', 'Denda berhasil dihapuskan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

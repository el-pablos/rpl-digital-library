<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $query = User::with('roles');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->role($role);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,suspended',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $validated['status'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $user->load(['roles', 'loans' => function ($q) {
            $q->latest()->limit(10);
        }, 'reviews' => function ($q) {
            $q->latest()->limit(5);
        }, 'fines']);

        $statistics = [
            'total_loans' => $user->loans()->count(),
            'active_loans' => $user->loans()->whereIn('status', ['active', 'overdue'])->count(),
            'total_reviews' => $user->reviews()->count(),
            'unpaid_fines' => $user->fines()->where('status', 'unpaid')->sum('amount'),
        ];

        return view('admin.users.show', compact('user', 'statistics'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,suspended',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $validated['status'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Suspend the specified user.
     */
    public function suspend(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menangguhkan diri sendiri.');
        }

        $user->update(['status' => 'suspended']);

        return back()->with('success', 'Pengguna berhasil ditangguhkan.');
    }

    /**
     * Activate the specified user.
     */
    public function activate(User $user): RedirectResponse
    {
        $user->update(['status' => 'active']);

        return back()->with('success', 'Pengguna berhasil diaktifkan.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus diri sendiri.');
        }

        // Check if user has active loans
        if ($user->loans()->whereIn('status', ['requested', 'approved', 'active'])->exists()) {
            return back()->with('error', 'Pengguna tidak dapat dihapus karena masih memiliki pinjaman aktif.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}

<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Librarian\DashboardController as LibrarianDashboardController;
use App\Http\Controllers\Librarian\LoanController as LibrarianLoanController;
use App\Http\Controllers\Librarian\ReviewController as LibrarianReviewController;
use App\Http\Controllers\Librarian\FineController as LibrarianFineController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public book catalog
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (All Users)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - redirects based on role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

/*
|--------------------------------------------------------------------------
| Member Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:member'])->group(function () {
    // Recommendations
    Route::get('/recommendations', [BookController::class, 'recommendations'])->name('books.recommendations');

    // Loans
    Route::get('/my-loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('/books/{book}/borrow', [LoanController::class, 'store'])->name('loans.store');
    Route::post('/loans/{loan}/cancel', [LoanController::class, 'cancel'])->name('loans.cancel');
    Route::post('/loans/{loan}/renew', [LoanController::class, 'renew'])->name('loans.renew');

    // Reviews
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/vote', [ReviewController::class, 'vote'])->name('reviews.vote');
    Route::delete('/reviews/{review}/vote', [ReviewController::class, 'removeVote'])->name('reviews.remove-vote');
    Route::get('/my-reviews', [ReviewController::class, 'userReviews'])->name('reviews.user-reviews');

    // Fines
    Route::get('/my-fines', [FineController::class, 'index'])->name('fines.index');
    Route::post('/fines/{fine}/pay', [FineController::class, 'pay'])->name('fines.pay');
});

/*
|--------------------------------------------------------------------------
| Librarian Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:librarian|admin'])->prefix('librarian')->name('librarian.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [LibrarianDashboardController::class, 'index'])->name('dashboard');

    // Loan Management
    Route::get('/loans', [LibrarianLoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/pending', [LibrarianLoanController::class, 'pending'])->name('loans.pending');
    Route::get('/loans/awaiting-pickup', [LibrarianLoanController::class, 'awaitingPickup'])->name('loans.awaiting-pickup');
    Route::get('/loans/active', [LibrarianLoanController::class, 'active'])->name('loans.active');
    Route::get('/loans/{loan}', [LibrarianLoanController::class, 'show'])->name('loans.show');
    Route::post('/loans/{loan}/approve', [LibrarianLoanController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/reject', [LibrarianLoanController::class, 'reject'])->name('loans.reject');
    Route::post('/loans/{loan}/pickup', [LibrarianLoanController::class, 'pickup'])->name('loans.pickup');
    Route::post('/loans/{loan}/return', [LibrarianLoanController::class, 'return'])->name('loans.return');

    // Review Moderation
    Route::get('/reviews', [LibrarianReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [LibrarianReviewController::class, 'show'])->name('reviews.show');
    Route::post('/reviews/{review}/approve', [LibrarianReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [LibrarianReviewController::class, 'reject'])->name('reviews.reject');

    // Fine Management
    Route::get('/fines', [LibrarianFineController::class, 'index'])->name('fines.index');
    Route::get('/fines/unpaid', [LibrarianFineController::class, 'unpaid'])->name('fines.unpaid');
    Route::post('/fines/{fine}/pay', [LibrarianFineController::class, 'pay'])->name('fines.pay');
    Route::post('/fines/{fine}/waive', [LibrarianFineController::class, 'waive'])->name('fines.waive');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Book Management
    Route::resource('books', AdminBookController::class);

    // User Management
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');

    // Category Management
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
});

require __DIR__.'/auth.php';

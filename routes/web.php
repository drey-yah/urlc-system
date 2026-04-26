<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ResearchProposalController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Redirect After Login (Role-Based)
|--------------------------------------------------------------------------
*/

Route::get('/redirect', function () {
    $role = Auth::user()->role;

    if ($role == 'admin') {
        return redirect('/admin');
    } elseif ($role == 'reviewer') {
        return redirect('/reviewer');
    } else {
        return redirect('/researcher');
    }
})->middleware(['auth']);

/*
|--------------------------------------------------------------------------
| Protected Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::get('/admin', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'role:admin']);

Route::get('/reviewer', function () {
    return view('reviewer.dashboard');
})->middleware(['auth', 'role:reviewer']);

Route::get('/researcher', function () {
    return view('researcher.dashboard');
})->middleware(['auth', 'role:researcher']);

/*
|--------------------------------------------------------------------------
| Researcher Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:researcher'])->group(function () {
    Route::get('/proposal/create', [ResearchProposalController::class, 'create'])->name('proposal.create');
    Route::post('/proposal/store', [ResearchProposalController::class, 'store'])->name('proposal.store');
    Route::get('/proposal/my', [ResearchProposalController::class, 'index'])->name('proposal.index');

    Route::get('/proposal/{id}/edit', [ResearchProposalController::class, 'edit'])->name('proposal.edit');
    Route::put('/proposal/{id}/update', [ResearchProposalController::class, 'update'])->name('proposal.update');
});

/*
|--------------------------------------------------------------------------
| Reviewer Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:reviewer'])->group(function () {
    Route::get('/reviewer/proposals', [ResearchProposalController::class, 'reviewerIndex'])->name('reviewer.proposals');
    Route::post('/reviewer/proposals/{id}/update-status', [ResearchProposalController::class, 'updateStatus'])->name('reviewer.proposals.updateStatus');
});

/*
|--------------------------------------------------------------------------
| 🔥 Admin Proposal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/proposals', [ResearchProposalController::class, 'adminIndex'])->name('admin.proposals');

    // ✅ NEW: Final decision route
    Route::post('/admin/proposals/{id}/final-decision', [ResearchProposalController::class, 'adminFinalDecision'])->name('admin.proposals.finalDecision');
});

/*
|--------------------------------------------------------------------------
| Default Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return redirect('/redirect');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Temporary Debug Route
|--------------------------------------------------------------------------
*/

Route::get('/whoami', function () {
    return auth()->user();
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
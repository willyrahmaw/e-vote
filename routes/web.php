<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ElectionReportController;
use App\Livewire\Admin\AuditLogIndex;
use App\Livewire\Admin\CandidateIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ElectionIndex;
use App\Livewire\Admin\ElectionWizard;
use App\Livewire\Admin\LiveVotingDashboard;
use App\Livewire\Admin\VoterIndex;
use App\Livewire\Admin\WebsiteSetting;
use App\Livewire\Display\LiveScreen;
use App\Livewire\Profile\ProfileIndex;
use App\Livewire\Public\BallotVerification;
use App\Livewire\Voter\ElectionList;
use App\Livewire\Voter\LiveResults;
use App\Livewire\Voter\VotingBallot;
use App\Livewire\Voter\VotingSuccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root to login / portal
Route::get('/', function () {
    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('voter.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Standalone Big Screen / Monitor Kiosk Routes (No Menu)
Route::get('/screen/{election?}', LiveScreen::class)->name('screen.live');
Route::get('/live/{election?}', LiveScreen::class)->name('screen.short');

// Zero-Knowledge Public Ballot Verification
Route::get('/verify/{token?}', BallotVerification::class)->name('ballot.verify');

// Public Live Result Route
Route::get('/live-results/{election}', LiveResults::class)->name('public.live-results');

// Shortcut /profile redirect
Route::get('/profile', function () {
    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->isAdmin()
            ? redirect()->route('admin.profile')
            : redirect()->route('voter.profile');
    }
    return redirect()->route('login');
})->middleware('auth')->name('profile');

// Admin Portal Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', ProfileIndex::class)->name('profile');
    Route::get('/settings', WebsiteSetting::class)->name('settings');
    Route::get('/elections', ElectionIndex::class)->name('elections.index');
    Route::get('/elections/wizard/{id?}', ElectionWizard::class)->name('elections.wizard');
    Route::get('/elections/{election}/report', [ElectionReportController::class, 'show'])->name('elections.report');
    Route::get('/candidates', CandidateIndex::class)->name('candidates.index');
    Route::get('/voters', VoterIndex::class)->name('voters.index');
    Route::get('/live-voting/{electionId?}', LiveVotingDashboard::class)->name('live-voting');
    Route::get('/audit-logs', AuditLogIndex::class)->name('audit-logs');
});

// Voter Portal Routes
Route::middleware(['auth', 'voter'])->prefix('portal')->name('voter.')->group(function () {
    Route::get('/dashboard', ElectionList::class)->name('dashboard');
    Route::get('/profile', ProfileIndex::class)->name('profile');
    Route::get('/elections/{election}/vote', VotingBallot::class)->name('elections.vote');
    Route::get('/elections/{election}/success', VotingSuccess::class)->name('elections.success');
    Route::get('/elections/{election}/results', LiveResults::class)->name('elections.results');
});

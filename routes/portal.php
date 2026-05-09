<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Portal\DashboardController as PortalDashboard;
use App\Http\Controllers\Portal\ProfileController as PortalProfile;
use Illuminate\Support\Facades\Route;

// ── Member Portal ──────────────────────────────────────────────────────────
Route::prefix('portal')
    ->name('portal.')
    ->middleware(['auth', 'verified', 'require.2fa', 'portal.access'])
    ->group(function () {

        Route::get('/dashboard', [PortalDashboard::class, 'index'])->name('dashboard');
        Route::get('/profile', [PortalProfile::class,   'show'])->name('profile');
        Route::get('/security', [PortalProfile::class,   'security'])->name('security');

        // Placeholder routes for future phases
        Route::get('/events', fn () => view('portal.placeholder', ['title' => 'Events',    'phase' => 4]))->name('events');
        Route::get('/documents', fn () => view('portal.placeholder', ['title' => 'Documents', 'phase' => 5]))->name('documents');

        // NOTE: /portal/donations is now handled by the Donations module
        // See app/Modules/Donations/Routes/web.php → portal.donations.*
    });

// Redirect /portal to /portal/dashboard
Route::get('/portal', fn () => redirect()->route('portal.dashboard'));

// ── Admin Panel ────────────────────────────────────────────────────────────
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'require.2fa', 'role:super-admin|admin|team'])
    ->group(function () {

        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Members
        Route::get('/members', fn () => view('admin.members.index'))->name('members');
        Route::get('/members/create', fn () => view('admin.members.create'))->name('members.create');

        // Placeholder routes for future phases
        Route::get('/events', fn () => view('admin.placeholder', ['title' => 'Events',       'phase' => 4]))->name('events');
        Route::get('/blog', fn () => view('admin.placeholder', ['title' => 'Blog',         'phase' => 4]))->name('blog');
        Route::get('/documents', fn () => view('admin.placeholder', ['title' => 'Documents',    'phase' => 5]))->name('documents');
        Route::get('/newsletter', fn () => view('admin.placeholder', ['title' => 'Newsletter',   'phase' => 6]))->name('newsletter');
        Route::get('/volunteering', fn () => view('admin.placeholder', ['title' => 'Volunteering', 'phase' => 6]))->name('volunteering');
        Route::get('/settings', fn () => view('admin.settings'))->name('settings');

        // NOTE: /admin/donations is now handled by the Donations module
        // See app/Modules/Donations/Routes/web.php → admin.donations.*

        // NOTE: /admin/themes and /admin/modules are now handled by DashboardController
        // See routes/admin.php → admin.themes, admin.modules
    });

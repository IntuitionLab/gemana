<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Gemana Super-Admin Routes
|--------------------------------------------------------------------------
| All routes here are prefixed with the value of config('gemana.admin_prefix')
| which defaults to 'gemana-admin'. They are loaded by CoreServiceProvider.
|
| TODO Phase 2: Add auth middleware — ['auth', 'role:Super-Admin']
*/

Route::prefix(config('gemana.admin_prefix', 'gemana-admin'))
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Modules
        Route::get('/modules', [DashboardController::class, 'modules'])->name('modules');
        Route::post('/modules/{name}/toggle', [DashboardController::class, 'toggleModule'])->name('modules.toggle');

        // Themes
        Route::get('/themes', [DashboardController::class, 'themes'])->name('themes');
        Route::post('/themes/{name}/activate', [DashboardController::class, 'activateTheme'])->name('themes.activate');

    });

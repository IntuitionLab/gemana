<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasRole('super-admin') || $user->hasAnyRole(['admin', 'team'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('portal.dashboard');
    }

    return view('welcome');
});

// Portal & Admin routes
require __DIR__.'/portal.php';

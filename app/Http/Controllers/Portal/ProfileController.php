<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show()
    {
        return view('portal.profile');
    }

    public function security()
    {
        return view('portal.security');
    }
}

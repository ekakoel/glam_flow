<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LandingController extends Controller
{
    public function home(): View
    {
        return view('landing.home');
    }

    public function pricing(): View
    {
        return view('landing.pricing');
    }

    public function features(): View
    {
        return view('landing.features');
    }
}

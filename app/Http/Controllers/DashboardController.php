<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    function DashboardPage(): RedirectResponse
    {
        // Redirect to the enhanced dashboard
        return redirect()->route('dashboard');
    }
}

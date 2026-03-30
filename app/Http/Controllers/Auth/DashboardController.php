<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $board = $user->boards()->first();

        return view('dashboard', compact('user', 'board'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /*
        Get games from the user's library
        */
        $games = $user->games()
            ->withPivot('status')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard', [
            'user' => $user,
            'games' => $games
        ]);
    }
}
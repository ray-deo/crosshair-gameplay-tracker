<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index()
    {
        $games = Auth::user()
            ->games()
            ->withPivot('status', 'progress')
            ->latest()
            ->get();

        return view('library', compact('games'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'cover' => 'nullable',
            'rawg_id' => 'required',
        ]);

        $game = Game::firstOrCreate(
            ['rawg_id' => $request->rawg_id],
            [
                'title' => $request->title,
                'cover_url' => $request->cover ?? null,
            ]
        );

        auth()->user()->games()->syncWithoutDetaching([
            $game->id => [
                'status' => 'backlog',
                'progress' => 0,
            ]
        ]);

        return redirect('/library');
    }

    public function destroy($id)
{
    $user = auth()->user();

    // detach game from user (pivot table)
    $user->games()->detach($id);

    return back()->with('success', 'Game removed from library');
}
}
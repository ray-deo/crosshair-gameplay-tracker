<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserGame;

class GameProgressController extends Controller
{
    // 🎮 START GAME
    public function start($id)
    {
        $entry = UserGame::where('user_id', Auth::id())
            ->where('game_id', $id)
            ->firstOrFail();

        $entry->update([
            'status' => 'playing',
            'started_at' => now()
        ]);

        return back();
    }

    // 📊 UPDATE PROGRESS
    public function updateProgress(Request $request, $id)
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $entry = UserGame::where('user_id', Auth::id())
            ->where('game_id', $id)
            ->firstOrFail();

        $entry->update([
            'progress' => $request->progress,
            'status' => $request->progress == 100 ? 'completed' : 'playing'
        ]);

        if ($request->progress == 100) {
            $entry->update([
                'completed_at' => now()
            ]);
        }

        return back();
    }

    // ✅ COMPLETE GAME
    public function complete($id)
    {
        $entry = UserGame::where('user_id', Auth::id())
            ->where('game_id', $id)
            ->firstOrFail();

        $entry->update([
            'status' => 'completed',
            'progress' => 100,
            'completed_at' => now()
        ]);

        return back();
    }
}
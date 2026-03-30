<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserGame;

class GameProgressController extends Controller
{
    public function start($id)
    {
        $entry = UserGame::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'game_id' => $id,
            ],
            [
                'status' => 'backlog',
                'progress' => 0,
            ]
        );

        $entry->update([
            'status' => 'playing',
            'started_at' => $entry->started_at ?? now(),
            'completed_at' => null,
        ]);

        return back();
    }

    public function updateProgress(Request $request, $id)
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $entry = UserGame::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'game_id' => $id,
            ],
            [
                'status' => 'backlog',
                'progress' => 0,
            ]
        );

        $progress = (int) $request->progress;
        $status = $progress === 100 ? 'completed' : ($progress > 0 ? 'playing' : 'backlog');

        $entry->update([
            'progress' => $progress,
            'status' => $status,
            'started_at' => $progress > 0 ? ($entry->started_at ?? now()) : null,
            'completed_at' => $progress === 100 ? now() : null,
        ]);

        return back();
    }

    public function complete($id)
    {
        $entry = UserGame::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'game_id' => $id,
            ],
            [
                'status' => 'backlog',
                'progress' => 0,
            ]
        );

        $entry->update([
            'status' => 'completed',
            'progress' => 100,
            'started_at' => $entry->started_at ?? now(),
            'completed_at' => now(),
        ]);

        return back();
    }
}
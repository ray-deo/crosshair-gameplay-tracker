<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Note;
use App\Models\Screenshot;


class GameController extends Controller
{
    public function show($id)
    {
        $game = Game::findOrFail($id);

        $data = null;
        if (!empty($game->rawg_id)) {
            $data = app(\App\Services\RawgService::class)
                ->getGameDetails($game->rawg_id);
        }

        $notes = Note::where('game_id', $id)
            ->where('user_id', auth()->id())
            ->get();

        $screenshots = Screenshot::where('game_id', $id)
            ->where('user_id', auth()->id())
            ->get();

        return view('game.show', [
            'game' => $game,
            'data' => $data,
            'notes' => $notes,
            'screenshots' => $screenshots,
        ]);
    }
}
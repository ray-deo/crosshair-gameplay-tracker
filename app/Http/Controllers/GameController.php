<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Note;
use App\Models\Screenshot;
use App\Models\UserGame;
use App\Models\Video;


class GameController extends Controller
{
    public function show($id)
    {
        $game = Game::findOrFail($id);

        $data = null;
        try {
            if (!empty($game->rawg_id) && strpos((string) $game->rawg_id, 'steam_') !== 0) {
                $data = app(\App\Services\RawgService::class)
                    ->getGameDetails($game->rawg_id);
            }

            if (!$data) {
                $steamAppId = $game->steam_appid;

                if (!$steamAppId && !empty($game->rawg_id) && strpos((string) $game->rawg_id, 'steam_') === 0) {
                    $steamAppId = (int) str_replace('steam_', '', (string) $game->rawg_id);
                }

                if ($steamAppId) {
                    $data = app(\App\Services\SteamService::class)
                        ->getGameDetailsForShow($steamAppId);
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Game detail metadata fetch failed: ' . $e->getMessage(), [
                'game_id' => $game->id,
                'rawg_id' => $game->rawg_id,
                'steam_appid' => $game->steam_appid,
            ]);

            $data = null;
        }

        $notes = Note::where('game_id', $id)
            ->where('user_id', auth()->id())
            ->get();

        $screenshots = Screenshot::where('game_id', $id)
            ->where('user_id', auth()->id())
            ->get();

        $videos = Video::where('game_id', $id)
            ->where('user_id', auth()->id())
            ->get();

        $userGame = UserGame::where('user_id', auth()->id())
            ->where('game_id', $id)
            ->first();

        return view('game.show', [
            'game' => $game,
            'data' => $data,
            'notes' => $notes,
            'screenshots' => $screenshots,
            'videos' => $videos,
            'userGame' => $userGame,
        ]);
    }
}
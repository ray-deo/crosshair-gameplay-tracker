<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $viewData = [
            'stats' => null,
            'continueGames' => collect(),
            'randomGameId' => null,
            'recommendedGameId' => null,
        ];

        if ($user) {
            $hasFavoriteColumn = Schema::hasColumn('user_games', 'is_favorite');

            $baseGamesQuery = $user->games()
                ->select('games.id', 'games.title', 'games.cover_url', 'games.steam_appid')
                ->withPivot('status', 'progress')
                ->withTimestamps();

            $totalGames = (clone $baseGamesQuery)->count();
            $avgProgress = (int) round((float) DB::table('user_games')
                ->where('user_id', $user->id)
                ->avg('progress'));

            $favorites = $hasFavoriteColumn
                ? (int) DB::table('user_games')->where('user_id', $user->id)->where('is_favorite', 1)->count()
                : 0;

            $backlog = (int) DB::table('user_games')
                ->where('user_id', $user->id)
                ->whereIn('status', ['backlog', 'planning'])
                ->count();

            $completed = (int) DB::table('user_games')
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->count();

            $continueGames = (clone $baseGamesQuery)
                ->orderBy('user_games.updated_at', 'desc')
                ->take(8)
                ->get();

            $randomGameId = DB::table('user_games')
                ->where('user_id', $user->id)
                ->whereIn('status', ['backlog', 'planning', 'playing'])
                ->inRandomOrder()
                ->value('game_id');

            $recommendedGameId = DB::table('user_games')
                ->where('user_id', $user->id)
                ->whereNotIn('status', ['completed', 'dropped'])
                ->orderByDesc('progress')
                ->value('game_id');

            $viewData = [
                'stats' => [
                    'total' => $totalGames,
                    'favorites' => $favorites,
                    'backlog' => $backlog,
                    'completed' => $completed,
                    'avg_progress' => $avgProgress,
                ],
                'continueGames' => $continueGames,
                'randomGameId' => $randomGameId,
                'recommendedGameId' => $recommendedGameId,
            ];
        }

        return view('landing', $viewData);
    }
}

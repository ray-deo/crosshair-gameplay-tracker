<?php

namespace App\Http\Controllers;

use App\Models\Screenshot;
use App\Models\Video;
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
            'mediaItems' => collect(),
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

            $screenshots = Screenshot::where('user_id', $user->id)
                ->with('game')
                ->latest()
                ->take(8)
                ->get()
                ->map(fn ($s) => [
                    'type' => 'screenshot',
                    'path' => $s->image_path,
                    'game_id' => $s->game_id,
                    'game_title' => $s->game->title ?? 'Unknown',
                    'created_at' => $s->created_at,
                ]);

            $videos = Video::where('user_id', $user->id)
                ->with('game')
                ->latest()
                ->take(8)
                ->get()
                ->map(fn ($v) => [
                    'type' => 'video',
                    'path' => $v->video_path,
                    'game_id' => $v->game_id,
                    'game_title' => $v->game->title ?? 'Unknown',
                    'created_at' => $v->created_at,
                ]);

            $mediaItems = $screenshots
                ->merge($videos)
                ->sortByDesc('created_at')
                ->take(12)
                ->values();

            $viewData = [
                'stats' => [
                    'total' => $totalGames,
                    'favorites' => $favorites,
                    'backlog' => $backlog,
                    'completed' => $completed,
                    'avg_progress' => $avgProgress,
                ],
                'continueGames' => $continueGames,
                'mediaItems' => $mediaItems,
                'randomGameId' => $randomGameId,
                'recommendedGameId' => $recommendedGameId,
            ];
        }

        return view('landing', $viewData);
    }
}

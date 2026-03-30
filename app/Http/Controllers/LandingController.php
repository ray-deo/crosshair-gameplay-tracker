<?php

namespace App\Http\Controllers;

use App\Models\Note;
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
            'checklist' => null,
            'activities' => collect(),
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

            $hasFirstGame = $totalGames > 0;
            $hasNote = Note::where('user_id', $user->id)->exists();
            $hasScreenshot = Screenshot::where('user_id', $user->id)->exists();
            $hasFavorite = $hasFavoriteColumn
                ? DB::table('user_games')->where('user_id', $user->id)->where('is_favorite', 1)->exists()
                : false;

            $completedChecklist = collect([$hasFirstGame, $hasNote, $hasScreenshot, $hasFavorite])
                ->filter(fn ($v) => $v)
                ->count();

            $checklistCompletion = (int) round(($completedChecklist / 4) * 100);

            $noteEvents = Note::where('user_id', $user->id)
                ->latest()
                ->take(4)
                ->get()
                ->map(function ($note) {
                    return [
                        'label' => 'Added a note',
                        'game_id' => $note->game_id,
                        'at' => $note->created_at,
                    ];
                });

            $screenshotEvents = Screenshot::where('user_id', $user->id)
                ->latest()
                ->take(4)
                ->get()
                ->map(function ($shot) {
                    return [
                        'label' => 'Uploaded a screenshot',
                        'game_id' => $shot->game_id,
                        'at' => $shot->created_at,
                    ];
                });

            $videoEvents = Video::where('user_id', $user->id)
                ->latest()
                ->take(4)
                ->get()
                ->map(function ($video) {
                    return [
                        'label' => 'Uploaded a video',
                        'game_id' => $video->game_id,
                        'at' => $video->created_at,
                    ];
                });

            $progressEvents = DB::table('user_games')
                ->join('games', 'games.id', '=', 'user_games.game_id')
                ->where('user_games.user_id', $user->id)
                ->orderByDesc('user_games.updated_at')
                ->limit(6)
                ->get(['user_games.game_id', 'games.title', 'user_games.status', 'user_games.updated_at'])
                ->map(function ($row) {
                    return [
                        'label' => 'Updated status: ' . ucfirst((string) $row->status),
                        'game_id' => $row->game_id,
                        'at' => $row->updated_at,
                    ];
                });

            $activities = $noteEvents
                ->merge($screenshotEvents)
                ->merge($videoEvents)
                ->merge($progressEvents)
                ->sortByDesc(function ($event) {
                    return strtotime((string) $event['at']);
                })
                ->take(8)
                ->values();

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
                'checklist' => [
                    'has_first_game' => $hasFirstGame,
                    'has_note' => $hasNote,
                    'has_screenshot' => $hasScreenshot,
                    'has_favorite' => $hasFavorite,
                    'completion' => $checklistCompletion,
                ],
                'activities' => $activities,
                'randomGameId' => $randomGameId,
                'recommendedGameId' => $recommendedGameId,
            ];
        }

        return view('landing', $viewData);
    }
}

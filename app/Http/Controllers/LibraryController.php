<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\SteamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'favorites');
        $hasFavoritesColumn = Schema::hasColumn('user_games', 'is_favorite');

        $gamesQuery = Auth::user()
            ->games()
            ->withPivot(...($hasFavoritesColumn ? ['status', 'progress', 'is_favorite'] : ['status', 'progress']));

        switch ($sort) {
            case 'title_asc':
                $gamesQuery->orderBy('games.title', 'asc');
                break;
            case 'title_desc':
                $gamesQuery->orderBy('games.title', 'desc');
                break;
            case 'recent':
                $gamesQuery->orderByPivot('updated_at', 'desc');
                break;
            case 'status':
                $gamesQuery->orderByPivot('status', 'asc')
                    ->orderBy('games.title', 'asc');
                break;
            case 'favorites':
            default:
                $sort = 'favorites';
                if ($hasFavoritesColumn) {
                    $gamesQuery->orderByPivot('is_favorite', 'desc')
                        ->orderByPivot('updated_at', 'desc');
                } else {
                    $gamesQuery->orderByPivot('updated_at', 'desc');
                }
                break;
        }

        $games = $gamesQuery->get();

        return view('library', compact('games', 'sort', 'hasFavoritesColumn'));
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
            ],
        ]);

        return redirect('/library');
    }

    public function destroy($id)
    {
        $user = auth()->user();

        $user->games()->detach($id);

        return back()->with('success', 'Game removed from library');
    }

    public function toggleFavorite($id)
    {
        if (!Schema::hasColumn('user_games', 'is_favorite')) {
            return back()->with('error', 'Favorites are not available yet. Run migrations in production.');
        }

        $user = auth()->user();

        $existing = $user->games()->withPivot('is_favorite')->where('game_id', $id)->first();
        if (!$existing) {
            return back()->with('error', 'Game not found in your library.');
        }

        $isFavorite = (bool) ($existing->pivot->is_favorite ?? false);

        $user->games()->updateExistingPivot($id, [
            'is_favorite' => !$isFavorite,
            'updated_at' => now(),
        ]);

        return back()->with('success', !$isFavorite ? 'Added to favorites.' : 'Removed from favorites.');
    }

    public function importFromSteam(Request $request)
    {
        $request->validate([
            'steam_profile' => 'required|string',
        ]);

        $steam = new SteamService();
        $steamId = $steam->resolveSteamId($request->steam_profile);

        if (!$steamId) {
            return back()->with('steam_error', 'Invalid Steam profile. Use a profile URL or Steam ID.');
        }

        try {
            $ownedGames = $steam->getOwnedGames($steamId);

            if (empty($ownedGames)) {
                return back()->with('steam_error', 'No games found. Your Steam game details may be private.');
            }

            $gamesToImport = array_slice($ownedGames, 0, 50);
            $appIds = array_column($gamesToImport, 'appid');
            $gameDetails = $steam->getGameDetailsBatch($appIds);

            $user = auth()->user();
            $imported = 0;
            $hasSteamAppIdColumn = Schema::hasColumn('games', 'steam_appid');

            foreach ($gameDetails as $details) {
                if (!$details || empty($details['name'])) {
                    continue;
                }

                $lookup = $hasSteamAppIdColumn
                    ? ['steam_appid' => $details['steam_appid']]
                    : ['rawg_id' => 'steam_' . $details['steam_appid']];

                $defaults = [
                    'title' => $details['name'],
                    'cover_url' => $details['header_image'] ?? null,
                ];

                if (!$hasSteamAppIdColumn) {
                    $defaults['rawg_id'] = 'steam_' . $details['steam_appid'];
                }

                $game = Game::firstOrCreate($lookup, $defaults);

                if (!$user->games()->where('game_id', $game->id)->exists()) {
                    $user->games()->syncWithoutDetaching([
                        $game->id => [
                            'status' => 'backlog',
                            'progress' => 0,
                        ],
                    ]);
                    $imported++;
                }
            }

            return back()->with('steam_success', "Imported {$imported} games from Steam.");
        } catch (\Throwable $e) {
            \Log::error('Steam import failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->with('steam_error', 'Steam import failed. Please try again.');
        }
    }
}

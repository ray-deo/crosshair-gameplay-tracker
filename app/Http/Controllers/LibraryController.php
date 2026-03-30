<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\SteamService;
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

    public function importFromSteam(Request $request)
    {
        $request->validate([
            'steam_profile' => 'required|string',
        ]);

        $steam = new SteamService();
        $steamId = $steam->resolveSteamId($request->steam_profile);

        if (!$steamId) {
            return back()->with('steam_error', 'Could not find Steam profile. Try using your Steam ID (17 digits) or full profile URL.');
        }

        try {
            $ownedGames = $steam->getOwnedGames($steamId);

            if (empty($ownedGames)) {
                return back()->with('steam_error', 'No games found or profile is private. Make sure your Steam library is set to public.');
            }

            // Fetch top 50 games (to respect rate limits)
            $gamesToImport = array_slice($ownedGames, 0, 50);
            $gameDetails = $steam->getGameDetailsBatch(array_column($gamesToImport, 'appid'));

            $user = auth()->user();
            $imported = 0;

            foreach ($gameDetails as $details) {
                if (!$details['name']) continue;

                // Check if game already exists by steam_appid
                $game = Game::firstOrCreate(
                    ['steam_appid' => $details['steam_appid']],
                    [
                        'title' => $details['name'],
                        'cover_url' => $details['header_image'] ?? null,
                    ]
                );

                // Add to user's library if not already there
                if (!$user->games()->where('game_id', $game->id)->exists()) {
                    $user->games()->syncWithoutDetaching([
                        $game->id => [
                            'status' => 'backlog',
                            'progress' => 0,
                        ]
                    ]);
                    $imported++;
                }
            }

            return back()->with('steam_success', "Imported {$imported} games from your Steam library!");
        } catch (\Exception $e) {
            \Log::error('Steam import failed: ' . $e->getMessage());
            return back()->with('steam_error', 'Import failed: ' . $e->getMessage());
        }
    }
}
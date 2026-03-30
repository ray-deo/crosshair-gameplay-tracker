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
                $steam = new SteamService();
                \Log::info('Steam import started for: ' . $request->steam_profile);

        $game = Game::firstOrCreate(
            ['rawg_id' => $request->rawg_id],
                if (!$steamId) {
                    \Log::warning('Could not resolve Steam ID from: ' . $request->steam_profile);
                    return back()->with('steam_error', 'Invalid Steam profile. Try: https://steamcommunity.com/profiles/YOUR_ID or https://steamcommunity.com/id/username or just your Steam ID.');
                'cover_url' => $request->cover ?? null,
            ]
                \Log::info('Resolved Steam ID: ' . $steamId);
        );

        auth()->user()->games()->syncWithoutDetaching([
                    \Log::info('Found ' . count($ownedGames) . ' games');
            $game->id => [
                'status' => 'backlog',
                        return back()->with('steam_error', 'No games found - profile might be private. Go to Steam > Profile > Edit Profile > Privacy Settings > Library visibility = PUBLIC.');
            ]
        ]);

        return redirect('/library');
                    $appIds = array_column($gamesToImport, 'appid');
                    \Log::info('Fetching details for ' . count($appIds) . ' apps: ' . implode(',', $appIds));
    }

                    \Log::info('Got details for ' . count($gameDetails) . ' games');
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
                    \Log::info('Successfully imported ' . $imported . ' games');
                    return back()->with('steam_success', "✓ Imported {$imported} games from Steam! (up to 50 per import)");

                    \Log::error('Steam import failed: ' . $e->getFile() . ':' . $e->getLine() . ' ' . $e->getMessage());
                    return back()->with('steam_error', 'Error: ' . $e->getMessage());
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
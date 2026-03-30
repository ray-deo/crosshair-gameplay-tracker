<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\RawgService;
use App\Models\Note;
use App\Models\Screenshot;


class GameController extends Controller
{
    public function show($id)
{
    $game = Game::findOrFail($id);

// ✅ SAFE RAWG CALL (STEP 2)
$data = null;

if (!empty($game->rawg_id)) {
    $data = app(\App\Services\RawgService::class)
    ->getGameDetails($game->rawg_id);


}

    // NOTES
    $notes = Note::where('game_id', $id)
        ->where('user_id', auth()->id())
        ->get();

    // 🔥 FIX: ADD SCREENSHOTS
    $screenshots = Screenshot::where('game_id', $id)->get();

    return view('game.show', [
        'game' => $game,
        'data' => $data,
        'notes' => $notes,
        'screenshots' => $screenshots, 
    ]);
}
    public function uploadScreenshots(Request $request, $id)
{
    if ($request->hasFile('screenshots')) {

        foreach ($request->file('screenshots') as $file) {

            $path = $file->store('screenshots', 'public');

            \App\Models\Screenshot::create([
                'game_id' => $id,
                'path' => $path
            ]);
        }
    }

    return back();
}
}
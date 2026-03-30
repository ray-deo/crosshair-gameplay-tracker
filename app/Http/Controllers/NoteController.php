<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(Request $request, $gameId)
    {
        $request->validate([
            'content' => 'required|string|max:2000'
        ]);

        Note::create([
            'user_id' => auth()->id(),
            'game_id' => $gameId,
            'content' => $request->content
        ]);

        return back();
    }
    public function update(Request $request, $id)
{
    $request->validate([
        'content' => 'required|string|max:2000'
    ]);

    $note = Note::findOrFail($id);

    if ($note->user_id !== auth()->id()) {
        abort(403);
    }

    $note->update([
        'content' => $request->content
    ]);

    return back();
}

    public function destroy($id)
    {
        $note = Note::findOrFail($id);

        if ($note->user_id !== auth()->id()) {
            abort(403);
        }

        $note->delete();

        return back();
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Screenshot;
use Illuminate\Http\Request;

class ScreenshotController extends Controller
{   
    
public function upload(Request $request, $id)
{
    if (!$request->hasFile('screenshots')) {
        return back()->with('error', 'No file uploaded');
    }

    foreach ($request->file('screenshots') as $file) {

        $path = $file->store('screenshots', 'public');

        Screenshot::create([
    'user_id' => auth()->id(),  // 🔥 ADD THIS
    'game_id' => $id,
    'image_path' => $path
]);
    }

    return back();
}

   
    public function destroy($id)
    {
        $shot = Screenshot::findOrFail($id);

        if ($shot->user_id !== auth()->id()) {
            abort(403);
        }

        unlink(storage_path('app/public/' . $shot->image_path));

        $shot->delete();

        return back();
    }
}
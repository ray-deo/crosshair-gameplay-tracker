<?php

namespace App\Http\Controllers;

use App\Models\Screenshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScreenshotController extends Controller
{   
    public function upload(Request $request, $id)
    {
        $request->validate([
            'screenshots'   => 'required',
            'screenshots.*' => 'file|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        try {
            $files = $request->file('screenshots');

            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }

                $path = $file->store('screenshots', 'public');

                Screenshot::create([
                    'user_id'    => auth()->id(),
                    'game_id'    => $id,
                    'image_path' => $path,
                    'path'       => $path,
                ]);
            }

            return back()->with('screenshot_success', 'Screenshot uploaded successfully.');
        } catch (\Throwable $e) {
            \Log::error('Screenshot upload failed: ' . $e->getMessage());
            return back()->with('screenshot_error', 'Upload failed: ' . $e->getMessage());
        }
    }

   
    public function destroy($id)
    {
        $shot = Screenshot::findOrFail($id);

        if ($shot->user_id !== auth()->id()) {
            abort(403);
        }

        if ($shot->image_path && Storage::disk('public')->exists($shot->image_path)) {
            Storage::disk('public')->delete($shot->image_path);
        }

        $shot->delete();

        return back();
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function upload(Request $request, $id)
    {
        $request->validate([
            'videos' => 'required',
            'videos.*' => 'file|mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo|max:51200',
        ]);

        try {
            $files = $request->file('videos');

            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }

                $path = $file->store('videos', 'public');

                Video::create([
                    'user_id' => auth()->id(),
                    'game_id' => $id,
                    'video_path' => $path,
                    'path' => $path,
                ]);
            }

            return back()->with('video_success', 'Video uploaded successfully.');
        } catch (\Throwable $e) {
            \Log::error('Video upload failed: ' . $e->getMessage());
            return back()->with('video_error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);

        if ($video->user_id !== auth()->id()) {
            abort(403);
        }

        if ($video->video_path && Storage::disk('public')->exists($video->video_path)) {
            Storage::disk('public')->delete($video->video_path);
        }

        $video->delete();

        return back();
    }
}

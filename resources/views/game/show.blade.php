{{-- filepath: c:\Users\raiha\gaming-progress-tracker\resources\views\game\show.blade.php --}}
@extends('layouts.app')

@section('content')

<div class="hero-wrapper">
    <div class="hero-bg"
         style="background-image: url('{{ $data['background_image'] ?? $game->cover_url ?? 'https://placehold.co/1200x600?text=No+Image' }}');">
        <div class="hero-overlay"></div>
    </div>
</div>

<div class="container">
    @php
        $steamCover = $game->steam_appid
            ? 'https://cdn.cloudflare.steamstatic.com/steam/apps/' . $game->steam_appid . '/library_600x900_2x.jpg'
            : null;

        $mainCover = $steamCover ?? ($game->cover_url ?? ($data['background_image'] ?? 'https://placehold.co/600x800?text=No+Image'));
        $fallbackCover = $game->cover_url ?? ($data['background_image'] ?? 'https://placehold.co/600x800?text=No+Image');
    @endphp

    <div class="hero-content">
        <img
            src="{{ $mainCover }}"
            onerror="this.onerror=null; this.src='{{ $fallbackCover }}';"
            class="cover"
            alt="{{ $game->title }}"
        >

        <div class="info">
            <h1>{{ $game->title }}</h1>

            @php
                $currentStatus = $userGame->status ?? 'backlog';
                $currentProgress = (int) ($userGame->progress ?? 0);
            @endphp

            <div class="progress-panel">
                <div class="progress-topline">
                    <span class="status-pill">Status: {{ ucfirst($currentStatus) }}</span>
                    <span class="progress-label">{{ $currentProgress }}%</span>
                </div>
                <div class="progress-track"><span style="width: {{ $currentProgress }}%"></span></div>

                <div class="progress-actions">
                    <form method="POST" action="{{ route('game.start', $game->id) }}">
                        @csrf
                        <button type="submit" class="add-btn">Start</button>
                    </form>

                    <form method="POST" action="{{ route('game.complete', $game->id) }}">
                        @csrf
                        <button type="submit" class="add-btn">Mark Complete</button>
                    </form>
                </div>

                <form method="POST" action="{{ route('game.progress', $game->id) }}" class="progress-form">
                    @csrf
                    <input
                        type="range"
                        min="0"
                        max="100"
                        name="progress"
                        value="{{ $currentProgress }}"
                        oninput="this.nextElementSibling.textContent = this.value + '%'"
                    >
                    <span class="range-value">{{ $currentProgress }}%</span>
                    <button type="submit" class="add-btn">Update Progress</button>
                </form>
            </div>

            @if(isset($data['released']))
                <span class="badge">Released: {{ $data['released'] }}</span>
            @endif

            <p class="description">
                {{ strip_tags($data['description_raw'] ?? 'No description available.') }}
            </p>

            <div class="meta">
                <strong>Genres:</strong>
                @forelse($data['genres'] ?? [] as $genre)
                    <span class="tag">{{ $genre['name'] }}</span>
                @empty
                    <span class="tag">Unknown</span>
                @endforelse
            </div>

            <div class="meta">
                <strong>Platforms:</strong>
                @forelse($data['platforms'] ?? [] as $platform)
                    <span class="tag">{{ $platform['platform']['name'] }}</span>
                @empty
                    <span class="tag">Unknown</span>
                @endforelse
            </div>
        </div>
    </div>

    <div class="sections">
        <div class="box">
            <h3>NOTES</h3>

            <form method="POST" action="{{ route('notes.store', $game->id) }}">
                @csrf
                <textarea name="content" class="note-input" placeholder="Write a note..."></textarea>
                <button type="submit" class="add-btn">+ Add Note</button>
            </form>

            @forelse($notes as $note)
                <div class="note">
                    <div id="note-view-{{ $note->id }}">
                        <p>{{ $note->content }}</p>

                        <button type="button" onclick="editNote({{ $note->id }})" class="edit-btn">Edit</button>

                        <form method="POST" action="{{ route('notes.destroy', $note->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </div>

                    <div id="note-edit-{{ $note->id }}" style="display:none;">
                        <form method="POST" action="{{ route('notes.update', $note->id) }}">
                            @csrf
                            @method('PUT')

                            <textarea name="content" class="note-input">{{ $note->content }}</textarea>

                            <button type="submit" class="add-btn">Save</button>
                            <button type="button" onclick="cancelEdit({{ $note->id }})" class="cancel-btn">Cancel</button>
                        </form>
                    </div>
                </div>
            @empty
                <p>No notes yet.</p>
            @endforelse
        </div>

        <div class="screenshots-box">
            <h3>SCREENSHOTS</h3>

            <form method="POST"
                  action="{{ route('screenshots.upload', $game->id) }}"
                  enctype="multipart/form-data">
                @csrf

                @if(session('screenshot_success'))
                    <p class="upload-success">{{ session('screenshot_success') }}</p>
                @endif

                @if(session('screenshot_error'))
                    <p class="upload-error">{{ session('screenshot_error') }}</p>
                @endif

                @if($errors->has('screenshots') || $errors->has('screenshots.*'))
                    <p class="upload-error">{{ $errors->first('screenshots') ?: $errors->first('screenshots.*') }}</p>
                @endif

                <label class="file-pick-label">
                    <input type="file" name="screenshots[]" multiple accept="image/*">
                    <span>Choose File</span>
                </label>

                <div class="upload-actions">
                    <button type="submit">Upload</button>
                </div>
            </form>

            <div class="screenshots-grid">
                @forelse($screenshots as $shot)
                    <div class="shot-card">
                        <img src="{{ asset('storage/' . $shot->image_path) }}" class="shot-img" alt="Screenshot">

                        <form method="POST" action="{{ route('screenshots.destroy', $shot->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-shot">X</button>
                        </form>
                    </div>
                @empty
                    <p>No screenshots yet.</p>
                @endforelse
            </div>
        </div>

        <div class="videos-box">
            <h3>VIDEOS</h3>

            <form method="POST"
                  action="{{ route('videos.upload', $game->id) }}"
                  enctype="multipart/form-data">
                @csrf

                @if(session('video_success'))
                    <p class="upload-success">{{ session('video_success') }}</p>
                @endif

                @if(session('video_error'))
                    <p class="upload-error">{{ session('video_error') }}</p>
                @endif

                @if($errors->has('videos') || $errors->has('videos.*'))
                    <p class="upload-error">{{ $errors->first('videos') ?: $errors->first('videos.*') }}</p>
                @endif

                <label class="file-pick-label">
                    <input type="file" name="videos[]" multiple accept="video/*">
                    <span>Choose File</span>
                </label>

                <div class="upload-actions">
                    <button type="submit">Upload</button>
                </div>
            </form>

            <div class="videos-grid">
                @forelse($videos as $video)
                    <div class="video-card">
                        <video controls preload="metadata" class="video-player">
                            <source src="{{ asset('storage/' . $video->video_path) }}">
                            Your browser does not support video playback.
                        </video>

                        <form method="POST" action="{{ route('videos.destroy', $video->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-shot">X</button>
                        </form>
                    </div>
                @empty
                    <p>No videos yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

<style>
:root {
    --game-accent: var(--accent, #00ff9c);
    --game-text: var(--text, #00ff9c);
    --game-bg: var(--bg, #000000);
    --game-card: rgba(0, 0, 0, 0.72);
}

.hero-wrapper {
    width: 100vw;
    margin-left: calc(-50vw + 50%);
    margin-top: -38px;
    position: relative;
}

.hero-wrapper::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 140px;
    background: linear-gradient(to bottom, transparent, var(--game-bg));
    pointer-events: none;
}

.hero-bg {
    height: 430px;
    background-size: cover;
    background-position: center;
    position: relative;
    filter: blur(5px) brightness(0.25) saturate(1.1);
    transform: scale(1.06);
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 18% 22%, color-mix(in srgb, var(--game-accent) 14%, transparent), transparent 45%),
        radial-gradient(circle at 80% 10%, color-mix(in srgb, var(--game-accent) 9%, transparent), transparent 50%),
        linear-gradient(to right, rgba(0,0,0,0.85), rgba(0,0,0,0.15) 30%, rgba(0,0,0,0.15) 70%, rgba(0,0,0,0.85)),
        linear-gradient(to bottom, rgba(0,0,0,0.05) 55%, rgba(0,0,0,0.95) 100%);
}

.container {
    position: relative;
    max-width: 1200px;
    margin: -165px auto 60px;
    padding: 24px;
    z-index: 2;
}

.hero-content {
    display: grid;
    grid-template-columns: 280px minmax(0, 1fr);
    gap: 34px;
    align-items: start;
    padding: 22px;
    border: 1px solid color-mix(in srgb, var(--game-accent) 45%, transparent);
    background:
        linear-gradient(120deg, rgba(255,255,255,0.05), transparent 28%),
        var(--game-card);
    box-shadow:
        0 18px 40px rgba(0, 0, 0, 0.45),
        inset 0 0 0 1px color-mix(in srgb, var(--game-accent) 15%, transparent);
    backdrop-filter: blur(8px);
}

.cover {
    width: 100%;
    max-width: 280px;
    aspect-ratio: 3 / 4;
    height: auto;
    object-fit: contain;
    object-position: center;
    background: #030303;
    display: block;
    border: 1px solid color-mix(in srgb, var(--game-accent) 75%, transparent);
    box-shadow:
        0 8px 26px rgba(0, 0, 0, 0.55),
        0 0 0 1px rgba(255,255,255,0.06) inset;
}

.info {
    min-width: 0;
}

.info h1 {
    margin: 0 0 12px;
    color: var(--game-text);
    font-size: clamp(1.7rem, 2.2vw, 2.3rem);
    letter-spacing: 0.02em;
    text-shadow: 0 0 12px color-mix(in srgb, var(--game-accent) 35%, transparent);
}

.badge {
    display: inline-block;
    margin-bottom: 14px;
    border: 1px solid color-mix(in srgb, var(--game-accent) 75%, transparent);
    padding: 7px 11px;
    background: color-mix(in srgb, var(--game-accent) 12%, transparent);
    font-size: 0.85rem;
    letter-spacing: 0.04em;
}

.progress-panel {
    margin: 12px 0 14px;
    border: 1px solid color-mix(in srgb, var(--game-accent) 48%, transparent);
    background: color-mix(in srgb, var(--game-accent) 7%, transparent);
    padding: 12px;
}

.progress-topline {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.status-pill {
    border: 1px solid color-mix(in srgb, var(--game-accent) 65%, transparent);
    padding: 4px 8px;
    font-size: 0.8rem;
}

.progress-label {
    font-size: 0.9rem;
    color: color-mix(in srgb, var(--game-text) 90%, white);
}

.progress-track {
    height: 6px;
    background: color-mix(in srgb, var(--game-accent) 16%, transparent);
    margin-bottom: 10px;
    overflow: hidden;
}

.progress-track span {
    display: block;
    height: 100%;
    background: linear-gradient(90deg, color-mix(in srgb, var(--game-accent) 65%, black), var(--game-accent));
}

.progress-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 8px;
}

.progress-actions form {
    margin: 0;
}

.progress-form {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.progress-form input[type="range"] {
    width: 220px;
    accent-color: var(--game-accent);
}

.range-value {
    min-width: 42px;
    font-size: 0.82rem;
}

.description {
    color: color-mix(in srgb, var(--game-text) 82%, #d7fff0);
    margin-bottom: 20px;
    line-height: 1.7;
    max-height: 210px;
    overflow-y: auto;
    padding-right: 6px;
}

.description::-webkit-scrollbar {
    width: 8px;
}

.description::-webkit-scrollbar-thumb {
    background: color-mix(in srgb, var(--game-accent) 65%, transparent);
    border-radius: 6px;
}

.meta {
    margin-bottom: 16px;
}

.meta strong {
    display: inline-block;
    min-width: 84px;
    color: color-mix(in srgb, var(--game-text) 90%, white);
}

.tag {
    display: inline-flex;
    align-items: center;
    border: 1px solid color-mix(in srgb, var(--game-accent) 70%, transparent);
    padding: 5px 10px;
    margin: 5px 7px 0 0;
    background: color-mix(in srgb, var(--game-accent) 8%, transparent);
    font-size: 0.82rem;
}

.sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px;
    margin-top: 30px;
}

.box,
.screenshots-box,
.videos-box {
    border: 1px solid color-mix(in srgb, var(--game-accent) 55%, transparent);
    padding: 20px;
    min-height: 260px;
    background: var(--game-card);
    box-shadow:
        0 10px 26px rgba(0, 0, 0, 0.35),
        inset 0 0 0 1px color-mix(in srgb, var(--game-accent) 10%, transparent);
}

.box h3,
.screenshots-box h3,
.videos-box h3 {
    margin-top: 0;
    margin-bottom: 14px;
    letter-spacing: 0.12em;
    color: var(--game-text);
}

.note {
    margin-top: 12px;
    border: 1px solid color-mix(in srgb, var(--game-accent) 40%, transparent);
    padding: 12px;
    background: color-mix(in srgb, var(--game-accent) 4%, transparent);
}

.note p {
    margin-top: 0;
    margin-bottom: 8px;
    line-height: 1.55;
}

.note-input {
    width: 100%;
    min-height: 94px;
    background: rgba(0,0,0,0.75);
    color: var(--game-text);
    border: 1px solid color-mix(in srgb, var(--game-accent) 65%, transparent);
    padding: 10px;
    margin-top: 10px;
    resize: vertical;
}

.note-input:focus {
    outline: none;
    box-shadow: 0 0 0 2px color-mix(in srgb, var(--game-accent) 35%, transparent);
}

.add-btn,
.edit-btn,
.cancel-btn,
.upload-actions button {
    margin-top: 8px;
    margin-right: 8px;
    border: 1px solid color-mix(in srgb, var(--game-accent) 80%, transparent);
    background: color-mix(in srgb, var(--game-accent) 8%, transparent);
    color: var(--game-text);
    padding: 7px 11px;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
}

.add-btn:hover,
.edit-btn:hover,
.cancel-btn:hover,
.upload-actions button:hover {
    transform: translateY(-1px);
    background: color-mix(in srgb, var(--game-accent) 22%, transparent);
    box-shadow: 0 0 12px color-mix(in srgb, var(--game-accent) 35%, transparent);
}

.delete-btn {
    margin-top: 8px;
    border: 1px solid #ff6969;
    background: rgba(255, 105, 105, 0.08);
    color: #ff8f8f;
    padding: 7px 11px;
    cursor: pointer;
}

.screenshots-box form {
    display: flex;
    flex-direction: column;
}

#drop-zone {
    border: 1px dashed color-mix(in srgb, var(--game-accent) 75%, transparent);
    padding: 22px;
    font-size: 0.8rem;
    text-align: center;
    cursor: pointer;
    margin-bottom: 15px;
    transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    background: color-mix(in srgb, var(--game-accent) 5%, transparent);
}

#drop-zone.dragover {
    background: color-mix(in srgb, var(--game-accent) 20%, transparent);
    color: var(--game-text);
    border-color: color-mix(in srgb, var(--game-accent) 100%, white 10%);
}

.file-pick-label {
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px dashed color-mix(in srgb, var(--game-accent) 65%, transparent);
    padding: 14px 16px;
    margin-bottom: 12px;
    cursor: pointer;
    background: color-mix(in srgb, var(--game-accent) 5%, transparent);
    transition: background 0.2s;
}

.file-pick-label:hover {
    background: color-mix(in srgb, var(--game-accent) 12%, transparent);
}

.file-pick-label input[type="file"] {
    font-size: 0.82rem;
    color: var(--game-text);
    background: transparent;
    border: none;
    outline: none;
    max-width: 100%;
    cursor: pointer;
}

.file-pick-label span {
    display: none;
}

.upload-actions {
    display: flex;
    justify-content: flex-end;
}

.upload-success,
.upload-error {
    margin: 0 0 10px;
    padding: 8px 10px;
    font-size: 0.82rem;
    border: 1px solid;
}

.upload-success {
    color: #8cfecb;
    border-color: #28d58d;
    background: rgba(40, 213, 141, 0.08);
}

.upload-error {
    color: #ffadad;
    border-color: #ff6f6f;
    background: rgba(255, 111, 111, 0.1);
}

.screenshots-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 12px;
    margin-top: 15px;
}

.videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
    margin-top: 15px;
}

.shot-card {
    position: relative;
    border: 1px solid color-mix(in srgb, var(--game-accent) 55%, transparent);
    padding: 6px;
    background: rgba(0,0,0,0.7);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.video-card {
    position: relative;
    border: 1px solid color-mix(in srgb, var(--game-accent) 55%, transparent);
    padding: 6px;
    background: rgba(0,0,0,0.7);
}

.shot-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(0,0,0,0.35);
}

.shot-img {
    display: block;
    width: 100%;
    aspect-ratio: 16 / 9;
    height: auto;
    object-fit: contain;
    object-position: center;
    background: #030303;
    border: 1px solid color-mix(in srgb, var(--game-accent) 40%, transparent);
}

.video-player {
    display: block;
    width: 100%;
    height: 140px;
    background: #000;
    border: 1px solid color-mix(in srgb, var(--game-accent) 40%, transparent);
}

.delete-shot {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.78);
    border: 1px solid #ff6969;
    color: #ff8f8f;
    padding: 2px 7px;
    cursor: pointer;
}

@media (max-width: 960px) {
    .hero-bg {
        height: 350px;
    }

    .container {
        margin-top: -140px;
        padding: 14px;
    }

    .hero-content {
        grid-template-columns: 1fr;
        gap: 18px;
    }

    .cover {
        max-width: 220px;
        margin: 0 auto;
    }

    .info {
        text-align: center;
    }

    .meta strong {
        display: block;
        margin-bottom: 4px;
    }

    .sections {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function editNote(id) {
    document.getElementById('note-view-' + id).style.display = 'none';
    document.getElementById('note-edit-' + id).style.display = 'block';
}

function cancelEdit(id) {
    document.getElementById('note-edit-' + id).style.display = 'none';
    document.getElementById('note-view-' + id).style.display = 'block';
}


</script>
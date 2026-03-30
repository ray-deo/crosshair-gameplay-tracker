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
    <div class="hero-content">
        <img
            src="{{ $data['background_image'] ?? $game->cover_url ?? 'https://placehold.co/600x800?text=No+Image' }}"
            class="cover"
            alt="{{ $game->title }}"
        >

        <div class="info">
            <h1>{{ $game->title }}</h1>

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

                        <form method="POST" action="/notes/{{ $note->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </div>

                    <div id="note-edit-{{ $note->id }}" style="display:none;">
                        <form method="POST" action="/notes/{{ $note->id }}">
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

            <form id="screenshot-form"
                  method="POST"
                  action="{{ route('screenshots.upload', $game->id) }}"
                  enctype="multipart/form-data">
                @csrf

                <div id="drop-zone">
                    Drag & Drop Screenshots Here
                    <br>or click to upload
                    <input type="file" name="screenshots[]" id="file-input" multiple hidden>
                </div>

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
    </div>
</div>

@endsection

<style>
.hero-wrapper {
    width: 100vw;
    margin-left: calc(-50vw + 50%);
}

.hero-bg {
    height: 400px;
    background-size: cover;
    background-position: center;
    position: relative;
    filter: blur(6px) brightness(0.3);
    transform: scale(1.05);
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(to right, black 0%, transparent 25%, transparent 75%, black 100%),
        linear-gradient(to bottom, transparent 60%, black 100%);
}

.container {
    position: relative;
    max-width: 1100px;
    margin: -120px auto 40px auto;
    padding: 20px;
    z-index: 2;
}

.hero-content {
    display: flex;
    gap: 40px;
    margin-top: 20px;
    align-items: flex-start;
}

.cover {
    width: 260px;
    height: auto;
    object-fit: cover;
    display: block;
    border: 2px solid #00ff9f;
}

.info {
    flex: 1;
}

.info h1 {
    color: #00ff9c;
    margin-bottom: 10px;
}

.badge {
    display: inline-block;
    margin-bottom: 10px;
    border: 1px solid #00ff9f;
    padding: 5px 10px;
}

.description {
    color: #9fffdc;
    margin-bottom: 20px;
    line-height: 1.6;
}

.meta {
    margin-bottom: 20px;
}

.tag {
    display: inline-block;
    border: 1px solid #00ff9f;
    padding: 4px 8px;
    margin: 5px 5px 0 0;
}

.sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-top: 50px;
}

.box,
.screenshots-box {
    border: 2px solid #00ff9f;
    padding: 20px;
    min-height: 220px;
}

.note {
    margin-top: 10px;
    border: 1px solid #00ff9f;
    padding: 10px;
}

.note-input {
    width: 100%;
    min-height: 80px;
    background: black;
    color: #00ff9c;
    border: 1px solid #00ff9f;
    padding: 10px;
    margin-top: 10px;
}

.add-btn,
.edit-btn,
.cancel-btn {
    margin-top: 8px;
    margin-right: 8px;
    border: 1px solid #00ff9f;
    background: black;
    color: #00ff9c;
    padding: 6px 10px;
    cursor: pointer;
}

.delete-btn {
    margin-top: 8px;
    border: 1px solid red;
    background: black;
    color: red;
    padding: 6px 10px;
    cursor: pointer;
}

.screenshots-box form {
    display: flex;
    flex-direction: column;
}

#drop-zone {
    border: 2px dashed #00ff9c;
    padding: 20px;
    font-size: 12px;
    text-align: center;
    cursor: pointer;
    margin-bottom: 15px;
    transition: 0.2s;
}

#drop-zone.dragover {
    background: #00ff9c;
    color: #000;
}

.upload-actions {
    display: flex;
    justify-content: flex-end;
}

.upload-actions button {
    background: none;
    border: 1px solid #00ff9c;
    color: #00ff9c;
    padding: 6px 12px;
    cursor: pointer;
}

.upload-actions button:hover {
    background: #00ff9c;
    color: #000;
}

.screenshots-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 12px;
    margin-top: 15px;
}

.shot-card {
    position: relative;
    border: 1px solid #00ff9c;
    padding: 6px;
    background: #000;
}

.shot-img {
    display: block;
    width: 100%;
    height: 120px;
    object-fit: cover;
    border: 1px solid #00ff9c;
}

.delete-shot {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #000;
    border: 1px solid red;
    color: red;
    padding: 2px 6px;
    cursor: pointer;
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

const form = document.getElementById('screenshot-form');
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');

if (dropZone && fileInput && form) {
    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
    });
}
</script>
@extends('layouts.app')

@section('content')

<div class="library-page">

    <div class="dashboard-user">
        <h2>{{ Auth::user()->name }}</h2>
        <p class="subtext">Library Initialized</p>
    </div>

    <div class="library-sorter">
        <form method="GET" action="{{ route('library') }}" class="sort-form">
            <label for="sort">Sort by</label>
            <select id="sort" name="sort" onchange="this.form.submit()">
                @if(($hasFavoritesColumn ?? true))
                    <option value="favorites" {{ ($sort ?? 'favorites') === 'favorites' ? 'selected' : '' }}>Favorites First</option>
                @endif
                <option value="recent" {{ ($sort ?? 'favorites') === 'recent' ? 'selected' : '' }}>Recently Updated</option>
                <option value="title_asc" {{ ($sort ?? 'favorites') === 'title_asc' ? 'selected' : '' }}>Title A-Z</option>
                <option value="title_desc" {{ ($sort ?? 'favorites') === 'title_desc' ? 'selected' : '' }}>Title Z-A</option>
                <option value="status" {{ ($sort ?? 'favorites') === 'status' ? 'selected' : '' }}>Status</option>
            </select>
        </form>
    </div>

    <!-- STEAM IMPORT SECTION -->
    <div class="steam-import-section">
        <h3>Import from Steam</h3>
        <form method="POST" action="{{ route('library.import-steam') }}" class="steam-form">
            @csrf
            <div class="form-group">
                <input 
                    type="text" 
                    name="steam_profile"
                    placeholder="Enter Steam ID or Profile URL"
                    class="steam-input"
                    required
                >
                <button type="submit" class="steam-btn">Import Games</button>
            </div>
        </form>

        @if ($errors->has('steam_profile'))
            <p class="error-msg">{{ $errors->first('steam_profile') }}</p>
        @endif
        
        @if (session()->has('steam_success'))
            <p class="success-msg">✓ {{ session('steam_success') }}</p>
        @endif
        
        @if (session()->has('steam_error'))
            <p class="error-msg">✗ {{ session('steam_error') }}</p>
        @endif
    </div>

    <div class="game-grid">

    @forelse($games as $game)

    <div class="game-card">

        <!-- CLICKABLE GAME AREA -->
        <a href="{{ route('game.show', $game->id) }}" class="game-link">

            <img 
                src="{{ $game->steam_appid ? 'https://cdn.cloudflare.steamstatic.com/steam/apps/' . $game->steam_appid . '/library_600x900_2x.jpg' : ($game->cover_url ?? 'https://placehold.co/300x400?text=No+Image') }}"
                onerror="this.onerror=null; this.src='{{ $game->cover_url ?? 'https://placehold.co/300x400?text=No+Image' }}';"
                class="game-cover"
            >

            <h3 class="game-title">{{ $game->title }}</h3>

        </a>

        @if(($hasFavoritesColumn ?? true))
            <form method="POST" action="{{ route('library.favorite', $game->id) }}" class="favorite-form">
                @csrf
                <button
                    type="submit"
                    class="favorite-btn {{ $game->pivot->is_favorite ? 'is-favorite' : '' }}"
                >
                    {{ $game->pivot->is_favorite ? 'FAVORITED' : 'FAVORITE' }}
                </button>
            </form>
        @endif

        <!-- REMOVE BUTTON -->
        <form method="POST" action="{{ route('library.remove', $game->id) }}" class="remove-form">
            @csrf
            @method('DELETE')

            <button 
                type="submit" 
                class="remove-btn"
                onclick="return confirm('Remove this game from your library?')"
            >
                REMOVE
            </button>
        </form>

    </div>

    @empty

    <p class="empty-state">
        > No games in library. Search and add your first game.
    </p>

    @endforelse

    </div>

</div>

<style>

/* PAGE FIX */
.library-page{
    margin-top:100px;
    padding:0 40px;
}

/* USER INFO */
.dashboard-user{
    margin-bottom:25px;
}

.library-sorter{
    margin-bottom:20px;
    display:flex;
    justify-content:flex-end;
}

.sort-form{
    display:flex;
    align-items:center;
    gap:10px;
    color:#00ff9c;
    font-family:monospace;
    font-size:13px;
}

.sort-form select{
    background:#050505;
    border:1px solid #00ff9c;
    color:#00ff9c;
    padding:7px 10px;
    font-family:monospace;
    font-size:13px;
}

.sort-form select:focus{
    outline:none;
    box-shadow:0 0 8px #00ff9c55;
}

.subtext{
    color:#00ff9c88;
    font-size:14px;
}

/* GRID */
.game-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(200px,1fr));
    gap:25px;
}

/* CARD */
.game-card{
    border:1px solid #00ff9c;
    padding:12px;
    background:#050505;
    transition:0.2s;
    display:flex;
    flex-direction:column;
}

.game-card:hover{
    transform:scale(1.03);
    box-shadow:0 0 20px #00ff9c55;
}

/* IMAGE */
.game-cover{
    width:100%;
    aspect-ratio:3/4;
    height:auto;
    object-fit:contain;
    object-position:center;
    background:#030303;
    border:1px solid #00ff9c55;
}

/* TITLE */
.game-title{
    font-size:14px;
    margin-top:8px;
    min-height:38px;
    line-height:1.35;
}

/* LINK FIX */
.game-link{
    text-decoration:none;
    color:inherit;
    display:block;
}

/* REMOVE BUTTON */
.remove-form{
    margin-top:8px;
}

.favorite-form{
    margin-top:10px;
}

.favorite-btn{
    width:100%;
    background:none;
    border:1px solid #ffd166;
    color:#ffd166;
    padding:6px;
    cursor:pointer;
    font-family:monospace;
    font-size:12px;
}

.favorite-btn:hover{
    background:#ffd166;
    color:#000;
}

.favorite-btn.is-favorite{
    background:#ffd166;
    color:#000;
    box-shadow:0 0 12px #ffd16666;
}

.remove-btn{
    width:100%;
    background:none;
    border:1px solid red;
    color:red;
    padding:6px;
    cursor:pointer;
    font-family:monospace;
    font-size:12px;
}

.remove-btn:hover{
    background:red;
    color:#000;
}

/* EMPTY */
.empty-state{
    margin-top:40px;
    color:#00ff9c88;
}

/* STEAM IMPORT SECTION */
.steam-import-section{
    margin-bottom:40px;
    padding:20px;
    border:1px solid #00ff9c55;
    background:linear-gradient(135deg, #00ff9c11 0%, #0099ff11 100%);
}

.steam-import-section h3{
    margin-bottom:15px;
    font-size:16px;
    color:#00ff9c;
}

.steam-form{
    display:flex;
    gap:10px;
}

.form-group{
    display:flex;
    gap:10px;
    width:100%;
}

.steam-input{
    flex:1;
    padding:10px 15px;
    background:#050505;
    border:1px solid #00ff9c;
    color:#fff;
    font-family:monospace;
    font-size:14px;
}

.steam-input:focus{
    outline:none;
    border-color:#0099ff;
    box-shadow:0 0 10px #0099ff55;
}

.steam-btn{
    padding:10px 25px;
    background:linear-gradient(135deg, #00ff9c, #0099ff);
    border:none;
    color:#000;
    font-weight:bold;
    cursor:pointer;
    font-family:monospace;
    transition:0.3s;
}

.steam-btn:hover{
    transform:scale(1.05);
    box-shadow:0 0 15px #00ff9c88;
}

.success-msg, .error-msg{
    margin-top:10px;
    padding:10px;
    border-left:4px solid;
    font-family:monospace;
    font-size:13px;
}

.success-msg{
    border-color:#00ff9c;
    color:#00ff9c;
    background:#00ff9c11;
}

.error-msg{
    border-color:#ff0066;
    color:#ff0066;
    background:#ff006611;
}

</style>

@endsection
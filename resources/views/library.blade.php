@extends('layouts.app')

@section('content')

<div class="library-page">

    <div class="dashboard-user">
        <h2>{{ Auth::user()->name }}</h2>
        <p class="subtext">Library Initialized</p>
    </div>

    <div class="game-grid">

    @forelse($games as $game)

    <div class="game-card">

        <!-- CLICKABLE GAME AREA -->
        <a href="{{ route('game.show', $game->id) }}" class="game-link">

            <img 
                src="{{ $game->cover_url ?? 'https://placehold.co/300x400?text=No+Image' }}" 
                class="game-cover"
            >

            <h3 class="game-title">{{ $game->title }}</h3>

        </a>

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
}

.game-card:hover{
    transform:scale(1.03);
    box-shadow:0 0 20px #00ff9c55;
}

/* IMAGE */
.game-cover{
    width:100%;
    height:300px;
    object-fit:cover;
    border:1px solid #00ff9c55;
}

/* TITLE */
.game-title{
    font-size:14px;
    margin-top:8px;
}

/* LINK FIX */
.game-link{
    text-decoration:none;
    color:inherit;
    display:block;
}

/* REMOVE BUTTON */
.remove-form{
    margin-top:10px;
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

</style>

@endsection
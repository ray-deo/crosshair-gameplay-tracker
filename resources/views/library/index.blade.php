@extends('layouts.app')

@section('content')

<div class="library-page">

    <div class="library-meta">
        <h2>{{ auth()->user()->name }}</h2>
        <p>Library Initialized</p>
    </div>

    {{-- your game cards here --}}

</div>

<div class="container">

    <h1 style="margin-bottom: 30px;">LIBRARY</h1>

    @if($games->isEmpty())
        <p>No games in your library yet.</p>
    @else
        <div class="library-grid">
            @foreach($games as $game)
                <a href="{{ route('game.show', $game->id) }}" class="game-card">

                    <img src="{{ $game->cover_url }}" alt="cover">

                    <div class="game-title">
                        {{ $game->title }}
                    </div>

                </a>
            @endforeach
        </div>
    @endif

</div>



<style>

.library-page{
    margin-top: 100px; /* 🔥 this is the real fix */
}

.library-meta{
    margin-bottom: 20px;
}
</style>

@endsection
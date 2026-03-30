@extends('layouts.app')

@section('content')

<h2 class="terminal-title">SEARCH GAMES</h2>

<form method="GET" action="/search" class="search-bar">
    <input type="text" name="query" placeholder="> search game..." required>
    <button type="submit">SCAN</button>
</form>



<div class="game-grid">
    @foreach($games as $game)
        <div class="game-card">
    @if(!empty($game['background_image']))
    <img src="{{ $game['background_image'] }}" class="game-cover">
@endif

    <h3>{{ $game['name'] }}</h3>

    <form method="POST" action="{{ route('library.add') }}">
        @csrf
        <input type="hidden" name="title" value="{{ $game['name'] }}">
        <input type="hidden" name="cover" value="{{ $game['background_image'] }}">
        <input type="hidden" name="rawg_id" value="{{ $game['id'] }}">

        <button type="submit" class="retro-btn">
            ADD TO LIBRARY
        </button>
    </form>
</div>
    @endforeach
</div>

@endsection
@extends('layouts.app')

@section('content')

@if(!request('q'))

<!-- GOOGLE STYLE CENTER -->
<div class="search-wrapper">

    <h1 class="search-title">Search Games</h1>

    <form method="GET" action="/search" class="search-form">
        <input 
            type="text" 
            name="q" 
            placeholder="Search for games..." 
            class="search-input"
        >
    </form>

</div>

@else

<!-- RESULTS VIEW -->
<div class="results-container">

    <form method="GET" action="/search" class="search-form-top">
        <input 
            type="text" 
            name="q" 
            value="{{ request('q') }}" 
            class="search-input-small"
        >
    </form>

    <div class="grid">
        @foreach($games as $game)

        <div class="card">

            <img 
                src="{{ $game['background_image'] ?? 'https://via.placeholder.com/300x400' }}"
                class="card-img"
            >

            <h3>{{ $game['name'] }}</h3>

            <form method="POST" action="{{ route('library.add') }}">
                @csrf
                <input type="hidden" name="title" value="{{ $game['name'] }}">
                <input type="hidden" name="cover" value="{{ $game['background_image'] ?? '' }}">
                <input type="hidden" name="rawg_id" value="{{ $game['id'] }}">

                <button class="add-btn">ADD</button>
            </form>

        </div>

        @endforeach
    </div>

</div>

@endif

@endsection


<style>

/* CENTER SEARCH */
.search-wrapper {
    height: 70vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

/* TITLE */
.search-title {
    color: #00ff9c;
    margin-bottom: 30px;
}

/* BIG INPUT */
.search-input {
    width: 600px;
    padding: 16px 20px;
    font-size: 18px;

    border: 2px solid #00ff9f;
    background: black;
    color: #00ff9c;
    border-radius: 30px;
    outline: none;
}

.search-input:focus {
    box-shadow: 0 0 10px #00ff9f;
}

/* TOP SEARCH (SMALL) */
.search-form-top {
    margin-bottom: 30px;
}

.search-input-small {
    width: 400px;
    padding: 10px;
    border: 2px solid #00ff9f;
    background: black;
    color: #00ff9c;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

/* CARD */
.card {
    border: 1px solid #00ff9f;
    padding: 10px;
}

.card-img {
    width: 100%;
    height: 300px;
    object-fit: cover;
}

/* BUTTON */
.add-btn {
    margin-top: 10px;
    padding: 8px;
    border: 1px solid #00ff9f;
    background: black;
    color: #00ff9c;
    cursor: pointer;
}

.add-btn:hover {
    background: #00ff9f;
    color: black;
}

</style>
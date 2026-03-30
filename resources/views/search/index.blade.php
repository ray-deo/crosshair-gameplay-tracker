@extends('layouts.app')

@section('content')

@if(!request('q'))

<!-- GOOGLE STYLE CENTER -->
<div class="search-wrapper">

    <h1 class="search-title">Search Games</h1>

    <form method="GET" action="/search" class="search-form">
        <div class="search-shell">
            <span class="search-icon" aria-hidden="true">[+]</span>
            <input 
                type="text" 
                name="q" 
                placeholder="Search for games..." 
                class="search-input"
                autocomplete="off"
            >
            <button type="submit" class="search-btn">Scan</button>
        </div>
    </form>

</div>

@else

<!-- RESULTS VIEW -->
<div class="results-container">

    <form method="GET" action="/search" class="search-form-top">
        <div class="search-shell small">
            <span class="search-icon" aria-hidden="true">[+]</span>
            <input 
                type="text" 
                name="q" 
                value="{{ request('q') }}" 
                class="search-input-small"
                autocomplete="off"
            >
            <button type="submit" class="search-btn">Search</button>
        </div>
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

:root {
    --search-panel: color-mix(in srgb, var(--bg) 82%, black 18%);
    --search-border: color-mix(in srgb, var(--accent) 65%, transparent);
    --search-border-soft: color-mix(in srgb, var(--accent) 35%, transparent);
    --search-soft: color-mix(in srgb, var(--text) 62%, transparent);
}

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
    color: var(--text);
    margin-bottom: 30px;
    letter-spacing: 0.08em;
    text-shadow: 0 0 14px color-mix(in srgb, var(--accent) 40%, transparent);
}

.search-shell {
    width: min(720px, 92vw);
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: 10px;
    border: 1px solid var(--search-border);
    background: linear-gradient(120deg, color-mix(in srgb, var(--accent) 10%, var(--bg)), var(--search-panel));
    border-radius: 999px;
    padding: 8px 10px 8px 14px;
    box-shadow: 0 0 18px color-mix(in srgb, var(--accent) 20%, transparent);
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}

.search-shell:focus-within {
    box-shadow: 0 0 22px color-mix(in srgb, var(--accent) 38%, transparent);
    transform: translateY(-1px);
}

.search-icon {
    color: var(--text);
    opacity: 0.85;
    letter-spacing: 0.1em;
    font-size: 13px;
}

/* BIG INPUT */
.search-input,
.search-input-small {
    width: 100%;
    padding: 10px 4px;
    font-size: 17px;
    border: none;
    background: transparent;
    color: var(--text);
    outline: none;
}

.search-input::placeholder {
    color: var(--search-soft);
}

.search-btn {
    border: 1px solid var(--search-border);
    border-radius: 999px;
    background: color-mix(in srgb, var(--accent) 12%, var(--bg));
    color: var(--text);
    padding: 9px 16px;
    font-size: 12px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background 0.2s ease, color 0.2s ease;
}

.search-btn:hover {
    background: var(--accent);
    color: #00150d;
}

/* TOP SEARCH (SMALL) */
.search-form-top {
    margin-bottom: 30px;
}

.search-shell.small {
    width: min(560px, 92vw);
    border-radius: 16px;
}

.search-shell.small .search-input-small {
    font-size: 15px;
    padding: 8px 2px;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

/* CARD */
.card {
    border: 1px solid var(--search-border);
    padding: 10px;
    background: color-mix(in srgb, var(--accent) 4%, transparent);
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
    border: 1px solid var(--search-border);
    background: var(--search-panel);
    color: var(--text);
    cursor: pointer;
}

.add-btn:hover {
    background: var(--accent);
    color: black;
}

@media (max-width: 780px) {
    .search-title {
        font-size: 28px;
        margin-bottom: 20px;
    }

    .search-shell,
    .search-shell.small {
        grid-template-columns: 1fr;
        border-radius: 16px;
        padding: 12px;
        gap: 8px;
    }

    .search-icon {
        display: none;
    }

    .search-btn {
        width: 100%;
    }
}

</style>
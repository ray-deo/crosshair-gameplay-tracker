@extends('layouts.app')

@section('content')

<h1 class="page-title">SEARCH GAMES</h1>
{{ $game['background_image'] }}

<form method="GET" action="/search" class="search-bar">

<input
type="text"
name="q"
placeholder="search games..."
value="{{ $query ?? '' }}"
>

<button type="submit">
SCAN
</button>

</form>


<div class="game-grid">

@forelse($games as $game)

<div class="game-card">

<div class="cover-wrapper">

<img
src="{{ $game['background_image'] ?? 'https://placehold.co/300x400?text=No+Image' }}"
class="game-cover"
alt="{{ $game['name'] }}"
>

<div class="cover-overlay">

<form method="POST" action="{{ route('library.add') }}">
@csrf

<input type="hidden" name="rawg_id" value="{{ $game['id'] }}">
<input type="hidden" name="name" value="{{ $game['name'] }}">
<input type="hidden" name="background_image" value="{{ $game['background_image'] ?? '' }}">

<button class="add-btn">
ADD TO LIBRARY
</button>

</form>

</div>

</div>

<h3 class="game-title">
{{ $game['name'] }}
</h3>

</div>

@empty

<p class="empty-search">
>No results found. Try another search.
</p>

@endforelse

</div>


<style>

/* SEARCH BAR */

.search-bar{
display:flex;
gap:10px;
margin-bottom:30px;
}

.search-bar input{
background:#000;
border:1px solid #00ff9c;
color:#00ff9c;
padding:8px;
font-family:monospace;
}

.search-bar button{
background:#00ff9c;
border:none;
padding:8px 14px;
font-weight:bold;
cursor:pointer;
}


/* GRID */

.game-grid{
display:grid;
grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
gap:30px;
margin-top:20px;
}


/* CARD */

.game-card{
text-align:center;
}


/* COVER */

.cover-wrapper{
position:relative;
}

.game-cover{
width:100%;
height:300px;
object-fit:cover;
border:1px solid #00ff9c55;
background:#000;
}


/* HOVER OVERLAY */

.cover-overlay{
position:absolute;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.75);
display:flex;
align-items:center;
justify-content:center;
opacity:0;
transition:0.2s;
}

.cover-wrapper:hover .cover-overlay{
opacity:1;
}


/* BUTTON */

.add-btn{
background:#00ff9c;
border:none;
padding:10px 16px;
font-weight:bold;
cursor:pointer;
font-family:monospace;
}


/* TITLE */

.game-title{
margin-top:10px;
font-size:14px;
color:#00ff9c;
}


/* EMPTY STATE */

.empty-search{
margin-top:40px;
color:#00ff9c88;
}

</style>

@endsection
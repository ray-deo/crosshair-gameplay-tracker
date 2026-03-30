@extends('layouts.app')

@section('content')

<div class="landing">

    {{-- HERO --}}
    <section class="hero">
        <div class="hero-box">
            <h1>Welcome to Crosshair</h1>
            <p>Add games, track status, and grind progress.</p>

            @auth
                <a href="/library" class="retro-btn">ENTER LIBRARY</a>
            @else
                <a href="/login" class="retro-btn">LOGIN</a>
            @endauth
        </div>
    </section>


    {{-- SECTION 1 --}}
    <section class="split">
        <div class="split-inner">

            <div class="text">
                <h2>Track your game collection</h2>
                <p>
                    Crosshair lets you build your personal gaming archive. Add titles directly
                    from RAWG, organize your backlog, and never lose track of what you want to play next.
                    Keep everything structured in one place with a clean retro interface.
                </p>
            </div>

            <div class="image-box">
                <div class="placeholder">LIBRARY PREVIEW</div>
            </div>

        </div>
    </section>


    {{-- SECTION 2 --}}
    <section class="split reverse">
        <div class="split-inner">

            <div class="text">
                <h2>Monitor your progress</h2>
                <p>
                    Track how far you've progressed in every game. Update completion percentage,
                    mark sessions, and move games from backlog to completed seamlessly.
                    Stay consistent and finish what you start.
                </p>
            </div>

            <div class="image-box">
                <div class="placeholder">PROGRESS TRACKING</div>
            </div>

        </div>
    </section>


    {{-- SECTION 3 --}}
    <section class="split">
        <div class="split-inner">

            <div class="text">
                <h2>Write notes & reviews</h2>
                <p>
                    Capture your thoughts, strategies, and experiences. Add personal notes,
                    write detailed reviews, and upload screenshots to document your journey.
                    Turn your gameplay into a record you can revisit anytime.
                </p>
            </div>

            <div class="image-box">
                <div class="placeholder">NOTES & REVIEWS</div>
            </div>

        </div>
    </section>

</div>
<style>

/* RESET */
body{
    margin:0;
    padding:0;
}

/* GLOBAL CONTAINER */
.container{
    width:100%;
    max-width:none;
    padding:0;
}

/* BASE */
.landing{
    margin-top:80px;
}

/* HERO */
.hero{
    padding:0 80px;   /* 🔥 MATCH SPLIT */
    margin-bottom:100px;
}

.hero-box{
    max-width:900px;
    margin:0 auto;
    border:1px solid #00ff9c;
    padding:60px;
    text-align:center;
}

.hero-box h1{
    font-size:36px;
    color:#00ff9c;
    font-family:monospace;
}

.hero-box p{
    font-size:15px;
    color:#00ff9c88;
    margin-top:10px;
}

/* SPLIT LAYOUT */
.split{
    display:flex;
    align-items:center;
    justify-content:center;   /* 🔥 CENTER THE WHOLE BLOCK */

    width:100%;
    margin:120px 0;
}

.split-inner{
    display:flex;
    align-items:center;
    justify-content:space-between;

    width:100%;
    max-width:1400px;   /* 🔥 increase width */
    padding:0 20px;     /* 🔥 reduce side padding */
    gap:60px;

    margin:0 auto;      /* 🔥 keep centered */
}
.split.reverse .split-inner{
    flex-direction:row-reverse;
}

/* TEXT */
.text{
    flex:1;
}

.text h2{
    font-size:30px;
    color:#00ff9c;
}

.text p{
    font-size:15px;
    line-height:1.9;
    color:#00ff9c88;
    margin-top:15px;
    max-width:500px; /* 🔥 controlled readability */
}

/* IMAGE */
.image-box{
    flex:1;
    height:280px;

    border:1px solid #00ff9c;

    display:flex;
    align-items:center;
    justify-content:center;

    box-shadow:0 0 20px #00ff9c22;
}

/* PLACEHOLDER */
.placeholder{
    font-size:12px;
    color:#00ff9c55;
    font-family:monospace;
    letter-spacing:1px;
}

/* BUTTON */
.retro-btn{
    border:1px solid #00ff9c;
    padding:10px 20px;
    color:#00ff9c;
    text-decoration:none;
    font-family:monospace;
    display:inline-block;
    margin-top:20px;
    transition:0.2s;
}

.retro-btn:hover{
    background:#00ff9c;
    color:#000;
}

</style>
@endsection
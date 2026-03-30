<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Crosshair</title>

<style>

/* GLOBAL */
body{
    margin:0;
    background:#000;
    color:#00ff9c;
    font-family:monospace;
}

/* CRT EFFECT */
body::after{
    content:"";
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:repeating-linear-gradient(
        to bottom,
        rgba(255,255,255,0.02),
        rgba(255,255,255,0.02) 2px,
        transparent 2px,
        transparent 4px
    );
    pointer-events:none;
}

/* HEADER */
header {
    position: fixed; /* 🔥 important */
    top: 0;
    left: 0;
    width: 100%;
    z-index: 100;

    display: flex;
    align-items: center;
    gap: 80px;

    padding: 20px 40px;

    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0.9),
        rgba(0,0,0,0)
    );
}

/* LOGO */
.logo {
    font-size: 18px;
    letter-spacing: 2px;
}

.tagline{
    font-size:12px;
    opacity:0.7;
}

/* NAV */
.nav-links{
    display:flex;
    gap:25px;
    align-items:center;
}

.nav-links a{
    color:#00ff9c;
    text-decoration:none;
}

.nav-links a:hover{
    text-shadow:0 0 8px #00ff9c;
}

/* LOGOUT BUTTON */
.nav-logout{
    background:none;
    border:none;
    color:#00ff9c;
    cursor:pointer;
    font-family:monospace;
}

.nav-logout:hover{
    text-shadow:0 0 8px #00ff9c;
}

/* CONTAINER */
.container{
    width:100%;
    padding:120px 40px 40px; /* 🔥 prevents overlap */
}

</style>
</head>

<body>

<header>

    <div>
        <div class="logo">[+] CROSSHAIR</div>
        <div class="tagline">Track • Review • Master Your Games</div>
    </div>

    <nav class="nav-links">

        <a href="/">HOME</a>

        @auth
            <a href="/search">SEARCH</a>
            <a href="/library">LIBRARY</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-logout">LOGOUT</button>
            </form>
        @else
            <a href="/login">LOGIN</a>
        @endauth

    </nav>

</header>

<div class="container">
    @yield('content')
</div>

</body>
</html>
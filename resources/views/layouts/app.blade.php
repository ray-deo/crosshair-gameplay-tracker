<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Crosshair</title>

<style>

:root{
    --bg:#000;
    --text:#00ff9c;
    --accent:#00ff9c;
    --header-top:rgba(0,0,0,0.9);
    --scanline:rgba(255,255,255,0.02);
}

html[data-theme="amber"]{
    --bg:#140b00;
    --text:#ffd27a;
    --accent:#ffb347;
    --header-top:rgba(20,11,0,0.92);
    --scanline:rgba(255,211,122,0.04);
}

html[data-theme="ice"]{
    --bg:#061018;
    --text:#aee9ff;
    --accent:#5bd0ff;
    --header-top:rgba(6,16,24,0.92);
    --scanline:rgba(174,233,255,0.03);
}

/* GLOBAL */
body{
    margin:0;
    background:var(--bg);
    color:var(--text);
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
        var(--scanline),
        var(--scanline) 2px,
        transparent 2px,
        transparent 4px
    );
    pointer-events:none;
}

.theme-toggle{
    position:fixed;
    top:14px;
    right:14px;
    z-index:140;
    border:1px solid var(--accent);
    background:var(--bg);
    color:var(--text);
    padding:6px 10px;
    font-family:monospace;
    font-size:12px;
    letter-spacing:1px;
    cursor:pointer;
}

.theme-toggle:hover{
    box-shadow:0 0 10px color-mix(in srgb, var(--accent) 65%, transparent);
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

    padding: 20px 130px 20px 40px;

    background: linear-gradient(
        to bottom,
        var(--header-top),
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
    color:var(--text);
    text-decoration:none;
}

.nav-links a:hover{
    text-shadow:0 0 8px var(--accent);
}

/* LOGOUT BUTTON */
.nav-logout{
    background:none;
    border:none;
    color:var(--text);
    cursor:pointer;
    font-family:monospace;
}

.nav-logout:hover{
    text-shadow:0 0 8px var(--accent);
}

/* CONTAINER */
.container{
    width:100%;
    padding:120px 40px 40px; /* 🔥 prevents overlap */
}

</style>
</head>

<body>

<button id="theme-toggle" class="theme-toggle" type="button" aria-label="Switch theme">Theme: Neon</button>

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

<script>
(() => {
    const themes = [
        { key: 'neon', label: 'Neon' },
        { key: 'amber', label: 'Amber' },
        { key: 'ice', label: 'Ice' }
    ];

    const btn = document.getElementById('theme-toggle');
    const root = document.documentElement;
    const saved = localStorage.getItem('crosshair-theme') || 'neon';

    const applyTheme = (key) => {
        if (key === 'neon') {
            root.removeAttribute('data-theme');
        } else {
            root.setAttribute('data-theme', key);
        }

        const found = themes.find((t) => t.key === key) || themes[0];
        btn.textContent = `Theme: ${found.label}`;
        localStorage.setItem('crosshair-theme', key);
    };

    applyTheme(saved);

    btn.addEventListener('click', () => {
        const current = root.getAttribute('data-theme') || 'neon';
        const index = themes.findIndex((t) => t.key === current);
        const next = themes[(index + 1) % themes.length].key;
        applyTheme(next);
    });
})();
</script>

</body>
</html>
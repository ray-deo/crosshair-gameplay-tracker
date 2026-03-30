<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Crosshair</title>

<style>

:root{
    --bg:#05070c;
    --text:#90ffd9;
    --accent:#00f5b4;
    --header-top:rgba(3,7,14,0.9);
    --scanline:rgba(173,255,230,0.03);
    --bg-layer-1:rgba(0,245,180,0.2);
    --bg-layer-2:rgba(22,132,255,0.16);
    --bg-layer-3:rgba(0,0,0,0.7);
    --nav-glow:0 0 12px rgba(0,245,180,0.45);
    --flow-speed:1;
    --drift-amount:16;
}

html[data-theme="synth"]{
    --bg:#0d0714;
    --text:#ffd8f7;
    --accent:#ff4fd8;
    --header-top:rgba(13,7,20,0.92);
    --scanline:rgba(255,173,240,0.04);
    --bg-layer-1:rgba(255,79,216,0.22);
    --bg-layer-2:rgba(114,90,255,0.2);
    --bg-layer-3:rgba(8,0,15,0.72);
    --nav-glow:0 0 12px rgba(255,79,216,0.45);
    --flow-speed:1.08;
    --drift-amount:20;
}

html[data-theme="toxic"]{
    --bg:#0b1204;
    --text:#d8ff7d;
    --accent:#b5ff2a;
    --header-top:rgba(11,18,4,0.92);
    --scanline:rgba(216,255,125,0.04);
    --bg-layer-1:rgba(181,255,42,0.2);
    --bg-layer-2:rgba(76,255,176,0.14);
    --bg-layer-3:rgba(2,9,0,0.74);
    --nav-glow:0 0 12px rgba(181,255,42,0.42);
    --flow-speed:0.94;
    --drift-amount:14;
}

html[data-theme="chrome"]{
    --bg:#07121d;
    --text:#c5ecff;
    --accent:#57d3ff;
    --header-top:rgba(7,18,29,0.92);
    --scanline:rgba(197,236,255,0.03);
    --bg-layer-1:rgba(87,211,255,0.22);
    --bg-layer-2:rgba(107,142,255,0.16);
    --bg-layer-3:rgba(1,6,14,0.72);
    --nav-glow:0 0 12px rgba(87,211,255,0.45);
    --flow-speed:1.15;
    --drift-amount:18;
}

/* Legacy saved keys */
html[data-theme="amber"]{
    --bg:#0d0714;
    --text:#ffd8f7;
    --accent:#ff4fd8;
    --header-top:rgba(13,7,20,0.92);
    --scanline:rgba(255,173,240,0.04);
    --bg-layer-1:rgba(255,79,216,0.22);
    --bg-layer-2:rgba(114,90,255,0.2);
    --bg-layer-3:rgba(8,0,15,0.72);
    --nav-glow:0 0 12px rgba(255,79,216,0.45);
}

html[data-theme="ice"]{
    --bg:#07121d;
    --text:#c5ecff;
    --accent:#57d3ff;
    --header-top:rgba(7,18,29,0.92);
    --scanline:rgba(197,236,255,0.03);
    --bg-layer-1:rgba(87,211,255,0.22);
    --bg-layer-2:rgba(107,142,255,0.16);
    --bg-layer-3:rgba(1,6,14,0.72);
    --nav-glow:0 0 12px rgba(87,211,255,0.45);
}

/* GLOBAL */
body{
    margin:0;
    background:var(--bg);
    background-image:
        radial-gradient(circle at 16% 14%, var(--bg-layer-1), transparent 35%),
        radial-gradient(circle at 82% 8%, var(--bg-layer-2), transparent 38%),
        linear-gradient(160deg, var(--bg-layer-3), transparent 45%);
    background-attachment: fixed;
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
    background:color-mix(in srgb, var(--bg) 78%, black 22%);
    color:var(--text);
    padding:6px 10px;
    font-family:monospace;
    font-size:12px;
    letter-spacing:1px;
    cursor:pointer;
    box-shadow: var(--nav-glow);
}

.theme-toggle:hover{
    box-shadow:0 0 18px color-mix(in srgb, var(--accent) 85%, transparent);
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

    transition: background 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    border-bottom: 1px solid transparent;
}

header.scrolled {
    background: color-mix(in srgb, var(--bg) 88%, black 12%);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45);
    border-bottom-color: color-mix(in srgb, var(--accent) 28%, transparent);
    backdrop-filter: blur(6px);
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
    text-shadow: var(--nav-glow);
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
    text-shadow: var(--nav-glow);
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
        { key: 'neon', label: 'Nightline' },
        { key: 'synth', label: 'Synth Riot' },
        { key: 'toxic', label: 'Acid Grid' },
        { key: 'chrome', label: 'Chrome Ghost' }
    ];

    const legacyMap = {
        amber: 'synth',
        ice: 'chrome'
    };

    const btn = document.getElementById('theme-toggle');
    const root = document.documentElement;
    const header = document.querySelector('header');
    const saved = localStorage.getItem('crosshair-theme') || 'neon';
    const normalizedSaved = legacyMap[saved] || saved;

    const applyTheme = (key) => {
        const normalizedKey = legacyMap[key] || key;

        if (normalizedKey === 'neon') {
            root.removeAttribute('data-theme');
        } else {
            root.setAttribute('data-theme', normalizedKey);
        }

        const found = themes.find((t) => t.key === normalizedKey) || themes[0];
        btn.textContent = `Theme: ${found.label}`;
        localStorage.setItem('crosshair-theme', normalizedKey);
    };

    applyTheme(normalizedSaved);

    btn.addEventListener('click', () => {
        const current = root.getAttribute('data-theme') || 'neon';
        const index = themes.findIndex((t) => t.key === current);
        const next = themes[(index + 1) % themes.length].key;
        applyTheme(next);
    });

    const updateHeaderOnScroll = () => {
        if (!header) {
            return;
        }

        header.classList.toggle('scrolled', window.scrollY > 24);
    };

    window.addEventListener('scroll', updateHeaderOnScroll, { passive: true });
    updateHeaderOnScroll();
})();
</script>

</body>
</html>
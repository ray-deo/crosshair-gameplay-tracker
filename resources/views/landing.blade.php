@extends('layouts.app')

@section('content')

<div class="landing">

    <div class="section-nav" id="section-nav">
        <a href="#hero" data-target="hero" class="nav-chip active">CORE</a>
        @auth
            <a href="#command" data-target="command" class="nav-chip">COMMAND</a>
            <a href="#continue" data-target="continue" class="nav-chip">CONTINUE</a>
            <a href="#media" data-target="media" class="nav-chip">MEDIA</a>
        @endauth
        <a href="#features" data-target="features" class="nav-chip">FEATURES</a>
    </div>

    <section class="hero reveal" id="hero" data-section="hero">
        <div class="hero-box">
            <h1 id="hero-welcome" data-default-text="Welcome to Crosshair">
                @auth
                    INITIALIZING...
                @else
                    Welcome to Crosshair
                @endauth
            </h1>

            @auth
                <div id="boot-sequence" class="boot-sequence" aria-live="polite"></div>
            @endauth

            <p>Add games, track status, and grind progress.</p>

            @auth
                <div class="hero-actions">
                    <a href="/library" class="retro-btn">ENTER LIBRARY</a>
                    <a href="/search" class="retro-btn secondary">DISCOVER GAMES</a>
                </div>
            @else
                <a href="/login" class="retro-btn">LOGIN</a>
            @endauth
        </div>
    </section>

    @auth
        <section class="interactive-section reveal" id="command" data-section="command">
            <div class="section-head">
                <h2>Command Center</h2>
                <span>Live profile telemetry</span>
            </div>
            <div class="stats-grid">
                <div class="stat-card">
                    <p>Total Games</p>
                    <strong>{{ $stats['total'] ?? 0 }}</strong>
                </div>
                <div class="stat-card">
                    <p>Favorites</p>
                    <strong>{{ $stats['favorites'] ?? 0 }}</strong>
                </div>
                <div class="stat-card">
                    <p>Backlog</p>
                    <strong>{{ $stats['backlog'] ?? 0 }}</strong>
                </div>
                <div class="stat-card">
                    <p>Completed</p>
                    <strong>{{ $stats['completed'] ?? 0 }}</strong>
                </div>
                <div class="stat-card wide">
                    <p>Average Progress</p>
                    <strong>{{ $stats['avg_progress'] ?? 0 }}%</strong>
                    <div class="meter"><span style="width: {{ $stats['avg_progress'] ?? 0 }}%"></span></div>
                </div>
            </div>
        </section>

        <section class="interactive-section reveal" id="continue" data-section="continue">
            <div class="section-head">
                <h2>Continue Playing</h2>
                <span>Resume your most recent games</span>
            </div>

            <div class="continue-rail">
                @forelse($continueGames as $game)
                    <a href="{{ route('game.show', $game->id) }}" class="continue-card">
                        <img
                            src="{{ $game->steam_appid ? 'https://cdn.cloudflare.steamstatic.com/steam/apps/' . $game->steam_appid . '/library_600x900_2x.jpg' : ($game->cover_url ?? 'https://placehold.co/240x320?text=No+Image') }}"
                            onerror="this.onerror=null; this.src='{{ $game->cover_url ?? 'https://placehold.co/240x320?text=No+Image' }}';"
                            alt="{{ $game->title }}"
                        >
                        <div class="continue-meta">
                            <h3>{{ $game->title }}</h3>
                            <p>Status: {{ ucfirst($game->pivot->status ?? 'backlog') }}</p>
                            <div class="meter small"><span style="width: {{ (int)($game->pivot->progress ?? 0) }}%"></span></div>
                        </div>
                    </a>
                @empty
                    <p class="empty-line">No recent games yet. Add a game to start your rail.</p>
                @endforelse
            </div>

            <div class="discovery-actions">
                @if($randomGameId)
                    <a href="{{ route('game.show', $randomGameId) }}" class="retro-btn">SURPRISE ME</a>
                @else
                    <a href="/search" class="retro-btn">SURPRISE ME</a>
                @endif

                @if($recommendedGameId)
                    <a href="{{ route('game.show', $recommendedGameId) }}" class="retro-btn secondary">WHAT SHOULD I PLAY?</a>
                @else
                    <a href="/search" class="retro-btn secondary">WHAT SHOULD I PLAY?</a>
                @endif
            </div>
        </section>

        <section class="interactive-section reveal" id="media" data-section="media">
            <div class="section-head">
                <h2>Media Wall</h2>
                <span>Your captured gameplay moments</span>
            </div>

            <div class="media-grid">
                @forelse($mediaItems as $item)
                    <a href="{{ route('game.show', $item['game_id']) }}" class="media-item" title="{{ $item['game_title'] }}">
                        @if($item['type'] === 'screenshot')
                            <img src="{{ asset('storage/' . $item['path']) }}" alt="Screenshot from {{ $item['game_title'] }}" class="media-content">
                        @else
                            <video class="media-content" preload="metadata">
                                <source src="{{ asset('storage/' . $item['path']) }}">
                            </video>
                            <div class="video-badge">▶</div>
                        @endif
                        <div class="media-overlay">
                            <p>{{ $item['game_title'] }}</p>
                        </div>
                    </a>
                @empty
                    <p class="empty-line">No media yet. Upload screenshots or videos from your game pages.</p>
                @endforelse
            </div>
        </section>

    @endauth

    <section class="split reveal" id="features" data-section="features">
        <div class="split-inner">
            <div class="text">
                <h2>Track your game collection</h2>
                <p>
                    Crosshair lets you build your personal gaming archive. Add titles directly
                    from RAWG, organize your backlog, and keep everything structured in one place.
                </p>
            </div>
            <div class="image-box">
                <img src="{{ asset('images/library-preview.png') }}" alt="Library Preview">
            </div>
        </div>
    </section>

    <section class="split reverse reveal">
        <div class="split-inner">
            <div class="text">
                <h2>Search from a vast library of games</h2>
                <p>
                    Choose from thousands of games across all platforms. Find titles fast,
                    add them instantly, and build your custom progression lane.
                </p>
            </div>
            <div class="image-box">
                <img src="{{ asset('images/search-preview.png') }}" alt="Search Preview">
            </div>
        </div>
    </section>

    <section class="split reveal">
        <div class="split-inner">
            <div class="text">
                <h2>Record screenshots, notes, and video clips</h2>
                <p>
                    Capture your play moments with notes, screenshots, and videos.
                    Turn each game run into a personal mission log.
                </p>
            </div>
            <div class="image-box">
                <img src="{{ asset('images/notes-preview.png') }}" alt="Notes and Screenshots Preview">
            </div>
        </div>
    </section>

</div>

<style>
html {
    scroll-behavior: smooth;
}

body {
    margin: 0;
    padding: 0;
}

.scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    width: 100%;
    z-index: 160;
    background: linear-gradient(90deg, var(--accent) 0%, color-mix(in srgb, var(--accent) 52%, white) 100%);
    transform-origin: left center;
    transform: scaleX(0);
    box-shadow: 0 0 10px color-mix(in srgb, var(--accent) 70%, transparent);
}

.container {
    width: 100%;
    max-width: none;
    padding: 0;
}

.landing {
    margin-top: 82px;
    padding-bottom: 70px;
}

.section-nav {
    position: sticky;
    top: 76px;
    z-index: 60;
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
    padding: 12px 20px;
    margin-bottom: 22px;
    background: linear-gradient(to bottom, color-mix(in srgb, var(--bg) 88%, black), transparent);
}

.nav-chip {
    border: 1px solid color-mix(in srgb, var(--accent) 60%, transparent);
    color: var(--text);
    padding: 6px 12px;
    font-size: 12px;
    letter-spacing: 0.06em;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, border-color 0.2s ease;
    background: color-mix(in srgb, var(--accent) 6%, transparent);
    position: relative;
    overflow: hidden;
}

.nav-chip::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: currentColor;
    opacity: 0.9;
}

.nav-chip.active,
.nav-chip:hover {
    transform: translateY(-1px);
    background: color-mix(in srgb, currentColor 16%, transparent);
    box-shadow: 0 0 14px color-mix(in srgb, currentColor 40%, transparent);
}

.nav-chip[data-target="hero"] {
    color: #66ffd9;
    border-color: rgba(102, 255, 217, 0.7);
}

.nav-chip[data-target="command"] {
    color: #ff6bd6;
    border-color: rgba(255, 107, 214, 0.7);
}

.nav-chip[data-target="continue"] {
    color: #76c7ff;
    border-color: rgba(118, 199, 255, 0.72);
}

.nav-chip[data-target="media"] {
    color: #ff1493;
    border-color: rgba(255, 20, 147, 0.72);
}

.nav-chip[data-target="features"] {
    color: #ffb763;
    border-color: rgba(255, 183, 99, 0.72);
}

.hero {
    padding: 0 80px;
    margin-bottom: 60px;
}

.hero-box {
    max-width: 980px;
    margin: 0 auto;
    border: 1px solid var(--accent);
    padding: 56px;
    text-align: center;
    box-shadow: 0 0 22px color-mix(in srgb, var(--accent) 24%, transparent);
}

.hero-box h1 {
    font-size: 38px;
    color: var(--text);
    font-family: monospace;
    min-height: 50px;
    margin: 0;
}

.hero-box p {
    font-size: 15px;
    color: color-mix(in srgb, var(--text) 75%, transparent);
    margin-top: 12px;
}

.hero-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.boot-sequence {
    margin: 10px auto 0;
    max-width: 640px;
    min-height: 42px;
    font-size: 12px;
    line-height: 1.5;
    color: color-mix(in srgb, var(--text) 72%, transparent);
    text-align: left;
    border-left: 2px solid color-mix(in srgb, var(--accent) 45%, transparent);
    padding-left: 12px;
    opacity: 0;
    transform: translateY(4px);
    transition: opacity 0.35s ease, transform 0.35s ease;
}

.boot-sequence.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.interactive-section,
.interactive-grid,
.split {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto 44px;
    padding: 0 60px;
    opacity: 0;
    transform: translateY(24px);
    transition: opacity calc(0.65s * var(--flow-speed, 1)) ease, transform calc(0.65s * var(--flow-speed, 1)) ease;
}

[data-section] {
    scroll-margin-top: 170px;
}

.boot-line {
    white-space: nowrap;
    overflow: hidden;
}

.reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.section-head {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 12px;
    margin-bottom: 14px;
}

.section-head h2 {
    margin: 0;
    font-size: 28px;
    color: var(--text);
}

.section-head span {
    font-size: 12px;
    color: color-mix(in srgb, var(--text) 68%, transparent);
    letter-spacing: 0.05em;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 12px;
}

.stat-card {
    border: 1px solid color-mix(in srgb, var(--accent) 48%, transparent);
    padding: 14px;
    background: color-mix(in srgb, var(--accent) 4%, transparent);
}

.stat-card p {
    margin: 0;
    font-size: 12px;
    color: color-mix(in srgb, var(--text) 70%, transparent);
}

.stat-card strong {
    display: block;
    font-size: 24px;
    margin-top: 6px;
}

.stat-card.wide {
    grid-column: span 2;
}

.meter {
    height: 5px;
    background: color-mix(in srgb, var(--accent) 15%, transparent);
    margin-top: 10px;
    overflow: hidden;
}

.meter span {
    display: block;
    height: 100%;
    background: linear-gradient(90deg, color-mix(in srgb, var(--accent) 70%, black), var(--accent));
}

.meter.small {
    height: 4px;
}

.continue-rail {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
    gap: 14px;
}

.continue-card {
    text-decoration: none;
    color: var(--text);
    border: 1px solid color-mix(in srgb, var(--accent) 50%, transparent);
    padding: 8px;
    background: color-mix(in srgb, var(--accent) 4%, transparent);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.continue-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0 18px color-mix(in srgb, var(--accent) 22%, transparent);
}

.continue-card img {
    width: 100%;
    aspect-ratio: 3 / 4;
    object-fit: contain;
    background: #020202;
    border: 1px solid color-mix(in srgb, var(--accent) 35%, transparent);
}

.continue-meta h3 {
    margin: 10px 0 4px;
    font-size: 14px;
    line-height: 1.35;
}

.continue-meta p {
    margin: 0;
    font-size: 11px;
    color: color-mix(in srgb, var(--text) 70%, transparent);
}

.discovery-actions {
    margin-top: 14px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 12px;
}

.media-item {
    position: relative;
    overflow: hidden;
    border: 1px solid color-mix(in srgb, var(--accent) 40%, transparent);
    aspect-ratio: 1;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    background: #000;
}

.media-item:hover {
    transform: scale(1.02);
    box-shadow: 0 0 16px color-mix(in srgb, var(--accent) 28%, transparent);
}

.media-content {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.video-badge {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 42px;
    color: var(--accent);
    opacity: 0.8;
    text-shadow: 0 0 8px rgba(0, 0, 0, 0.8);
    pointer-events: none;
}

.media-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.88), transparent);
    padding: 12px 8px 8px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.media-item:hover .media-overlay {
    opacity: 1;
}

.media-overlay p {
    margin: 0;
    font-size: 12px;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.empty-line {
    font-size: 13px;
    color: color-mix(in srgb, var(--text) 65%, transparent);
}

.split {
    margin-top: 30px;
}

.split-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 60px;
    width: 100%;
    transform: translateY(var(--parallax-shift, 0px));
    transition: transform 0.12s linear;
}

.split.reverse .split-inner {
    flex-direction: row-reverse;
}

.text {
    flex: 1;
}

.text h2 {
    font-size: 30px;
    color: var(--text);
}

.text p {
    font-size: 15px;
    line-height: 1.8;
    color: color-mix(in srgb, var(--text) 72%, transparent);
    margin-top: 15px;
    max-width: 500px;
}

.image-box {
    flex: 1;
    height: 280px;
    border: 1px solid color-mix(in srgb, var(--accent) 45%, transparent);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: #000;
    box-shadow: 0 0 20px color-mix(in srgb, var(--accent) 18%, transparent);
    transition: box-shadow 0.25s ease, transform 0.25s ease;
}

.split.is-visible .image-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 0 28px color-mix(in srgb, var(--accent) 34%, transparent);
}

.image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.retro-btn {
    border: 1px solid var(--accent);
    padding: 10px 20px;
    color: var(--text);
    text-decoration: none;
    font-family: monospace;
    display: inline-block;
    margin-top: 20px;
    transition: 0.2s;
}

.retro-btn.secondary {
    border-color: color-mix(in srgb, var(--accent) 55%, white);
    color: color-mix(in srgb, var(--text) 85%, white);
}

.retro-btn:hover {
    background: var(--accent);
    color: #000;
}

@media (max-width: 1080px) {
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .stat-card.wide {
        grid-column: span 2;
    }

    .media-grid {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    }

}

@media (max-width: 960px) {
    .section-nav {
        top: 70px;
    }

    .hero {
        padding: 0 20px;
    }

    .hero-box {
        padding: 36px 22px;
    }

    .interactive-section,
    .split,
    .split.reverse {
        padding: 0 20px;
        margin-bottom: 28px;
    }

    .split-inner,
    .split.reverse .split-inner {
        flex-direction: column;
        gap: 28px;
    }

    .image-box {
        width: 100%;
        max-width: 600px;
    }

    .media-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
    }
}
</style>

<div id="scroll-progress" class="scroll-progress" aria-hidden="true"></div>

<script>
(() => {
    const sections = document.querySelectorAll('.reveal');
    const progressBar = document.getElementById('scroll-progress');
    const navChips = document.querySelectorAll('.nav-chip[data-target]');
    const heroWelcome = document.getElementById('hero-welcome');
    const bootSequence = document.getElementById('boot-sequence');
    const authName = @json(auth()->check() ? auth()->user()->name : null);

    if (!progressBar) {
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                }
            });
        },
        {
            threshold: 0.18,
            rootMargin: '0px 0px -8% 0px'
        }
    );

    sections.forEach((section) => observer.observe(section));

    const updateEffects = () => {
        const doc = document.documentElement;
        const scrollTop = window.pageYOffset || doc.scrollTop;
        const maxScroll = Math.max(doc.scrollHeight - window.innerHeight, 1);
        const progress = Math.min(scrollTop / maxScroll, 1);

        progressBar.style.transform = `scaleX(${progress})`;

        document.querySelectorAll('.split').forEach((section) => {
            const rect = section.getBoundingClientRect();
            const centerOffset = rect.top + rect.height / 2 - window.innerHeight / 2;
            const drift = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--drift-amount')) || 16;
            const shift = Math.max(Math.min(-centerOffset * 0.04, drift), -drift);
            const inner = section.querySelector('.split-inner');
            if (inner) {
                inner.style.setProperty('--parallax-shift', `${shift}px`);
            }
        });

        const currentSection = (() => {
            const anchors = document.querySelectorAll('[data-section]');
            let current = 'hero';
            anchors.forEach((el) => {
                const rect = el.getBoundingClientRect();
                if (rect.top <= 140) {
                    current = el.getAttribute('data-section');
                }
            });
            return current;
        })();

        navChips.forEach((chip) => {
            chip.classList.toggle('active', chip.dataset.target === currentSection);
        });
    };

    window.addEventListener('scroll', updateEffects, { passive: true });
    window.addEventListener('resize', updateEffects);
    updateEffects();

    if (heroWelcome && bootSequence && authName) {
        const lines = [
            '[BOOT] Session token verified',
            '[SYNC] Pulling player profile',
            '[READY] Welcome to Crosshair, ' + authName
        ];

        const typeText = (el, text, speed = 24) => {
            return new Promise((resolve) => {
                let i = 0;
                el.textContent = '';
                const tick = () => {
                    i += 1;
                    el.textContent = text.slice(0, i);
                    if (i < text.length) {
                        setTimeout(tick, speed);
                    } else {
                        resolve();
                    }
                };
                tick();
            });
        };

        const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

        (async () => {
            bootSequence.classList.add('is-visible');

            for (const line of lines) {
                const row = document.createElement('div');
                row.className = 'boot-line';
                bootSequence.appendChild(row);
                await typeText(row, line, 20);
                await wait(160);
            }

            await typeText(heroWelcome, 'Welcome to Crosshair, ' + authName, 24);
        })();
    }
})();
</script>
@endsection

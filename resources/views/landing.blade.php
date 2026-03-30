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
                <img src="{{ asset('images/library-preview.png') }}" alt="Library Preview">
            </div>

        </div>
    </section>

    {{-- SECTION 2 --}}
    <section class="split reverse">
        <div class="split-inner">

            <div class="text">
                <h2>Search from a vast library of games</h2>
                <p>
                    Choose from thousands of games across all platforms. Crosshair integrates with RAWG to provide you with up-to-date information, cover art, and release dates. Whether it's a hidden indie gem or the latest AAA blockbuster, find it easily and add it to your collection.
                </p>
            </div>

            <div class="image-box">
                <img src="{{ asset('images/search-preview.png') }}" alt="Search Preview">
            </div>

        </div>
    </section>

    {{-- SECTION 3 --}}
    <section class="split">
        <div class="split-inner">

            <div class="text">
                <h2>Record screenshots & notes</h2>
                <p>
                    Capture your thoughts, strategies, and experiences. Add personal notes,
                    write detailed notes, and upload screenshots to document your journey.
                    Turn your gameplay into a record you can revisit anytime.
                </p>
            </div>

            <div class="image-box">
                <img src="{{ asset('images/notes-preview.png') }}" alt="Notes & Screenshots Preview">
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
    background: linear-gradient(90deg, #00ff9c 0%, #6affc7 100%);
    transform-origin: left center;
    transform: scaleX(0);
    box-shadow: 0 0 10px #00ff9c77;
}

.container {
    width: 100%;
    max-width: none;
    padding: 0;
}

.landing {
    margin-top: 80px;
}

.hero {
    padding: 0 80px;
    margin-bottom: 100px;
}

.hero-box {
    max-width: 900px;
    margin: 0 auto;
    border: 1px solid #00ff9c;
    padding: 60px;
    text-align: center;
}

.hero-box h1 {
    font-size: 36px;
    color: #00ff9c;
    font-family: monospace;
}

.hero-box p {
    font-size: 15px;
    color: #00ff9c88;
    margin-top: 10px;
}

/* ---------- SPLIT SECTION ---------- */
.split {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    margin: 120px 0;
    padding: 80px 60px;
    opacity: 0;
    transform: translateY(45px);
    transition: opacity 0.7s ease, transform 0.7s ease;
}

.split.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.split-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 60px;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    transform: translateY(var(--parallax-shift, 0px));
    transition: transform 0.12s linear;
}

/* reverse layout */
.split.reverse .split-inner {
    flex-direction: row-reverse;
}

/* ---------- TEXT ---------- */
.text {
    flex: 1;
}

.text h2 {
    font-size: 30px;
    color: #00ff9c;
}

.text p {
    font-size: 15px;
    line-height: 1.8;
    color: #00ff9c88;
    margin-top: 15px;
    max-width: 500px;
}

/* ---------- IMAGE BOX ---------- */
.image-box {
    flex: 1;
    height: 280px;
    border: 1px solid #00ff9c;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: #000;
    box-shadow: 0 0 20px #00ff9c22;
    transition: box-shadow 0.25s ease, transform 0.25s ease;
}

.split.is-visible .image-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 0 28px #00ff9c44;
}

/* ---------- FIX YOUR IMAGE ISSUE ---------- */
.image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* ---------- PLACEHOLDER ---------- */
.placeholder {
    color: #00ff9c55;
    font-size: 14px;
    letter-spacing: 2px;
    font-family: monospace;
}

/* BUTTON */
.retro-btn {
    border: 1px solid #00ff9c;
    padding: 10px 20px;
    color: #00ff9c;
    text-decoration: none;
    font-family: monospace;
    display: inline-block;
    margin-top: 20px;
    transition: 0.2s;
}

.retro-btn:hover {
    background: #00ff9c;
    color: #000;
}

@media (max-width: 960px) {
    .hero {
        padding: 0 20px;
    }

    .hero-box {
        padding: 36px 22px;
    }

    .split,
    .split.reverse {
        padding: 40px 20px;
        margin: 70px 0;
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
}
</style>

<div id="scroll-progress" class="scroll-progress" aria-hidden="true"></div>

<script>
(() => {
    const sections = document.querySelectorAll('.split');
    const progressBar = document.getElementById('scroll-progress');

    if (!sections.length || !progressBar) {
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
            threshold: 0.22,
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

        sections.forEach((section) => {
            const rect = section.getBoundingClientRect();
            const centerOffset = rect.top + rect.height / 2 - window.innerHeight / 2;
            const shift = Math.max(Math.min(-centerOffset * 0.04, 16), -16);
            const inner = section.querySelector('.split-inner');
            if (inner) {
                inner.style.setProperty('--parallax-shift', `${shift}px`);
            }
        });
    };

    window.addEventListener('scroll', updateEffects, { passive: true });
    window.addEventListener('resize', updateEffects);
    updateEffects();
})();
</script>
@endsection
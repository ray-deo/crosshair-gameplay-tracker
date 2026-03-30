<!DOCTYPE html>
<html>
<head>
    <title>Crosshair Login</title>

    <style>
        :root {
            --accent: #00ff9c;
        }

        body {
            background: black;
            color: var(--accent);
            font-family: monospace;
            margin: 0;
            height: 100vh;
            overflow: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: repeating-linear-gradient(
                to bottom,
                rgba(255, 255, 255, 0.03),
                rgba(255, 255, 255, 0.03) 1px,
                transparent 1px,
                transparent 3px
            );
            mix-blend-mode: screen;
            opacity: 0.45;
            animation: scanFlicker 0.25s steps(2) infinite;
        }

        .glitch-overlay {
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: 0;
            background:
                linear-gradient(90deg, rgba(255, 0, 76, 0.07), transparent 40%, rgba(0, 255, 178, 0.07)),
                radial-gradient(circle at 15% 15%, rgba(0, 255, 156, 0.2), transparent 40%);
            transform: translateX(0);
        }

        /* ===== HEADER ===== */
        .login-header {
            position: absolute;
            top: 20px;
            left: 40px;
        }

        .logo {
            font-size: 18px;
            letter-spacing: 2px;
        }

        .tagline {
            font-size: 12px;
            opacity: 0.7;
        }

        /* ===== CENTER LOGIN ===== */
        .auth-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-box {
            border: 1px solid var(--accent);
            padding: 40px;
            width: 320px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            position: relative;
            animation: bootIn 0.55s steps(8) 1;
        }

        .auth-box::before,
        .auth-box::after {
            content: "";
            position: absolute;
            inset: -1px;
            pointer-events: none;
            opacity: 0;
        }

        .auth-box::before {
            border: 1px solid rgba(255, 0, 60, 0.5);
            transform: translate(-1px, 0);
        }

        .auth-box::after {
            border: 1px solid rgba(0, 255, 200, 0.5);
            transform: translate(1px, 0);
        }

        /* ===== TITLE ===== */
        h2 {
            margin: 0;
            text-align: center;
            letter-spacing: 2px;
        }

        h2::after {
            content: "_";
            animation: blink 1s infinite;
        }

        @keyframes blink {
            50% { opacity: 0; }
        }

        /* ===== INPUT ===== */
        input {
            background: black;
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 8px;
            font-family: monospace;
        }

        input::placeholder {
            color: var(--accent);
            opacity: 0.6;
        }

        /* ===== BUTTON ===== */
        button {
            background: black;
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 8px;
            cursor: pointer;
        }

        button:hover {
            background: var(--accent);
            color: black;
        }

        button.loading {
            cursor: wait;
            pointer-events: none;
            animation: btnGlitch 0.35s steps(2) infinite;
        }

        /* ===== LINK ===== */
        a {
            color: var(--accent);
            text-decoration: none;
            text-align: center;
            margin-top: 10px;
        }

        /* ===== ERROR ===== */
        .error-box {
            position: absolute;
            top: 80px;
            left: 40px;
            color: red;
            font-size: 12px;
        }

        body.loading .glitch-overlay {
            opacity: 1;
            animation: overlayPulse 0.25s steps(2) infinite;
        }

        body.loading .auth-box::before,
        body.loading .auth-box::after {
            opacity: 1;
            animation: ghostShift 0.16s steps(2) infinite;
        }

        @keyframes bootIn {
            0% {
                opacity: 0;
                transform: scale(0.98) translateY(8px);
                filter: contrast(1.6) brightness(1.4);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
                filter: contrast(1) brightness(1);
            }
        }

        @keyframes scanFlicker {
            0% { opacity: 0.38; }
            100% { opacity: 0.48; }
        }

        @keyframes overlayPulse {
            0% { transform: translateX(-1px); filter: hue-rotate(0deg); }
            100% { transform: translateX(1px); filter: hue-rotate(20deg); }
        }

        @keyframes ghostShift {
            0% { transform: translate(-2px, 0); }
            100% { transform: translate(2px, 0); }
        }

        @keyframes btnGlitch {
            0% { transform: translate(0, 0); }
            25% { transform: translate(-1px, 0); }
            50% { transform: translate(1px, 0); }
            75% { transform: translate(0, -1px); }
            100% { transform: translate(0, 0); }
        }

    </style>
</head>

<body>

<div class="glitch-overlay" aria-hidden="true"></div>

<!-- HEADER -->
<div class="login-header">
    <div class="logo">[+] CROSSHAIR</div>
    <div class="tagline">Track • Review • Master Your Games</div>
</div>

<!-- ERRORS -->
@if ($errors->any())
<div class="error-box">
    @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
</div>
@endif

<!-- LOGIN -->
<div class="auth-container">
    <div class="auth-box">

        <h2>LOGIN</h2>

        @if(session('error'))
            <div style="color:red; text-align:center;">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <input 
                type="email" 
                name="email" 
                placeholder="Email" 
                required
                value="{{ old('email') }}"
            >

            <input 
                type="password" 
                name="password" 
                placeholder="Password" 
                required
            >

            <button type="submit" id="loginSubmit">ENTER</button>
        </form>

        <a href="/register">Create Account</a>

    </div>
</div>

<script>
const loginForm = document.getElementById('loginForm');
const loginSubmit = document.getElementById('loginSubmit');

if (loginForm && loginSubmit) {
    loginForm.addEventListener('submit', function (event) {
        if (loginForm.dataset.loading === '1') {
            return;
        }

        event.preventDefault();
        loginForm.dataset.loading = '1';
        document.body.classList.add('loading');
        loginSubmit.classList.add('loading');
        loginSubmit.textContent = 'AUTHENTICATING...';

        setTimeout(function () {
            loginForm.submit();
        }, 450);
    });
}
</script>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>Crosshair Register</title>

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
            transition: opacity 0.35s ease;
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

        /* ===== CENTER FORM ===== */
        .auth-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-box {
            border: 1px solid var(--accent);
            padding: 50px 40px;
            width: 340px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            box-shadow: 0 0 20px #00ff9c22;
            position: relative;
            animation: bootIn 0.55s steps(8) 1;
            transition: transform 0.6s ease, box-shadow 0.6s ease, border-color 0.6s ease;
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
            font-size: 22px;
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
            padding: 10px;
            font-family: monospace;
            outline: none;
        }

        input::placeholder {
            color: #00ff9c66;
        }

        input:focus {
            box-shadow: 0 0 8px #00ff9c66;
        }

        /* ===== BUTTON ===== */
        button {
            background: black;
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 10px;
            cursor: pointer;
            margin-top: 5px;
            transition: 0.2s;
        }

        button:hover {
            background: var(--accent);
            color: black;
        }

        button.loading {
            cursor: wait;
            pointer-events: none;
            animation: btnGlitch 0.85s ease-in-out infinite;
        }

        .flow-status {
            min-height: 18px;
            margin-top: 8px;
            font-size: 11px;
            letter-spacing: 0.08em;
            opacity: 0;
            transform: translateY(4px);
            transition: opacity 0.28s ease, transform 0.28s ease;
        }

        .flow-meter {
            width: 100%;
            height: 3px;
            background: rgba(0, 255, 156, 0.16);
            margin-top: 6px;
            overflow: hidden;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .flow-meter span {
            display: block;
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, rgba(0,255,156,0.3), rgba(0,255,156,1));
            transition: width 1.35s cubic-bezier(0.19, 1, 0.22, 1);
        }

        /* ===== LINK ===== */
        a {
            color: var(--accent);
            text-decoration: none;
            text-align: center;
            margin-top: 10px;
            font-size: 13px;
        }

        /* ===== ERROR ===== */
        .error-box {
            position: absolute;
            top: 80px;
            left: 40px;
            color: #ff4d4d;
            font-size: 12px;
        }

        body.loading .glitch-overlay {
            opacity: 1;
            animation: overlayPulse 1.2s ease-in-out infinite alternate;
        }

        body.loading .auth-box::before,
        body.loading .auth-box::after {
            opacity: 1;
            animation: ghostShift 0.55s ease-in-out infinite alternate;
        }

        body.loading .auth-box {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 0 28px rgba(0, 255, 156, 0.22);
            border-color: rgba(0, 255, 156, 0.85);
        }

        body.loading .flow-status {
            opacity: 0.95;
            transform: translateY(0);
        }

        body.loading .flow-meter {
            opacity: 1;
        }

        body.loading .flow-meter span {
            width: 100%;
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
            0% { transform: translateX(-1px); filter: hue-rotate(0deg) saturate(1); }
            100% { transform: translateX(1px); filter: hue-rotate(16deg) saturate(1.2); }
        }

        @keyframes ghostShift {
            0% { transform: translate(-1px, 0); }
            100% { transform: translate(1px, 0); }
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

<!-- REGISTER -->
<div class="auth-container">
    <div class="auth-box">

        <h2>REGISTER</h2>

        <form method="POST" action="/register" id="registerForm">
            @csrf

            <input type="text" name="name" placeholder="Name" required>

            <input type="email" name="email" placeholder="Email" required>

            <input type="password" name="password" placeholder="Password" required>

            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

            <button type="submit" id="registerSubmit">CREATE</button>
            <div class="flow-status" id="registerFlowStatus" aria-live="polite"></div>
            <div class="flow-meter" aria-hidden="true"><span></span></div>
        </form>

        <a href="/login">Back to Login</a>

    </div>
</div>

<script>
const registerForm = document.getElementById('registerForm');
const registerSubmit = document.getElementById('registerSubmit');
const registerFlowStatus = document.getElementById('registerFlowStatus');

if (registerForm && registerSubmit) {
    registerForm.addEventListener('submit', function (event) {
        if (registerForm.dataset.loading === '1') {
            return;
        }

        event.preventDefault();
        registerForm.dataset.loading = '1';
        document.body.classList.add('loading');
        registerSubmit.classList.add('loading');
        registerSubmit.textContent = 'PROVISIONING...';

        const phases = ['ALLOCATING PROFILE...', 'ENCRYPTING CREDENTIALS...', 'BOOTING ACCOUNT...'];
        let phaseIndex = 0;
        if (registerFlowStatus) {
            registerFlowStatus.textContent = phases[0];
        }

        const phaseTimer = setInterval(function () {
            phaseIndex = Math.min(phaseIndex + 1, phases.length - 1);
            if (registerFlowStatus) {
                registerFlowStatus.textContent = phases[phaseIndex];
            }
        }, 420);

        setTimeout(function () {
            clearInterval(phaseTimer);
            registerForm.submit();
        }, 1400);
    });
}
</script>

</body>
</html>
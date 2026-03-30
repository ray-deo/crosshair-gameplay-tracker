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

    </style>
</head>

<body>

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

        <form method="POST" action="{{ route('login') }}">
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

            <button type="submit">ENTER</button>
        </form>

        <a href="/register">Create Account</a>

    </div>
</div>

</body>
</html>
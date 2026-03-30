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

<!-- REGISTER -->
<div class="auth-container">
    <div class="auth-box">

        <h2>REGISTER</h2>

        <form method="POST" action="/register">
            @csrf

            <input type="text" name="name" placeholder="Name" required>

            <input type="email" name="email" placeholder="Email" required>

            <input type="password" name="password" placeholder="Password" required>

            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

            <button type="submit">CREATE</button>
        </form>

        <a href="/login">Back to Login</a>

    </div>
</div>

</body>
</html>
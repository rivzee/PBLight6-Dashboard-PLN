<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard Kinerja</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            background-image: url("{{ asset('images/bg.jpeg') }}");
            background-size: cover;
            background-position: center;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(to bottom, rgba(155, 215, 248, 0.7), rgba(61, 108, 134, 0.7));
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 1;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            color: #333;
        }

        .login-box {
            background: #ffffffee;
            padding: 30px;
            border-radius: 15px;
            width: 350px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .login-box img {
            width: 180px;
            margin-bottom: 20px;
        }

        h2 {
            margin: 0;
            font-size: 30px;
        }

        p {
            margin: 5px 0 20px;
            font-size: 16px;
            color: #000000;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Tambahan untuk error message */
        .alert-error {
            background-color: #ffcccc;
            color: #cc0000;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Selamat Datang di</h2>
        <p>Dashboard Kinerja</p>
        <div class="login-box">
            <img src="{{ asset('images/logoPLN.png') }}" alt="PLN Logo">

            <!-- Menampilkan pesan error kalau login gagal -->
            @if(session('error'))
                <div class="alert-error">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="text" name="email" placeholder="Email" required value="{{ old('email') }}">
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>

        </div>
    </div>
</body>
</html>

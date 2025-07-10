<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Dashboard Kinerja PLN</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="login-container">
        <!-- Kiri: Gambar -->
        <div class="login-image">
            <div class="login-image-content">
                <h1>Dashboard Kinerja</h1>
                <p>Sistem monitoring dan pengelolaan data kinerja terpadu PT PLN (Persero)</p>
            </div>
        </div>

        <!-- Kanan: Form Login -->
        <div class="login-form">
            <div class="logo-container">
                <div class="pln-logo">
                    <img src="{{ asset('images/logo_pln.png') }}" alt="PLN Logo">
                </div>
            </div>

            <div class="login-title">
                <h2>Selamat Datang</h2>
                <p>Silahkan login untuk melanjutkan</p>
            </div>

            @if(session('error'))
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                <div class="input-group">
                    <input type="text" name="email" placeholder="Email" required value="{{ old('email') }}">
                    <i class="fas fa-envelope"></i>
                </div>

                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fas fa-lock"></i>
                </div>

                <button type="submit" class="login-button" id="loginButton">
                    <div class="button-content">
                        <div class="spinner"></div>
                        <span class="button-text">Masuk</span>
                    </div>
                </button>
            </form>

            <div class="footer-text">
                &copy; {{ date('Y') }} PT PLN (Persero) - Dashboard Kinerja
            </div>
        </div>
    </div>

    <!-- Transition Overlay -->
    <div class="transition-overlay" id="transitionOverlay">
        <div class="transition-content">
            <div class="transition-logo">
                <img src="{{ asset('images/logo_pln.png') }}" alt="PLN Logo">
            </div>
            <div class="loading-text">Memuat Dashboard PLN...</div>
            <div class="loading-bar"></div>
        </div>
    </div>

    <!-- Sparkles -->
    <div id="sparkleContainer"></div>

    <!-- Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            const transitionOverlay = document.getElementById('transitionOverlay');
            const sparkleContainer = document.getElementById('sparkleContainer');

            // Buat efek sparkle
            function createSparkles() {
                for (let i = 0; i < 15; i++) {
                    const sparkle = document.createElement('div');
                    sparkle.classList.add('sparkle');
                    sparkle.style.left = Math.random() * 100 + 'vw';
                    sparkle.style.top = Math.random() * 100 + 'vh';
                    sparkle.style.animationDelay = Math.random() * 2 + 's';
                    sparkleContainer.appendChild(sparkle);
                }
            }

            createSparkles();

            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                loginButton.classList.add('loading');

                setTimeout(function() {
                    transitionOverlay.classList.add('active');
                    setTimeout(function() {
                        loginForm.submit();
                    }, 2200);
                }, 1000);
            });
        });
    </script>
</body>
</html>

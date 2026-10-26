<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Corner - Login with Google</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: url("{{ asset('images/Mac_Background.jpg') }}") no-repeat center center fixed;
            background-size: cover;
            font-family: 'Inter', sans-serif;
            color: #ffffff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .back-btn {
            position: absolute;
            top: 40px;
            left: 50px;
            color: #ffffff;
            text-decoration: none;
            font-size: 46px;
            font-weight: bold;
            z-index: 30;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s;
        }

        .back-btn:hover {
            opacity: 0.6;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 1150px;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            z-index: 10;
        }

        .mascot-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .mascot-wrapper {
            position: relative;
            animation: floatingMascot 3s ease-in-out infinite;
        }

        @keyframes floatingMascot {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-12px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .mascot-login {
            width: 400px;
            position: relative;
            left: -15px;
            filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.65));
        }

        .login-box-wrapper {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .login-box {
            position: relative;
            width: 480px;
            height: 380px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(151, 252, 255, 0.4);
            border-radius: 24px;
            backdrop-filter: blur(18px);
            padding: 40px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45), 0 0 25px rgba(151, 252, 255, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .login-title {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #ffca7a;
            text-shadow: 0 0 8px rgba(255, 202, 122, 0.9), 0 0 18px rgba(255, 177, 66, 0.8);
            text-align: center;
        }

        /* Tombol Google Login Mewah */
        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: #ffffff;
            color: #333333;
            width: 100%;
            padding: 14px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s, background 0.2s;
        }

        .btn-google:hover {
            background: #f1f3f4;
            transform: scale(1.02);
        }

        /* Petunjuk di bawah tombol Google */
        .login-hint {
            margin-top: 22px;
            font-size: 13px;
            line-height: 1.6;
            text-align: center;
            color: #d8e6ea;
        }

        .login-hint strong {
            color: #ffca7a;
        }

        .login-hint .warn {
            color: #ff9f9f;
        }

        .bottom-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background: rgba(30, 30, 30, 0.8);
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            z-index: 5;
        }
    </style>
</head>

<body>

    <a href="{{ url('/') }}" class="back-btn" onclick="event.preventDefault(); window.history.back();">&lsaquo;</a>

    <div class="login-container">
        <!-- Maskot Yucca -->
        <div class="mascot-container">
            <div class="mascot-wrapper">
                <img src="{{ asset('images/yucca_cloud.png') }}" alt="Mascot Yucca" class="mascot-login">
            </div>
        </div>

        <!-- Kotak Utama Login Google -->
        <div class="login-box-wrapper">
            <div class="login-box">
                <div class="login-title">Welcome to AI Corner</div>

                <!-- Tombol Google OAuth -->
                <a href="{{ route('auth.google') }}" class="btn-google">
                    <svg width="20" height="20" viewBox="0 0 24 24">
                        <path fill="#4285F4"
                            d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z" />
                        <path fill="#34A853"
                            d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.19v3.15C3.17 21.32 7.23 24 12 24z" />
                        <path fill="#FBBC05"
                            d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.19C.43 8.12 0 9.87 0 11.7c0 1.83.43 3.58 1.19 5.12l4.09-2.55z" />
                        <path fill="#EA4335"
                            d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.23 0 3.17 2.68 1.19 6.58l4.09 3.15c.95-2.83 3.6-4.98 6.72-4.98z" />
                    </svg>
                    Login with Google
                </a>

                <p class="login-hint">
                    Please login with your <strong>university email</strong><br>
                    On the Google screen, choose <strong>"Use another account"</strong>.<br>
                    <span class="warn">Never click an account that isn't yours.</span>
                </p>
            </div>
        </div>
    </div>

    <div class="bottom-bar"></div>

    {{-- Pesan error dari proses login Google (misal: email di luar domain kampus) --}}
    @if (session('error') || $errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: @json(session('error') ?? $errors->first()),
                confirmButtonColor: '#ff9f43',
                heightAuto: false
            });
        </script>
    @endif

</body>
</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Corner - Admin Login</title>
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

        /* Tombol Back ala Mac di Pojok Kiri Atas */
        .back-btn {
            position: absolute;
            top: 40px;
            left: 50px;
            color: #ffffff;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
            z-index: 30;
            background: rgba(255, 255, 255, 0.1);
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
            transition: background 0.2s;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* Kotak Glassmorphism Login diposisikan pas di tengah */
        .login-box {
            position: relative;
            width: 480px;
            height: 410px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(151, 252, 255, 0.4);
            border-radius: 24px;
            backdrop-filter: blur(18px);
            padding: 40px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45), 0 0 25px rgba(151, 252, 255, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 10;
        }

        .login-title {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 35px;
            color: #ffca7a;
            text-shadow:
                0 0 8px rgba(255, 202, 122, 0.9),
                0 0 18px rgba(255, 177, 66, 0.8),
                0 0 30px rgba(255, 159, 67, 0.5);
            text-align: center;
        }

        /* Form Input */
        .form-group {
            width: 100%;
            margin-bottom: 20px;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .input-wrapper input {
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 12px;
            padding: 16px 20px;
            padding-right: 45px;
            color: #2c2c2c;
            font-size: 16px;
            font-weight: 500;
            outline: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .input-wrapper input::placeholder {
            color: #888888;
        }

        .input-wrapper svg {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            fill: #666666;
        }

        .error-msg {
            color: #ff6b6b;
            font-size: 12px;
            margin-top: 6px;
            font-weight: 500;
            text-align: left;
            width: 100%;
        }

        /* Tombol Login */
        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, #ff9f43 0%, #ff7675 100%);
            border: none;
            border-radius: 12px;
            padding: 16px;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(255, 159, 67, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }

        .login-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(255, 159, 67, 0.6);
        }

        /* Footer Bar */
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

    <!-- Tombol Kembali ala Mac ke Halaman Utama -->
    <a href="{{ url('/') }}" class="back-btn">&lsaquo;</a>

    <!-- Kotak Form Login Admin di Tengah -->
    <div class="login-box">
        <div class="login-title">Admin Portal</div>

        @if (session('error'))
            <div class="error-msg" style="text-align: center; margin-bottom: 15px;">⚠️ {{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.login.process') }}" method="POST" style="width: 100%;">
            @csrf
            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" name="username" placeholder="Username" required autocomplete="off">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
            </div>

            <div class="form-group">
                <div class="input-wrapper">
                    <input type="password" name="password" placeholder="Password" required>
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" />
                    </svg>
                </div>
                @error('username')
                    <div class="error-msg">⚠️ {{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="login-btn">Log In</button>
        </form>
    </div>

    <!-- Footer Bar -->
    <div class="bottom-bar"></div>

</body>

</html>

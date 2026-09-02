<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Corner - Student Login</title>
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

        /* Tombol Back di Pojok Kiri Atas */
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

        /* Container Utama diposisikan tepat di tengah */
        .login-container {
            display: flex;
            width: 100%;
            max-width: 1150px;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            z-index: 10;
            margin-top: -20px;
        }

        /* Kontainer Utama Pembungkus Semuanya */
        .mascot-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .mascot-wrapper::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 350px;
            background: radial-gradient(circle, rgba(151, 252, 255, 0.75) 0%, rgba(151, 252, 255, 0.3) 45%, transparent 70%);
            border-radius: 50%;
            z-index: -1;
            filter: blur(35px);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .mascot-login {
            width: 550px;
            position: relative;
            left: -50px;
            filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.65));
        }

        .mascot-shadow {
            left: 30%;
            bottom: 25px;
            width: 180px;
            height: 18px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 50%;
            filter: blur(6px);
            position: absolute;
            transform: translateX(-50%);
            z-index: 1;
        }

        /* Kotak Glassmorphism Login */
        .login-box {
            position: relative;
            width: 480px;
            height: 380px;
            bottom: 25px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(151, 252, 255, 0.4);
            border-radius: 24px;
            backdrop-filter: blur(18px);
            padding: 40px 40px 100px 40px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45), 0 0 25px rgba(151, 252, 255, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .login-title {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 95px;
            color: #ffca7a;
            text-shadow:
                0 0 8px rgba(255, 202, 122, 0.9),
                0 0 18px rgba(255, 177, 66, 0.8),
                0 0 30px rgba(255, 159, 67, 0.5);
            transform: translateY(35px);
        }

        /* Form Input */
        .form-group {
            width: 100%;
            margin-bottom: 25px;
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

        /* Pesan Peringatan Hanya Angka & Error Tidak Terdaftar (Disamakan 12px, Rata Kiri) */
        .warning-msg {
            color: #ff9f43;
            font-size: 12px;
            margin-top: 6px;
            display: none;
            font-weight: 500;
            text-align: left;
            width: 100%;
        }

        .error-msg {
            color: #ff6b6b;
            font-size: 12px;
            margin-top: 6px;
            display: none;
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
        }

        .login-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(255, 159, 67, 0.6);
        }

        /* Posisi Bawah_Login.png menempel ke dekat bawah kotak */
        .bottom-decoration {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            pointer-events: none;
        }

        /* Efek Trapesium Terbalik (Light Beam) */
        .light-beam {
            position: absolute;
            top: 150px;
            bottom: 50px;
            width: 340px;
            height: 150px;
            background: linear-gradient(to top, rgba(151, 252, 255, 0.85), rgba(151, 252, 255, 0.2) 50%, transparent 90%);
            clip-path: polygon(0% 0%, 100% 0%, 61% 100%, 39% 100%);
            filter: blur(5px);
            z-index: 1;
        }

        .bottom-decoration img {
            width: 150px;
            position: relative;
            top: 220px;
            z-index: 2;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.5));
        }

        /* Shadow Oval di Bawah Bunder Thingy */
        .device-shadow {
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 90px;
            height: 12px;
            background: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            filter: blur(4px);
            z-index: 1;
        }

        /* Footer Bar di Bawah Layar */
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

    <!-- Tombol Kembali ke Dashboard -->
    <a href="{{ url('/') }}" class="back-btn">&lsaquo;</a>

    <div class="mascot-container">
        <div class="mascot-wrapper">
            <!-- Glow Biru & Maskot Kuda -->
            <img src="{{ asset('images/Yucca_Student_Login.png') }}" alt="Student Mascot" class="mascot-login">
            <!-- Shadow Yucca -->
            <div class="mascot-shadow"></div>
        </div>
    </div>

    <!-- Kotak Form Login di Kanan -->
    <div class="login-box">
        <div class="login-title">Login Page</div>

        <form action="{{ route('student.login.submit') }}" method="POST" style="width: 100%;" id="loginForm">
            @csrf
            <div class="form-group">
                <div class="input-wrapper">
                    <!-- Input hanya mengizinkan angka -->
                    <input type="text" name="nim" id="nimInput" placeholder="NIM" required autocomplete="off"
                        oninput="validateNim(this)">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
                <!-- Pesan Error Sistem (NIM Tidak Terdaftar) -->
                <div id="unregisteredError" class="error-msg">⚠️ NIM tidak terdaftar dalam sistem!</div>
                <!-- Pesan Peringatan Hanya Angka -->
                <div id="warningText" class="warning-msg">⚠️ Hanya bisa memasukkan angka!</div>
            </div>

            <button type="submit" class="login-btn">Log In</button>
        </form>

        <!-- Gambar Bawah Login & Efek Trapesium Terbalik plus Shadow Device -->
        <div class="bottom-decoration">
            <div class="light-beam"></div>
            <img src="{{ asset('images/Bawah_Login.png') }}" alt="Bottom Decoration">
            <div class="device-shadow"></div>
        </div>
    </div>

    <!-- Footer Bar -->
    <div class="bottom-bar"></div>

    <script>
        const majorMap = {
            '101601': 'Management - Reguler Class',
            '010601': 'Management - Reguler Class',
            '101602': 'Management - International Class',
            '010602': 'Management - International Class',
            '101604': 'Accounting',
            '010604': 'Accounting',
            '101801': 'Magister of Management',
            '010801': 'Magister of Management',
            '101891': 'Magister of Management (BUF)',
            '010891': 'Magister of Management (BUF)',
            '101901': 'Management S3',
            '010901': 'Management S3',
            '102603': 'Architecture',
            '020603': 'Architecture',
            '102604': 'Visual Communication Design',
            '020604': 'Visual Communication Design',
            '102606': 'Fashion Design and Business',
            '020606': 'Fashion Design and Business',
            '103601': 'Psychology',
            '030601': 'Psychology',
            '103701': 'Professional Psychologist Education',
            '030701': 'Professional Psychologist Education',
            '104601': 'Hotel, Tourism, and Event Business',
            '040601': 'Hotel, Tourism, and Event Business',
            '104602': 'Tourism - Culinary Business',
            '040602': 'Tourism - Culinary Business',
            '104604': 'Food Technology Program',
            '040604': 'Food Technology Program',
            '105601': 'Communication Science',
            '050601': 'Communication Science',
            '106601': 'Medicine',
            '060601': 'Medicine',
            '106701': 'Medical Doctor Profession Education',
            '060701': 'Medical Doctor Profession Education',
            '109601': 'Dental Medicine',
            '090601': 'Dental Medicine',
            '107601': 'Informatics',
            '070601': 'Informatics',
            '107602': 'Information System',
            '070602': 'Information System'
        };

        function validateNim(input) {
            let warning = document.getElementById('warningText');
            let unregisteredError = document.getElementById('unregisteredError');

            let originalValue = input.value;

            // 1. Validasi karakter selain angka (menjaga teks tetap bersih dari huruf)
            if (/[^0-9]/.test(originalValue)) {
                warning.style.display = 'block';
            } else {
                warning.style.display = 'none';
            }

            // Bersihkan input secara otomatis sehingga karakter non-angka tidak pernah masuk
            input.value = originalValue.replace(/[^0-9]/g, '');

            // Reset error tidak terdaftar setiap kali user mengetik
            unregisteredError.style.display = 'none';

            let val = input.value;

            // 2. Jika sudah mencapai 6 digit atau lebih, cek ke dalam mapping jurusan
            if (val.length >= 6) {
                let prefix = val.substring(0, 6);
                if (!majorMap[prefix]) {
                    unregisteredError.style.display = 'block';
                }
            }
        }
    </script>
</body>

</html>
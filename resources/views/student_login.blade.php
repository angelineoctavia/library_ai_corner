<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Corner - Scan Your ID</title>
    <!-- SweetAlert2 untuk Pop-up interaktif -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: url("{{ asset('images/Mac_Background.jpg') }}") no-repeat center center fixed;
            background-size: cover;
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
            font-size: 20px;
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

        /* Container Utama */
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

        /* Maskot & Animasi Floating */
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
            left: 35%;
            bottom: -10px;
            width: 180px;
            height: 18px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 50%;
            filter: blur(6px);
            position: absolute;
            transform: translateX(-50%);
            z-index: 1;
        }

        /* Kotak Glassmorphism */
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

        /* Efek Glow Biru & Gambar Bawah_Login di bawah container */
        .login-box-glow {
            position: absolute;
            bottom: -60px;
            left: 50%;
            transform: translateX(-50%);
            width: 380px;
            height: 120px;
            background: radial-gradient(ellipse at center, rgba(151, 252, 255, 0.5) 0%, rgba(50, 150, 200, 0.15) 50%, transparent 70%);
            filter: blur(25px);
            z-index: 1;
            pointer-events: none;
        }

        .bawah-login-img {
            position: absolute;
            bottom: -220px;
            left: 50%;
            transform: translateX(-50%);
            width: 220px;
            z-index: 3;
            pointer-events: none;
            filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.5));
        }

        .login-title {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #ffca7a;
            text-shadow: 0 0 8px rgba(255, 202, 122, 0.9), 0 0 18px rgba(255, 177, 66, 0.8);
            text-align: center;
        }

        .scanner-img {
            width: 130px;
            margin-bottom: 20px;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.5));
        }

        .manual-link-text {
            color: #adb5bd;
            font-size: 13px;
            text-align: center;
        }

        .manual-link-text a {
            color: #ffca7a;
            text-decoration: none;
            font-weight: 600;
        }

        .manual-link-text a:hover {
            text-decoration: underline;
        }

        #scanner-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .manual-form-container {
            display: none;
            flex-direction: column;
            width: 100%;
            gap: 15px;
        }

        .manual-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 12px;
            padding: 14px 18px;
            color: #2c2c2c;
            font-size: 16px;
            font-weight: 500;
            outline: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-submit-manual {
            width: 100%;
            background: linear-gradient(135deg, #ff9f43 0%, #ff7675 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(255, 159, 67, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-submit-manual:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(255, 159, 67, 0.6);
        }

        .back-to-scan {
            color: #adb5bd;
            font-size: 13px;
            text-align: center;
            cursor: pointer;
            margin-top: 5px;
            text-decoration: underline;
        }

        .back-to-scan:hover {
            color: #ffffff;
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

    <a href="{{ url('/') }}" class="back-btn" onclick="event.preventDefault(); window.history.back();">&#10094;</a>

    <div class="login-container">
        <!-- Maskot Yucca dengan Animasi Floating -->
        <div class="mascot-container">
            <div class="mascot-wrapper">
                <img src="{{ asset('images/Yucca_Student_Login.png') }}" alt="Mascot Yucca" class="mascot-login">
            </div>
            <div class="mascot-shadow"></div>
        </div>

        <!-- Kotak Utama & Elemen Bawah -->
        <div class="login-box-wrapper">
            <div class="login-box">
                <!-- Mode Scan QR -->
                <div id="scan-view" style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                    <div class="login-title">Please Scan Your ID</div>
                    <img src="{{ asset('images/Scanner_Device.png') }}" alt="Scanner Device" class="scanner-img">
                    <div class="manual-link-text">
                        if you cannot login with QR, <a href="#" id="trigger-manual">click here</a>
                    </div>
                </div>

                <!-- Mode Manual Input -->
                <div id="manual-view" class="manual-form-container">
                    <div class="login-title" style="font-size: 24px; margin-bottom: 15px;">Manual Login</div>
                    <input type="text" id="manual-nim-input" class="manual-input"
                        placeholder="Enter ID / Staff ID (Year + 4 digits)...">
                    <div
                        style="font-size: 12px; color: #adb5bd; margin-top: -8px; margin-bottom: 5px; text-align: left;">
                        *Hanya dapat menginput angka (numeric)
                    </div>
                    <button type="button" id="btn-manual-submit" class="btn-submit-manual">Login</button>
                    <div class="back-to-scan" id="trigger-scan">Back to QR Scanner</div>
                </div>

                <!-- Input tersembunyi untuk Scanner Fisik -->
                <input type="text" id="scanner-input" autocomplete="off">
            </div>
            <!-- Efek Glow Biru & Gambar Bawah_Login.png -->
            <div class="login-box-glow"></div>
            <img src="{{ asset('images/Bawah_Login.png') }}" alt="Bottom Element" class="bawah-login-img">
        </div>
    </div>

    <div class="bottom-bar"></div>

    <script>
        const scannerInput = document.getElementById('scanner-input');
        const scanView = document.getElementById('scan-view');
        const manualView = document.getElementById('manual-view');
        const triggerManual = document.getElementById('trigger-manual');
        const triggerScan = document.getElementById('trigger-scan');
        const manualNimInput = document.getElementById('manual-nim-input');
        const btnManualSubmit = document.getElementById('btn-manual-submit');

        // Fokus otomatis konstan untuk scanner fisik
        function keepFocus() {
            if (manualView.style.display !== 'flex' && !Swal.isVisible()) {
                scannerInput.focus();
            }
        }
        setInterval(keepFocus, 100);
        document.addEventListener('click', () => {
            if (manualView.style.display !== 'flex' && !Swal.isVisible()) {
                scannerInput.focus();
            }
        });

        // Tangkap data dari Scanner Fisik (Beri parameter sumber 'qr')
        scannerInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                let scannedData = scannerInput.value.trim();
                scannerInput.value = '';
                if (scannedData) {
                    processLogin(scannedData, 'qr');
                }
            }
        });

        triggerManual.addEventListener('click', (e) => {
            e.preventDefault();
            scanView.style.display = 'none';
            manualView.style.display = 'flex';
            manualNimInput.focus();
        });

        triggerScan.addEventListener('click', () => {
            manualView.style.display = 'none';
            scanView.style.display = 'flex';
            scannerInput.focus();
        });

        // Hanya izinkan tombol angka (0-9) saat diketik di input manual
        manualNimInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                btnManualSubmit.click();
                return;
            }
            if (e.key < '0' || e.key > '9') {
                e.preventDefault();
            }
        });

        // Pencegahan ekstra (mencegah paste huruf atau simbol)
        manualNimInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });

        // Tombol submit manual (Beri parameter sumber 'manual')
        btnManualSubmit.addEventListener('click', () => {
            let identifier = manualNimInput.value.trim();
            if (!identifier) return;

            processLogin(identifier, 'manual');
        });

        // Fungsi AJAX POST ke Laravel Backend dengan parameter source ('qr' atau 'manual')
        function processLogin(identifier, source) {
            fetch("{{ route('student.login.submit') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        identifier: identifier,
                        source: source
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: `Welcome, ${data.name}!`,
                            text: 'Login successful. Redirecting...',
                            timer: 1500,
                            showConfirmButton: false,
                            heightAuto: false
                        }).then(() => {
                            window.location.href = data.redirect_url;
                        });
                    } else {
                        // Pesan khusus jika discan via QR fisik vs input manual
                        let errorText = data.message || 'NIM atau jurusan tidak dikenali dalam sistem.';
                        let errorTitle = 'Login Failed';

                        if (source === 'qr') {
                            errorTitle = 'QR Code Not Recognized';
                            errorText = "Use Universitas Ciputra's official ID card or QR code!";
                        }

                        Swal.fire({
                            icon: 'error',
                            title: errorTitle,
                            text: errorText,
                            confirmButtonColor: '#ff9f43',
                            heightAuto: false
                        }).then(() => {
                            scannerInput.value = '';
                            manualNimInput.value = '';
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Something went wrong. Please try again.',
                    });
                });
        }
    </script>
</body>

</html>

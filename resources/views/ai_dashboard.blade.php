<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Corner - Dashboard</title>
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
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .logout-container {
            position: absolute;
            top: 35px;
            right: 45px;
            z-index: 30;
        }

        .logout-btn {
            background: rgba(255, 107, 107, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s, transform 0.2s;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
        }

        .logout-btn:hover {
            background: rgba(255, 71, 87, 1);
            transform: scale(1.05);
        }

        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 980px;
            padding: 0;
            margin-bottom: 30px;
        }

        .welcome-text {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            align-self: flex-start;
            width: 100%;
            padding-left: 0;
        }

        .welcome-text span {
            color: #ffca7a;
            text-shadow:
                0 0 8px rgba(255, 202, 122, 0.9),
                0 0 18px rgba(255, 177, 66, 0.8),
                0 0 30px rgba(255, 159, 67, 0.5);
        }

        .student-nim-display {
            font-size: 15px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            align-self: flex-start;
            width: 100%;
            text-align: left;
            padding-left: 0;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }

        .glass-box {
            width: 100%;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            backdrop-filter: blur(18px);
            padding: 45px 50px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 30px;
            justify-items: center;
            align-items: center;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45);
        }

        .ai-icon-card {
            width: 90px;
            height: 90px;
            border-radius: 22px;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
            display: block;
        }

        .ai-icon-card:hover {
            transform: scale(1.12);
            box-shadow: 0 10px 25px rgba(255, 177, 66, 0.6);
        }

        .ai-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .ai-name {
            margin-top: 8px;
            color: #ffcf70;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            text-shadow: 0 0 6px rgba(255, 177, 66, 0.35);
        }

        .ai-icon-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            z-index: 10;
        }

        .mascot {
            position: absolute;
            bottom: 35px;
            right: 45px;
            width: 320px;
            z-index: 20;
            filter: drop-shadow(0 12px 18px rgba(0, 0, 0, 0.55));
        }

        .admin-link {
            position: absolute;
            bottom: 20px;
            right: 40px;
            color: #ff9f43;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            z-index: 30;
            transition: color 0.2s;
        }

        .admin-link:hover {
            color: #ffb142;
            text-decoration: underline;
        }

        /* Idle Warning Modal */
        .idle-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .idle-modal-box {
            background: #ffffff;
            color: #2c2c2c;
            padding: 30px 35px;
            border-radius: 16px;
            text-align: center;
            width: 340px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .idle-modal-box h3 {
            margin-bottom: 10px;
            font-size: 18px;
        }

        .idle-modal-box p {
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
        }

        .idle-modal-box #idle-countdown {
            font-weight: bold;
            color: #F27D00;
        }

        .idle-stay-btn {
            background: #F27D00;
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
        }

        .idle-stay-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>

    @if (session()->has('student_nim'))
        <div class="logout-container">
            <form id="logout-form" action="{{ route('student.logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    Log Out
                    <svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 24 24">
                        <path
                            d="M16 13v-2H7V8l-5 4 5 4v-3h9zM20 3h-9c-1.1 0-2 .9-2 2v4h2V5h9v14h-9v-4H9v4c0 1.1.9 2 2 2h9c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
                    </svg>
                </button>
            </form>
        </div>
    @endif

    <div class="main-container">
        <div class="welcome-text">
            Welcome to<br>
            <span>UC Library's AI Corner!</span>
        </div>

        @if (session()->has('student_nim'))
            <div class="student-nim-display">
                {{ session('student_nim') }} &bull; <span style="color: #ffb142;">{{ session('student_major') }}</span>
            </div>
        @endif

        <div class="glass-box">
            @foreach ($aiTools as $tool)
                <div class="ai-item">
                    @if (session()->has('student_nim'))
                        <a href="{{ route('ai.visit', $tool->ai_id) }}"
                            onclick="openAiTool(event, this.href, {{ $tool->ai_id }})" class="ai-icon-card"
                            title="{{ $tool->ai_name }}">
                            <img src="{{ asset('storage/' . $tool->ai_icon) }}" alt="{{ $tool->ai_name }}">
                        </a>
                    @else
                        <a href="{{ route('student.login.page') }}" class="ai-icon-card" title="{{ $tool->ai_name }}">
                            <img src="{{ asset('storage/' . $tool->ai_icon) }}" alt="{{ $tool->ai_name }}">
                        </a>
                    @endif

                    <div class="ai-name">{{ $tool->ai_name }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bottom-bar"></div>

    <img src="{{ asset('images/Yucca_Futuristic.png') }}" alt="Mascot" class="mascot">
    <a href="{{ route('admin.login') }}" class="admin-link">Admin Login</a>

    <!-- Idle Warning Modal -->
    @if (session()->has('student_nim'))
        <div id="idle-modal" class="idle-modal-overlay">
            <div class="idle-modal-box">
                <h3>Masih di sini?</h3>
                <p>Sesi akan otomatis logout dalam <span id="idle-countdown">30</span> detik.</p>
                <button type="button" id="idle-stay-btn" class="idle-stay-btn">Ya, Lanjutkan</button>
            </div>
        </div>
    @endif

    <script>
        // ================== TAB TRACKING ==================
        const openedTabs = {};

        function openAiTool(event, url, toolId) {
            event.preventDefault();
            const key = 'ai_tab_' + toolId;

            if (openedTabs[key] && !openedTabs[key].closed) {
                openedTabs[key].focus();
            } else {
                openedTabs[key] = window.open(url, key);
            }
        }

        function closeAllOpenedAiTabs() {
            Object.values(openedTabs).forEach(win => {
                if (win && !win.closed) {
                    win.close();
                }
            });
        }

        // ================== DURATION TRACKING (beacon) ==================
        function sendCloseSessionBeacon() {
            navigator.sendBeacon(
                '{{ route('ai.close-session') }}',
                new URLSearchParams({
                    _token: '{{ csrf_token() }}'
                })
            );
        }

        @if (session()->has('student_nim'))
            // ================== IDLE TIMEOUT (pause saat dashboard tidak aktif) ==================
            let idleTimer, countdownTimer, countdownVal;
            const IDLE_LIMIT_MS = 10 * 60 * 1000; // 10 menit tanpa aktivitas DI DASHBOARD -> warning
            const COUNTDOWN_SEC = 30; // waktu respon sebelum auto-logout
            const HARD_SESSION_LIMIT_MS = 1 * 60 * 60 * 1000; // 1 jam hard cap, jaring pengaman mutlak
            const sessionStartTime = Date.now();

            function resetIdleTimer() {
                clearTimeout(idleTimer);
                if (document.visibilityState === 'visible') {
                    idleTimer = setTimeout(showIdleModal, IDLE_LIMIT_MS);
                }
            }

            function showIdleModal() {
                document.getElementById('idle-modal').style.display = 'flex';
                countdownVal = COUNTDOWN_SEC;
                document.getElementById('idle-countdown').textContent = countdownVal;

                countdownTimer = setInterval(() => {
                    countdownVal--;
                    document.getElementById('idle-countdown').textContent = countdownVal;
                    if (countdownVal <= 0) {
                        forceLogout();
                    }
                }, 1000);
            }

            function forceLogout() {
                clearInterval(countdownTimer);
                clearTimeout(idleTimer);
                sendCloseSessionBeacon();
                closeAllOpenedAiTabs();
                document.getElementById('logout-form').submit();
            }

            document.getElementById('idle-stay-btn').addEventListener('click', () => {
                clearInterval(countdownTimer);
                document.getElementById('idle-modal').style.display = 'none';
                resetIdleTimer();
            });

            // Aktivitas DI DASHBOARD reset timer
            ['mousemove', 'keydown', 'click', 'touchstart'].forEach(evt =>
                document.addEventListener(evt, resetIdleTimer)
            );

            // Dashboard di-hide (user pindah ke tab AI/WA/Docs) -> pause idle timer.
            // Dashboard aktif lagi -> resume timer + catat durasi sesi AI yang masih terbuka.
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    sendCloseSessionBeacon();
                    resetIdleTimer();
                } else {
                    clearTimeout(idleTimer);
                }
            });

            // Hard cap: paksa logout kalau sudah lewat batas absolut, apapun kondisinya
            setInterval(() => {
                if (Date.now() - sessionStartTime >= HARD_SESSION_LIMIT_MS) {
                    forceLogout();
                }
            }, 60000);

            resetIdleTimer();

            // Logout manual: catat durasi + tutup semua tab AI
            document.getElementById('logout-form').addEventListener('submit', () => {
                sendCloseSessionBeacon();
                closeAllOpenedAiTabs();
            });
        @endif
    </script>

</body>
</html>
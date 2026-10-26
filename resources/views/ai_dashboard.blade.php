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
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45);
            overflow: hidden;
        }

        .ai-icons-scroll {
            max-height: calc(100vh - 260px);
            overflow-y: auto;
            padding: 45px 50px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 30px;
            justify-items: center;
            align-content: start;
        }

        .ai-icons-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .ai-icons-scroll::-webkit-scrollbar-thumb {
            background: rgba(13, 115, 121, 0.5);
            border-radius: 10px;
        }

        .ai-icons-scroll::-webkit-scrollbar-track {
            background: transparent;
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

        /* Dipakai kalau browser nge-block tab AI otomatis: icon-nya berkedip
           biar user tau tinggal klik icon itu buat buka */
        .ai-icon-card.attention {
            animation: pulse-glow 1.2s ease-in-out infinite;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
            }

            50% {
                box-shadow: 0 0 28px 8px rgba(255, 177, 66, 0.9);
            }
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
            bottom: 15px;
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

        /* Tombol Feedback (di bar bawah, kiri) */
        .feedback-bar {
            position: absolute;
            bottom: 12px;
            left: 40px;
            z-index: 30;
            display: flex;
            gap: 10px;
        }

        .feedback-btn {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 202, 122, 0.55);
            color: #ffca7a;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
        }

        .feedback-btn:hover {
            background: rgba(255, 202, 122, 0.2);
            transform: translateY(-1px);
        }

        /* Modal Feedback */
        .feedback-box {
            width: 440px;
            text-align: left;
        }

        .feedback-box textarea {
            width: 100%;
            min-height: 120px;
            resize: vertical;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            color: #2c2c2c;
            outline: none;
        }

        .feedback-box textarea:focus {
            border-color: #F27D00;
        }

        .feedback-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 6px 0 14px 0;
            font-size: 12px;
            color: #888;
        }

        .feedback-status {
            min-height: 16px;
            font-size: 12px;
            font-weight: 600;
        }

        .feedback-status.error {
            color: #d63031;
        }

        .feedback-status.success {
            color: #27ae60;
        }

        .feedback-box .feedback-hint {
            margin-bottom: 12px;
        }

        /* Tooltip deskripsi AI (hover) */
        .ai-tooltip {
            position: fixed;
            z-index: 10000;
            max-width: 260px;
            padding: 10px 14px;
            background: rgba(20, 20, 20, 0.92);
            color: #ffffff;
            border: 1px solid rgba(255, 202, 122, 0.5);
            border-radius: 12px;
            font-size: 12.5px;
            line-height: 1.45;
            white-space: pre-line;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.45);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s ease;
        }

        .ai-tooltip.show {
            opacity: 1;
        }

        .ai-tooltip strong {
            display: block;
            color: #ffca7a;
            font-size: 13px;
            margin-bottom: 3px;
        }

        /* Tombol "i" kecil di pojok icon AI */
        .ai-icon-wrap {
            position: relative;
            width: 90px;
            height: 90px;
        }

        .ai-info-btn {
            position: absolute;
            top: -7px;
            right: -7px;
            z-index: 5;
            width: 24px;
            height: 24px;
            padding: 0;
            border-radius: 50%;
            background: rgba(20, 20, 20, 0.9);
            border: 1.5px solid #ffca7a;
            color: #ffca7a;
            font-family: Georgia, 'Times New Roman', serif;
            font-style: italic;
            font-weight: bold;
            font-size: 14px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.45);
            transition: transform 0.15s ease, background 0.15s ease, color 0.15s ease;
        }

        .ai-info-btn:hover {
            background: #ffca7a;
            color: #1a1a1a;
            transform: scale(1.15);
        }

        /* Pop-up info AI (deskripsi + panduan lengkap) */
        .idle-modal-box.info-box {
            width: 480px;
            max-width: 92vw;
            max-height: 80vh;
            text-align: left;
            display: flex;
            flex-direction: column;
        }

        .idle-modal-box .info-head {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .idle-modal-box .info-head img {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .idle-modal-box .info-head h3 {
            margin: 0;
            font-size: 20px;
        }

        .info-desc {
            font-size: 13.5px;
            color: #555;
            line-height: 1.5;
            margin-bottom: 14px;
            white-space: pre-line;
        }

        .info-guide-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #F27D00;
            margin-bottom: 6px;
        }

        .info-guide {
            font-size: 13.5px;
            color: #333;
            line-height: 1.55;
            white-space: pre-line;
            overflow-y: auto;
            max-height: 38vh;
            padding-right: 6px;
            margin-bottom: 18px;
        }

        .info-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
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

        .idle-stay-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>

<body>

    {{-- $userEmail = email user yang sedang login (null kalau belum login).
         Dipakai sebagai penanda "sudah login" di seluruh halaman ini. --}}
    @if ($userEmail)
        <div class="logout-container">
            <form id="logout-form" action="{{ route('student.logout') }}" method="POST">
                @csrf
                <button type="button" id="logout-trigger-btn" class="logout-btn">
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

        @if ($userEmail)
            <div class="student-nim-display">
                Welcome, <span style="color: #ffb142;">{{ $displayName }}</span> &bull; {{ $userType }}
            </div>
        @endif

        <div class="glass-box">
            <div class="ai-icons-scroll">
                @foreach ($aiTools as $tool)
                    <div class="ai-item">
                        <div class="ai-icon-wrap">
                            <a href="{{ route('ai.visit', $tool->ai_id) }}" id="ai-icon-{{ $tool->ai_id }}"
                                @if ($userEmail) onclick="openAiTool(event, this.href, {{ $tool->ai_id }})" @endif
                                class="ai-icon-card" aria-label="{{ $tool->ai_name }}" data-name="{{ $tool->ai_name }}"
                                data-description="{{ $tool->ai_description }}">
                                <img src="{{ asset('storage/' . $tool->ai_icon) }}" alt="{{ $tool->ai_name }}">
                            </a>

                            {{-- Tombol "i": hanya muncul kalau AI ini punya deskripsi / panduan --}}
                            @if (filled($tool->ai_description) || filled($tool->ai_guide))
                                <button type="button" class="ai-info-btn" aria-label="About {{ $tool->ai_name }}"
                                    data-id="{{ $tool->ai_id }}" data-name="{{ $tool->ai_name }}"
                                    data-description="{{ $tool->ai_description }}" data-guide="{{ $tool->ai_guide }}"
                                    data-icon="{{ asset('storage/' . $tool->ai_icon) }}"
                                    data-url="{{ route('ai.visit', $tool->ai_id) }}">i</button>
                            @endif
                        </div>
                        <div class="ai-name">{{ $tool->ai_name }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bottom-bar"></div>

        {{-- 2 tombol feedback (dipisah biar usulan AI & keluhan nggak tercampur). Hanya muncul kalau sudah login. --}}
        @if ($userEmail)
            <div class="feedback-bar">
                <button type="button" class="feedback-btn" data-feedback="ai_request">Suggest an AI</button>
                <button type="button" class="feedback-btn" data-feedback="complaint">Complaints &amp; Feedback</button>
            </div>
        @endif

        <img src="{{ asset('images/yucca_standee_official.png') }}" alt="Mascot" class="mascot">
        <a href="{{ route('admin.login') }}" class="admin-link">Admin Login</a>

        <!-- Idle Warning Modal -->
        @if ($userEmail)
            <div id="idle-modal" class="idle-modal-overlay">
                <div class="idle-modal-box">
                    <h3>Still here?</h3>
                    <p>Session will automatically logout in <span id="idle-countdown">30</span> seconds.</p>
                    <button type="button" id="idle-stay-btn" class="idle-stay-btn">Yes, continue</button>
                </div>
            </div>

            <div id="logout-confirm-overlay" class="idle-modal-overlay">
                <div class="idle-modal-box">
                    <h3>Are you sure you want to log out?</h3>
                    <p>Please make sure to close all active AI tool tabs manually before logging out, as some tools may
                        not close automatically. Thank you.</p>
                    <div style="display:flex; gap:10px; justify-content:center;">
                        <button type="button" id="logout-cancel-btn" class="idle-stay-btn"
                            style="background:#6c757d;">Cancel</button>
                        <button type="button" id="logout-confirm-btn" class="idle-stay-btn">Yes, Logout</button>
                    </div>
                </div>
            </div>

            <!-- Modal Feedback -->
            <div id="feedback-overlay" class="idle-modal-overlay">
                <div class="idle-modal-box feedback-box">
                    <h3 id="feedback-title">Feedback</h3>
                    <p id="feedback-hint" class="feedback-hint"></p>
                    <textarea id="feedback-message" maxlength="1000"></textarea>
                    <div class="feedback-meta">
                        <span id="feedback-status" class="feedback-status"></span>
                        <span id="feedback-counter">0/1000</span>
                    </div>
                    <div style="display:flex; gap:10px; justify-content:flex-end;">
                        <button type="button" id="feedback-cancel-btn" class="idle-stay-btn"
                            style="background:#6c757d;">Cancel</button>
                        <button type="button" id="feedback-submit-btn" class="idle-stay-btn">Send</button>
                    </div>
                </div>
            </div>
        @endif

        <script>
            window.name = ''; // reset - cegah tab dashboard ke-collide sama nama target 'ai_tab_X'

            // TAB TRACKING
            const openedTabs = {};

            /*
             * Buka tab AI, ATAU fokus ke tab yang sudah ada kalau sebelumnya sudah dibuka.
             *
             * Triknya: window.open('', nama) itu "cari window dengan nama itu".
             *  - Kalau sudah ada  -> dapat window-nya (isinya website AI = beda origin,
             *    jadi akses .location akan error -> kita tau itu tab asli, tinggal fokus)
             *  - Kalau belum ada  -> browser bikin tab kosong baru (about:blank,
             *    bisa diakses) -> baru kita arahkan ke URL AI-nya
             *
             * Return true kalau tab siap, false kalau di-block popup blocker.
             */
            function openOrFocusAiTab(toolId, url) {
                const key = 'ai_tab_' + toolId;

                // 1. Referensi tab masih hidup -> fokus aja
                if (openedTabs[key] && !openedTabs[key].closed) {
                    openedTabs[key].focus();
                    return true;
                }

                // 2. Cari tab bernama sama (misal yang dibuka dari halaman login)
                const win = window.open('', key);
                if (!win) {
                    return false; // di-block browser
                }

                let isNewBlankTab = false;
                try {
                    isNewBlankTab = (win.location.href === 'about:blank');
                } catch (e) {
                    isNewBlankTab = false; // cross-origin = tab AI yang sudah ada
                }

                if (isNewBlankTab) {
                    win.location.href = url; // tab baru -> arahkan ke AI (sekaligus nge-log lewat /ai/visit)
                } else {
                    win.focus(); // tab AI sudah ada -> cukup fokus, jangan di-reload
                }

                openedTabs[key] = win;
                return true;
            }

            function openAiTool(event, url, toolId) {
                event.preventDefault();
                if (event.currentTarget) {
                    event.currentTarget.classList.remove('attention');
                }
                openOrFocusAiTab(toolId, url);
            }

            @if ($userEmail && request()->has('reacquire_tab'))
                // Baru selesai login dari klik icon AI: sambungkan referensi ke tab AI
                // yang sudah dibuka halaman login (atau buka kalau belum ada).
                (function() {
                    const id = {{ (int) request('reacquire_tab') }};
                    const icon = document.getElementById('ai-icon-' + id);

                    if (icon) {
                        const ok = openOrFocusAiTab(id, icon.href);
                        if (!ok) {
                            // Browser nge-block buka tab otomatis -> icon berkedip, tinggal klik
                            icon.classList.add('attention');
                        }
                    }

                    // Bersihin ?reacquire_tab=.. dari URL supaya refresh halaman
                    // tidak memicu buka tab lagi
                    history.replaceState(null, '', window.location.pathname);
                })();
            @endif

            function closeAllOpenedAiTabs() {
                Object.values(openedTabs).forEach(win => {
                    if (win && !win.closed) {
                        win.close();
                    }
                });
            }

            // DURATION TRACKING (beacon)
            function sendCloseSessionBeacon() {
                navigator.sendBeacon(
                    '{{ route('ai.close-session') }}',
                    new URLSearchParams({
                        _token: '{{ csrf_token() }}'
                    })
                );
            }

            @if ($userEmail)
                // IDLE TIMEOUT (pause saat dashboard tidak aktif)
                let idleTimer, countdownTimer, countdownVal;
                const IDLE_LIMIT_MS = 15 * 60 * 1000; // 15 menit tanpa aktivitas DI DASHBOARD -> warning
                const COUNTDOWN_SEC = 30; // waktu respon sebelum auto-logout
                //const HARD_SESSION_LIMIT_MS = 1 * 60 * 60 * 1000; // 1 jam hard cap, jaring pengaman mutlak
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

                // UBAH KE true DI PC PERPUS. Biar login session tdk tersimpan, mencegah menggunakan akun org lain.
                const SIGN_OUT_GOOGLE_ON_LOGOUT = false;

                /*
                 * Logout aplikasi. Kalau SIGN_OUT_GOOGLE_ON_LOGOUT = true, sekalian coba
                 * keluarkan login Google di browser ini (best effort), supaya akun mahasiswa
                 * ini nggak nyangkut di pilihan akun Google buat orang berikutnya.
                 *
                 * Logout Google dibuka di jendela kecil (bukan di halaman ini), ditutup
                 * otomatis, baru form logout aplikasi dikirim. Kalau jendelanya di-block
                 * browser, logout aplikasi tetap jalan seperti biasa.
                 */
                function logoutNow() {
                    sendCloseSessionBeacon();
                    closeAllOpenedAiTabs();

                    const form = document.getElementById('logout-form');

                    if (!SIGN_OUT_GOOGLE_ON_LOGOUT) {
                        form.submit();
                        return;
                    }

                    let googleWin = null;
                    try {
                        googleWin = window.open('https://accounts.google.com/Logout', 'google_signout',
                            'width=420,height=320');
                    } catch (e) {}

                    if (!googleWin) {
                        form.submit();
                        return;
                    }

                    setTimeout(() => {
                        try {
                            googleWin.close();
                        } catch (e) {}
                        form.submit();
                    }, 2500);
                }

                function forceLogout() {
                    clearInterval(countdownTimer);
                    clearTimeout(idleTimer);
                    logoutNow();
                }

                document.getElementById('idle-stay-btn').addEventListener('click', () => {
                    clearInterval(countdownTimer);
                    document.getElementById('idle-modal').style.display = 'none';
                    resetIdleTimer();
                });

                document.getElementById('logout-trigger-btn').addEventListener('click', () => {
                    document.getElementById('logout-confirm-overlay').style.display = 'flex';
                });

                document.getElementById('logout-cancel-btn').addEventListener('click', () => {
                    document.getElementById('logout-confirm-overlay').style.display = 'none';
                });

                document.getElementById('logout-confirm-btn').addEventListener('click', () => {
                    document.getElementById('logout-confirm-overlay').style.display = 'none';

                    logoutNow();
                });

                // Aktivitas DI DASHBOARD reset timer
                ['mousemove', 'keydown', 'click', 'touchstart'].forEach(evt =>
                    document.addEventListener(evt, resetIdleTimer)
                );

                // Dashboard di-hide (user pindah ke tab AI/WA/Docs) -> pause idle timer.
                // Dashboard aktif lagi -> resume timer.
                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'visible') {
                        sendCloseSessionBeacon();
                        resetIdleTimer();
                    } else {
                        clearTimeout(idleTimer);
                    }
                });

                // Hard cap: paksa logout kalau sudah lewat batas absolut, apapun kondisinya
                /*setInterval(() => {
                    if (Date.now() - sessionStartTime >= HARD_SESSION_LIMIT_MS) {
                        forceLogout();
                    }
                }, 60000);*/

                resetIdleTimer();

                // Logout manual: catat durasi + tutup semua tab AI
                document.getElementById('logout-form').addEventListener('submit', () => {
                    sendCloseSessionBeacon();
                    closeAllOpenedAiTabs();
                });

                // ================== FEEDBACK ==================
                const FEEDBACK_TEXT = {
                    ai_request: {
                        title: 'Suggest a New AI Tool',
                        hint: 'Enter the name of the AI tool you would like to see and why it should be added.',
                        placeholder: 'e.g., Gemini, to help summarize research papers...'
                    },
                    complaint: {
                        title: 'Feedback & Complaints',
                        hint: 'Let us know about any issues, feedback, or suggestions regarding the AI Corner.',
                        placeholder: 'Type your feedback or complaint here...'
                    }
                };

                const feedbackOverlay = document.getElementById('feedback-overlay');
                const feedbackMessage = document.getElementById('feedback-message');
                const feedbackStatus = document.getElementById('feedback-status');
                const feedbackCounter = document.getElementById('feedback-counter');
                const feedbackSubmit = document.getElementById('feedback-submit-btn');
                let feedbackCategory = null;

                function setFeedbackStatus(text, type) {
                    feedbackStatus.textContent = text || '';
                    feedbackStatus.className = 'feedback-status' + (type ? ' ' + type : '');
                }

                function openFeedback(category) {
                    feedbackCategory = category;
                    const t = FEEDBACK_TEXT[category];
                    document.getElementById('feedback-title').textContent = t.title;
                    document.getElementById('feedback-hint').textContent = t.hint;
                    feedbackMessage.placeholder = t.placeholder;
                    feedbackMessage.value = '';
                    feedbackCounter.textContent = '0/1000';
                    setFeedbackStatus('');
                    feedbackSubmit.disabled = false;
                    feedbackOverlay.style.display = 'flex';
                    feedbackMessage.focus();
                }

                function closeFeedback() {
                    feedbackOverlay.style.display = 'none';
                    feedbackCategory = null;
                }

                document.querySelectorAll('[data-feedback]').forEach(btn => {
                    btn.addEventListener('click', () => openFeedback(btn.dataset.feedback));
                });

                document.getElementById('feedback-cancel-btn').addEventListener('click', closeFeedback);

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape' && feedbackOverlay.style.display === 'flex') {
                        closeFeedback();
                    }
                });

                feedbackMessage.addEventListener('input', () => {
                    feedbackCounter.textContent = feedbackMessage.value.length + '/1000';
                    setFeedbackStatus('');
                });

                feedbackSubmit.addEventListener('click', () => {
                    const message = feedbackMessage.value.trim();
                    if (message.length < 5) {
                        setFeedbackStatus('Pesan terlalu pendek (minimal 5 karakter).', 'error');
                        return;
                    }

                    feedbackSubmit.disabled = true;
                    setFeedbackStatus('Mengirim...', '');

                    fetch('{{ route('feedback.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                category: feedbackCategory,
                                message: message
                            })
                        })
                        .then(async res => {
                            const data = await res.json().catch(() => ({}));
                            if (res.ok && data.success) {
                                setFeedbackStatus('Terima kasih! Masukan kamu sudah terkirim.', 'success');
                                setTimeout(closeFeedback, 1500);
                                return;
                            }
                            const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
                            setFeedbackStatus(firstError || data.message || 'Gagal mengirim. Coba lagi.',
                                'error');
                            feedbackSubmit.disabled = false;
                        })
                        .catch(() => {
                            setFeedbackStatus('Gagal mengirim. Cek koneksi lalu coba lagi.', 'error');
                            feedbackSubmit.disabled = false;
                        });
                });
            @endif
        </script>

        {{-- Tooltip deskripsi AI saat hover. Sengaja di luar kotak kaca (glass-box) supaya tidak terpotong overflow. --}}
        <div id="ai-tooltip" class="ai-tooltip" role="tooltip"></div>

        {{-- Pop-up info AI: deskripsi + panduan lengkap (dibuka lewat tombol "i") --}}
        <div id="info-overlay" class="idle-modal-overlay">
            <div class="idle-modal-box info-box">
                <div class="info-head">
                    <img id="info-icon" src="" alt="">
                    <h3 id="info-title"></h3>
                </div>
                <div id="info-desc" class="info-desc"></div>
                <div id="info-guide-wrap">
                    <div class="info-guide-title">Guide</div>
                    <div id="info-guide" class="info-guide"></div>
                </div>
                <div class="info-actions">
                    <button type="button" id="info-close-btn" class="idle-stay-btn"
                        style="background:#6c757d;">Close</button>
                    <button type="button" id="info-open-btn" class="idle-stay-btn">Open tool</button>
                </div>
            </div>
        </div>

        <script>
            (function() {
                const tip = document.getElementById('ai-tooltip');

                function showTip(card) {
                    const desc = (card.dataset.description || '').trim();
                    if (!desc) return; // belum ada deskripsi -> tidak ada tooltip

                    tip.innerHTML = '';
                    tip.appendChild(document.createTextNode(desc));

                    tip.classList.add('show');
                    const r = card.getBoundingClientRect();
                    const w = tip.offsetWidth,
                        h = tip.offsetHeight;
                    let left = r.left + r.width / 2 - w / 2;
                    left = Math.max(8, Math.min(left, window.innerWidth - w - 8));
                    let top = r.top - h - 12;
                    if (top < 8) top = r.bottom + 12; // mentok atas -> taruh di bawah icon
                    tip.style.left = left + 'px';
                    tip.style.top = top + 'px';
                }

                function hideTip() {
                    tip.classList.remove('show');
                }

                document.querySelectorAll('.ai-icon-card').forEach(card => {
                    card.addEventListener('mouseenter', () => showTip(card));
                    card.addEventListener('mouseleave', hideTip);
                    card.addEventListener('click', hideTip);
                });

                const scroller = document.querySelector('.ai-icons-scroll');
                if (scroller) scroller.addEventListener('scroll', hideTip);
            })();

            // Pop-up info AI (tombol "i")
            (function() {
                const IS_LOGGED_IN = {{ $userEmail ? 'true' : 'false' }};

                const overlay = document.getElementById('info-overlay');
                const iconEl = document.getElementById('info-icon');
                const titleEl = document.getElementById('info-title');
                const descEl = document.getElementById('info-desc');
                const guideWrap = document.getElementById('info-guide-wrap');
                const guideEl = document.getElementById('info-guide');
                const openBtn = document.getElementById('info-open-btn');
                let current = null;

                function openInfo(btn) {
                    current = {
                        id: btn.dataset.id,
                        url: btn.dataset.url
                    };
                    iconEl.src = btn.dataset.icon || '';
                    iconEl.alt = btn.dataset.name || '';
                    titleEl.textContent = btn.dataset.name || '';

                    const desc = (btn.dataset.description || '').trim();
                    const guide = (btn.dataset.guide || '').trim();

                    descEl.textContent = desc;
                    descEl.style.display = desc ? '' : 'none';
                    guideEl.textContent = guide;
                    guideWrap.style.display = guide ? '' : 'none';

                    overlay.style.display = 'flex';
                }

                function closeInfo() {
                    overlay.style.display = 'none';
                    current = null;
                }

                document.querySelectorAll('.ai-info-btn').forEach(btn => {
                    btn.addEventListener('click', () => openInfo(btn));
                });

                document.getElementById('info-close-btn').addEventListener('click', closeInfo);

                overlay.addEventListener('click', e => {
                    if (e.target === overlay) closeInfo();
                });

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape' && overlay.style.display === 'flex') closeInfo();
                });

                openBtn.addEventListener('click', () => {
                    if (!current) return;
                    const {
                        id,
                        url
                    } = current;
                    closeInfo();

                    if (IS_LOGGED_IN) {
                        openOrFocusAiTab(id, url); // buka / fokus ke tab AI
                    } else {
                        window.location.href = url; // belum login -> diarahkan ke halaman login
                    }
                });
            })();
        </script>
</body>
</html>
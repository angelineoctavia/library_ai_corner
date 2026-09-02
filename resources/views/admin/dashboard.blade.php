<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AI Corner</title>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: url('/images/mac_background.jpg') no-repeat center center fixed;
            background-size: cover;
            background-color: #1a1a1a;
            color: #fff;
        }

        /* Main Wrapper diturunin sithik biar aman nggak ketiban navbar atas */
        .main-container {
            max-width: 1200px;
            margin: 110px auto 40px auto;
            padding: 0 20px;
        }

        /* Judul Dashboard dengan efek Glow sesuai request */
        h2.title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 25px;
            color: #ffca7a;
            text-shadow:
                0 0 8px rgba(255, 202, 122, 0.9),
                0 0 18px rgba(255, 177, 66, 0.8);
        }

        /* Filter & Export Section */
        .filter-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .input-group-date {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(40, 40, 40, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 6px 12px;
        }

        .input-group-date label {
            font-size: 13px;
            color: #bbb;
            font-weight: 500;
        }

        .filter-section input[type="date"] {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 14px;
            outline: none;
            color-scheme: dark;
            cursor: pointer;
        }

        /* Tombol Filter Solid & Jelas */
        .btn-filter {
            background: #f27d00;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 10px rgba(242, 125, 0, 0.3);
            transition: background 0.2s, transform 0.1s;
        }

        .btn-filter:hover {
            background: #d96f00;
        }

        .btn-filter:active {
            transform: scale(0.98);
        }

        /* Tombol Reset Filter */
        .btn-reset {
            background: rgba(255, 255, 255, 0.15);
            color: #ddd;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-reset:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        .btn-download {
            background: #27ae60;
            color: #fff;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(39, 174, 96, 0.3);
            transition: opacity 0.2s;
            margin-left: auto;
        }

        .btn-download:hover {
            opacity: 0.85;
        }

        /* KPI Cards Grid — 3 kolom (logic dari code kedua) */
        .kpi-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .kpi-card {
            background: rgba(255, 255, 255, 0.85);
            color: #333;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .kpi-card h4 {
            font-size: 12px;
            color: #666;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }

        .kpi-card h2 {
            font-size: 20px;
            color: #f27d00;
            margin: 0;
            font-weight: 700;
        }

        /* Analytics Section Layout */
        .analytics-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr 0.8fr;
            gap: 20px;
        }

        .panel-box {
            background: rgba(255, 255, 255, 0.85);
            color: #333;
            border-radius: 14px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .panel-box h4 {
            margin-top: 0;
            font-size: 15px;
            color: #444;
            margin-bottom: 15px;
        }

        /* Table Styling inside panel */
        .table-responsive {
            overflow-x: auto;
            max-height: 250px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table th {
            background: #f27d00;
            color: #fff;
            padding: 8px;
            text-align: left;
            position: sticky;
            top: 0;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            color: #333;
        }
    </style>
</head>

<body>

    <!-- Panggil Navbar Partials -->
    @include('admin.partials.navbar')

    <!-- Main Content -->
    <div class="main-container">
        <h2 class="title">Dashboard</h2>

        <!-- Filter & Excel Download Form (logic pake $startDate / $endDate dari code kedua) -->
        <form method="GET" action="{{ route('admin.dashboard') }}">
            <div class="filter-section">
                <div class="input-group-date">
                    <label for="startDate">Dari</label>
                    <input type="date" id="startDate" name="start_date" value="{{ $startDate ?? '' }}">
                </div>

                <div class="input-group-date">
                    <label for="endDate">Sampai</label>
                    <input type="date" id="endDate" name="end_date" value="{{ $endDate ?? '' }}">
                </div>

                <button type="submit" class="btn-filter">Terapkan Filter</button>

                @if (!empty($startDate) || !empty($endDate))
                    <a href="{{ route('admin.dashboard') }}" class="btn-reset">Reset</a>
                @endif

                <a href="{{ route('admin.dashboard.export', ['start_date' => $startDate ?? '', 'end_date' => $endDate ?? '']) }}"
                    class="btn-download">
                    Download Laporan (.xlsx) &darr;
                </a>
            </div>
        </form>

        @if ($errors->has('export_error'))
            <div style="color: #ff6b6b; margin-bottom: 15px; font-weight: bold;">
                {{ $errors->first('export_error') }}
            </div>
        @endif

        <!-- 3 KPI Cards (logic dari code kedua) -->
        <div class="kpi-container">
            <div class="kpi-card">
                <h4>AI Terpopuler Hari Ini</h4>
                <h2>{{ $aiPopulerHariIni }}</h2>
            </div>
            <div class="kpi-card">
                <h4>AI Paling Favorit</h4>
                <h2>{{ $aiFavorit }}</h2>
            </div>
            <div class="kpi-card">
                <h4>Jurusan Dengan Pengguna Terbanyak</h4>
                <h2>{{ $jurusanTerbanyak }}</h2>
            </div>
        </div>

        <!-- Charts & Table Grid -->
        <div class="analytics-grid">

            <!-- Tren Jam Sibuk (Line Chart) -->
            <div class="panel-box">
                <h4>Tren Jam Sibuk</h4>
                <div style="position: relative; flex-grow: 1; min-height: 200px;">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>

            <!-- Riwayat Akses (Table) -->
            <div class="panel-box">
                <h4>Riwayat Akses</h4>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Jurusan</th>
                                <th>Jam Akses</th>
                                <th>Jenis AI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $row)
                                <tr>
                                    <td>{{ $row->users_nim }}</td>
                                    <td>{{ $row->users_major }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('H:i') }}</td>
                                    <td>{{ $row->ai_tool_name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #777;">Belum ada riwayat akses.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Proporsi Penggunaan AI (Pie Chart) -->
            <div class="panel-box">
                <h4>Proporsi Penggunaan AI</h4>
                <div
                    style="position: relative; flex-grow: 1; min-height: 200px; display: flex; justify-content: center; align-items: center;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js & Validation Script -->
    <script>
        const chartJamData = @json($chartJam);
        const pieLabelsData = @json($pieLabels);
        const pieValuesData = @json($pieValues);

        // Line Chart (Tren Jam Sibuk)
        const ctxLine = document.getElementById('lineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00',
                    '18:00', '19:00', '20:00'
                ],
                datasets: [{
                    label: 'Jumlah Klik AI',
                    data: chartJamData,
                    borderColor: '#f27d00',
                    backgroundColor: 'rgba(242, 125, 0, 0.2)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Pie Chart (Proporsi Penggunaan AI)
        const ctxPie = document.getElementById('pieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: pieLabelsData.length > 0 ? pieLabelsData : ['Tidak Ada Data'],
                datasets: [{
                    data: pieValuesData.length > 0 ? pieValuesData : [1],
                    backgroundColor: ['#F27D00', '#feca57', '#ee5253', '#0abde3', '#10ac84', '#5f27cd',
                        '#c8d6e5'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 1.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });

        // Validasi Tanggal: End Date tidak boleh lebih awal dari Start Date
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');

        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = '';
            }
        });

        if (startDateInput.value) {
            endDateInput.min = startDateInput.value;
        }
    </script>
</body>
</html>
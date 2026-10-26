<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AI Corner</title>
    <!-- Chart.js + plugin datalabels -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js">
    </script>
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

        .main-container {
            max-width: 1440px;
            margin: 95px auto 20px auto;
            padding: 0 20px;
        }

        /* Baris atas: judul + filter tanggal GLOBAL (mengubah semua widget) */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        h2.title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #ffca7a;
            text-shadow: 0 0 8px rgba(255, 202, 122, 0.9), 0 0 18px rgba(255, 177, 66, 0.8);
        }

        .toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .date-chip {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 5px 10px;
            font-size: 12px;
            color: #555;
        }

        .date-chip input[type="date"] {
            border: none;
            outline: none;
            font-size: 12px;
            color: #333;
            background: transparent;
            cursor: pointer;
        }

        .btn-clear {
            background: #e9ecef;
            color: #444;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-clear:hover {
            background: #dde1e5;
        }

        .btn-download {
            background: #27ae60;
            color: #fff;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 10px rgba(39, 174, 96, 0.3);
            transition: opacity 0.2s;
        }

        .btn-download:hover {
            opacity: 0.85;
        }

        /* 4 KPI Cards (compact) */
        .kpi-container {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 12px;
        }

        .kpi-card {
            background: rgba(255, 255, 255, 0.85);
            color: #333;
            border-radius: 12px;
            padding: 12px 14px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .kpi-card h4 {
            font-size: 11px;
            color: #666;
            margin: 0 0 6px 0;
            text-transform: uppercase;
        }

        .kpi-card h2 {
            font-size: 22px;
            color: #f27d00;
            margin: 0;
            font-weight: 700;
        }

        .kpi-sub {
            font-size: 11px;
            color: #888;
            margin-top: 3px;
            min-height: 14px;
        }

        .panel-box {
            background: rgba(255, 255, 255, 0.85);
            color: #333;
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .panel-box h4 {
            margin: 0;
            font-size: 14px;
            color: #444;
        }

        .panel-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .record-count {
            font-size: 11px;
            color: #777;
            margin-top: 2px;
        }

        .row-charts {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
            gap: 12px;
            margin-bottom: 12px;
        }

        .row-tables {
            display: grid;
            grid-template-columns: minmax(0, 2.2fr) minmax(0, 1fr);
            gap: 12px;
        }

        .chart-wrap {
            position: relative;
            height: 210px;
            width: 100%;
            margin-top: 6px;
        }

        .table-responsive {
            overflow-x: auto;
            max-height: 235px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }

        table th {
            background: #f27d00;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 2;
            vertical-align: top;
        }

        .th-label {
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .sort-ind {
            font-size: 9px;
        }

        .col-filter {
            width: 100%;
            min-width: 60px;
            margin-top: 4px;
            padding: 3px 5px;
            font-size: 11px;
            border: none;
            border-radius: 4px;
            outline: none;
            color: #333;
            background: #fff;
            box-sizing: border-box;
        }

        .filter-note {
            margin-top: 4px;
            font-size: 10px;
            font-weight: normal;
            opacity: 0.9;
            white-space: nowrap;
        }

        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            color: #333;
        }

        .empty-row {
            text-align: center;
            color: #777;
        }

        /* Panel Feedback */
        .fb-select {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 5px 8px;
            font-size: 11px;
            color: #333;
            outline: none;
            cursor: pointer;
        }

        .nowrap {
            white-space: nowrap;
        }

        .fb-message {
            white-space: pre-wrap;
            word-break: break-word;
        }

        .fb-email {
            font-size: 10px;
            color: #888;
            margin-top: 2px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-ai {
            background: #e3f2fd;
            color: #1565c0;
        }

        .badge-complaint {
            background: #ffebee;
            color: #c62828;
        }

        @media (max-width: 1100px) {
            .kpi-container {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .row-charts,
            .row-tables {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    @include('admin.partials.navbar')

    <div class="main-container">

        <!-- Judul + filter tanggal GLOBAL: mengubah KPI, chart, tabel, feedback & hasil download -->
        <div class="top-bar">
            <h2 class="title">Dashboard</h2>

            <div class="toolbar">
                <div class="date-chip">
                    <label for="startDate">From</label>
                    <input type="date" id="startDate">
                </div>
                <div class="date-chip">
                    <label for="endDate">To</label>
                    <input type="date" id="endDate">
                </div>
                <button type="button" class="btn-clear" id="resetBtn">Reset</button>
                <a href="#" id="downloadBtn" class="btn-download">Download Report (.xlsx) &darr;</a>
            </div>
        </div>

        @if ($errors->has('export_error'))
            <div style="color: #ff6b6b; margin-bottom: 10px; font-weight: bold;">
                {{ $errors->first('export_error') }}
            </div>
        @endif

        <!-- KPI -->
        <div class="kpi-container">
            <div class="kpi-card">
                <h4>Login Sessions</h4>
                <h2 id="kpiTotalSessions">0</h2>
                <div class="kpi-sub">sessions with at least 1 AI access</div>
            </div>
            <div class="kpi-card">
                <h4>Unique Students</h4>
                <h2 id="kpiStudents">0</h2>
                <div class="kpi-sub" id="kpiStudentsSub"></div>
            </div>
            <div class="kpi-card">
                <h4>Unique Staff</h4>
                <h2 id="kpiStaff">0</h2>
                <div class="kpi-sub" id="kpiStaffSub"></div>
            </div>
            <div class="kpi-card">
                <h4>Avg AI per Session</h4>
                <h2 id="kpiAvg">-</h2>
                <div class="kpi-sub">engagement per login</div>
            </div>
        </div>

        <!-- Baris chart: tren harian + proporsi AI -->
        <div class="row-charts">
            <div class="panel-box">
                <h4>Daily Usage</h4>
                <div class="chart-wrap" id="dailyWrap">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>

            <div class="panel-box">
                <h4>AI Usage Proportion</h4>
                <div class="chart-wrap" id="aiPieWrap">
                    <canvas id="aiPieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Baris tabel: Access History + User Feedback -->
        <div class="row-tables">
            <div class="panel-box">
                <div class="panel-header">
                    <div>
                        <h4>Access History</h4>
                        <div class="record-count" id="recordCount"></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr id="tableHead"></tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="panel-box">
                <div class="panel-header">
                    <div>
                        <h4>User Feedback</h4>
                        <div class="record-count" id="feedbackCount"></div>
                    </div>

                    <select id="feedbackCategory" class="fb-select">
                        <option value="">All types</option>
                        <option value="ai_request">AI Suggestions</option>
                        <option value="complaint">Complaints &amp; Feedback</option>
                    </select>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody id="feedbackBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const allRows = @json($rows);
        // Feedback dari tabel feedbacks (kalau controller belum mengirim, dianggap kosong)
        const allFeedback = @json($feedbacks ?? []);
        const exportBase = @json(route('admin.dashboard.export'));

        const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));

        const datePart = dt => dt ? dt.slice(0, 10) : '';
        const timePart = dt => dt ? dt.slice(11, 16) : '';
        const pad = n => String(n).padStart(2, '0');
        const fmtDate = d => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

        function fmtSessionTime(dt, refDate) {
            if (!dt) return '-';
            return datePart(dt) === refDate ? timePart(dt) : dt.slice(0, 16);
        }

        const columns = [{
                key: 'email',
                label: 'Email',
                filter: 'text',
                value: r => r.email
            },
            {
                key: 'type',
                label: 'User Type',
                filter: 'select',
                value: r => r.type
            },
            {
                key: 'date',
                label: 'Date',
                filter: null,
                value: r => datePart(r.created_at),
                sort: r => r.created_at
            },
            {
                key: 'start',
                label: 'Session Start',
                filter: 'text',
                value: r => fmtSessionTime(r.login_at, datePart(r.created_at)),
                sort: r => r.login_at || ''
            },
            {
                key: 'end',
                label: 'Session End',
                filter: 'text',
                value: r => fmtSessionTime(r.logout_at, datePart(r.created_at)),
                sort: r => r.logout_at || ''
            },
            {
                key: 'time',
                label: 'AI Opened At',
                filter: 'text',
                value: r => timePart(r.created_at),
                sort: r => r.created_at.slice(11)
            },
            {
                key: 'tool',
                label: 'AI Tool',
                filter: 'select',
                value: r => r.tool
            },
            {
                key: 'ip',
                label: 'IP Address',
                filter: 'text',
                value: r => r.ip || '-'
            },
            {
                key: 'location',
                label: 'Location',
                filter: 'select',
                value: r => r.location || '-'
            },
        ];

        const state = {
            sortKey: 'date',
            sortDir: 'desc',
            start: '',
            end: ''
        };
        const colFilters = {};
        const fbState = {
            category: ''
        };

        const FEEDBACK_LABELS = {
            ai_request: 'AI Suggestion',
            complaint: 'Complaint / Feedback'
        };

        const basePalette = [
            '#F27D00', '#0abde3', '#ee5253', '#5f27cd', '#10ac84', '#feca57',
            '#2e86de', '#ff6b81', '#8395a7', '#01a3a4', '#a55eea', '#576574'
        ];

        function getPieColors(count) {
            const colors = [...basePalette];
            for (let i = basePalette.length; i < count; i++) {
                const hue = (i * 137.5) % 360;
                colors.push(`hsl(${hue}, 65%, 55%)`);
            }
            return colors.slice(0, count);
        }

        // Pie Chart dengan Connector Line (Outside Labels) ala Google Sheets
        function makePie(canvasId) {
            return new Chart(document.getElementById(canvasId).getContext('2d'), {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: 28 // Ruang di pinggir canvas agar label luar tidak terpotong
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            formatter: (value, ctx) => {
                                if (ctx.chart.$empty) return 'No data';
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total ? (value / total * 100).toFixed(1) : '0.0';
                                return `${ctx.chart.data.labels[ctx.dataIndex]}: ${value} (${pct}%)`;
                            },
                            anchor: 'end',
                            align: 'end',
                            offset: 10,
                            color: '#333',
                            font: {
                                weight: 'bold',
                                size: 10
                            },
                            textAlign: 'center',
                            connector: {
                                color: '#888',
                                width: 1.5
                            }
                        }
                    }
                },
                plugins: [
                    ChartDataLabels,
                    {
                        // Garis penghubung (connector line) dari slice ke label luar
                        id: 'pieConnectorLines',
                        afterDraw(chart) {
                            if (chart.$empty) return;
                            const meta = chart.getDatasetMeta(0);

                            const {
                                ctx
                            } = chart;
                            ctx.save();
                            ctx.strokeStyle = '#888';
                            ctx.lineWidth = 1.2;

                            meta.data.forEach(element => {
                                const {
                                    x: startX,
                                    y: startY
                                } = element.tooltipPosition();
                                const model = element.getProps(['startAngle', 'endAngle', 'outerRadius',
                                    'x', 'y'
                                ], true);
                                const angle = model.startAngle + (model.endAngle - model.startAngle) /
                                2;

                                const radius = model.outerRadius + 10;
                                const edgeX = model.x + Math.cos(angle) * radius;
                                const edgeY = model.y + Math.sin(angle) * radius;
                                const extraX = edgeX + (Math.cos(angle) > 0 ? 12 : -12);

                                ctx.beginPath();
                                ctx.moveTo(startX, startY);
                                ctx.lineTo(edgeX, edgeY);
                                ctx.lineTo(extraX, edgeY);
                                ctx.stroke();
                            });
                            ctx.restore();
                        }
                    }
                ]
            });
        }

        // Line chart: pemakaian per hari (AI accesses & login sessions)
        function makeLine(canvasId) {
            return new Chart(document.getElementById(canvasId).getContext('2d'), {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                            label: 'AI accesses',
                            data: [],
                            borderColor: '#f27d00',
                            backgroundColor: 'rgba(242, 125, 0, 0.15)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#f27d00'
                        },
                        {
                            label: 'Login sessions',
                            data: [],
                            borderColor: '#0abde3',
                            backgroundColor: 'rgba(10, 189, 227, 0.10)',
                            fill: false,
                            tension: 0.3,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#0abde3'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                boxWidth: 10,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        datalabels: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                autoSkip: true,
                                maxRotation: 0,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        }

        // Kalau Chart.js / plugin-nya gagal dimuat (internet mati, CDN diblokir),
        // tabel, KPI, dan download tetap jalan; cuma chart yang diganti pesan.
        const chartMissingMsg =
            '<div style="padding:40px 10px;text-align:center;color:#b33;font-size:12px;">' +
            'Chart library failed to load (check the internet connection / CDN access). ' +
            'The tables and report download still work.</div>';

        const lineAvailable = typeof Chart !== 'undefined';
        const pieAvailable = lineAvailable && typeof ChartDataLabels !== 'undefined';

        const dailyChart = lineAvailable ? makeLine('dailyChart') : null;
        const aiPie = pieAvailable ? makePie('aiPieChart') : null;

        if (!lineAvailable) document.getElementById('dailyWrap').innerHTML = chartMissingMsg;
        if (!pieAvailable) document.getElementById('aiPieWrap').innerHTML = chartMissingMsg;

        function setPie(chart, labels, values) {
            if (!chart) return;
            const empty = values.length === 0;
            chart.$empty = empty;
            chart.data.labels = empty ? ['No data'] : labels;
            chart.data.datasets[0].data = empty ? [1] : values;
            chart.data.datasets[0].backgroundColor = empty ? ['#c8d6e5'] : getPieColors(values.length);
            chart.options.plugins.tooltip = {
                enabled: !empty
            };
            chart.update();
        }

        function setLine(chart, series) {
            if (!chart) return;
            chart.data.labels = series.labels;
            chart.data.datasets[0].data = series.accesses;
            chart.data.datasets[1].data = series.sessions;
            chart.update();
        }

        // Hitung per hari: jumlah AI accesses & jumlah login session unik.
        // Hari tanpa aktivitas diisi 0 supaya garisnya jujur (rentang maks. 1 tahun).
        function buildDailySeries(rows) {
            const byDay = new Map();
            rows.forEach(r => {
                const d = datePart(r.created_at);
                if (!byDay.has(d)) byDay.set(d, {
                    accesses: 0,
                    sessions: new Set()
                });
                const e = byDay.get(d);
                e.accesses++;
                if (r.session) e.sessions.add(r.session);
            });

            if (!byDay.size) return {
                labels: [],
                accesses: [],
                sessions: []
            };

            const keys = [...byDay.keys()].sort();
            const first = state.start || keys[0];
            const last = state.end || keys[keys.length - 1];
            let days = keys;

            const spanDays = Math.round((new Date(last + 'T00:00:00') - new Date(first + 'T00:00:00')) / 86400000);
            if (spanDays >= 0 && spanDays <= 366) {
                days = [];
                for (let d = new Date(first + 'T00:00:00'); fmtDate(d) <= last; d.setDate(d.getDate() + 1)) {
                    days.push(fmtDate(d));
                }
            }

            return {
                labels: days,
                accesses: days.map(d => byDay.get(d)?.accesses || 0),
                sessions: days.map(d => byDay.get(d)?.sessions.size || 0)
            };
        }

        function toSeries(map) {
            const e = [...map.entries()].sort((a, b) => b[1] - a[1] || a[0].localeCompare(b[0]));
            return {
                labels: e.map(x => x[0]),
                values: e.map(x => x[1])
            };
        }

        function countBy(rows, fn) {
            const m = new Map();
            rows.forEach(r => m.set(fn(r), (m.get(fn(r)) || 0) + 1));
            return m;
        }

        // Jumlah USER UNIK (email) per tipe: Student / Staff
        function uniqueUsersByType(rows) {
            const sets = new Map();
            rows.forEach(r => {
                if (!sets.has(r.type)) sets.set(r.type, new Set());
                sets.get(r.type).add(r.email);
            });
            return new Map([...sets].map(([k, v]) => [k, v.size]));
        }

        function getFilteredRows() {
            return allRows.filter(r => {
                const d = datePart(r.created_at);
                if (state.start && d < state.start) return false;
                if (state.end && d > state.end) return false;

                for (const col of columns) {
                    const f = colFilters[col.key];
                    if (!f) continue;
                    const v = String(col.value(r));
                    if (col.filter === 'select') {
                        if (v !== f) return false;
                    } else if (!v.toLowerCase().includes(f.toLowerCase())) {
                        return false;
                    }
                }
                return true;
            });
        }

        function compareRows(a, b, col, dir) {
            const get = col.sort || col.value;
            const va = get(a),
                vb = get(b);
            const ea = va === '' || va == null,
                eb = vb === '' || vb == null;
            if (ea && eb) return 0;
            if (ea) return 1;
            if (eb) return -1;
            const c = String(va).localeCompare(String(vb), undefined, {
                numeric: true
            });
            return dir === 'asc' ? c : -c;
        }

        function uniqueValues(fn) {
            return [...new Set(allRows.map(fn))].sort((a, b) => a.localeCompare(b));
        }

        function filterControl(col) {
            if (col.filter === 'text') {
                return `<input type="text" class="col-filter" data-filter="${col.key}" placeholder="Filter...">`;
            }
            if (col.filter === 'select') {
                const opts = uniqueValues(col.value).map(v => `<option value="${esc(v)}">${esc(v)}</option>`).join('');
                return `<select class="col-filter" data-filter="${col.key}"><option value="">All</option>${opts}</select>`;
            }
            return '<div class="filter-note">use From / To</div>';
        }

        function buildHeader() {
            document.getElementById('tableHead').innerHTML = columns.map(col => `
                <th>
                    <div class="th-label" data-sort="${col.key}">${col.label}<span class="sort-ind" id="ind-${col.key}"></span></div>
                    ${filterControl(col)}
                </th>`).join('');
        }

        function updateSortIndicators() {
            columns.forEach(col => {
                const el = document.getElementById('ind-' + col.key);
                el.textContent = state.sortKey === col.key ? (state.sortDir === 'asc' ? '▲' : '▼') : '';
            });
        }

        function renderTable(rows) {
            const col = columns.find(c => c.key === state.sortKey);
            const sorted = [...rows].sort((a, b) => compareRows(a, b, col, state.sortDir));
            const body = document.getElementById('tableBody');

            if (!sorted.length) {
                body.innerHTML = `<tr><td colspan="${columns.length}" class="empty-row">No access records found.</td></tr>`;
                return;
            }
            body.innerHTML = sorted.map(r =>
                `<tr>${columns.map(c => `<td>${esc(c.value(r))}</td>`).join('')}</tr>`
            ).join('');
        }

        // Feedback: ikut rentang tanggal From/To + filter tipe (semua di browser, tanpa query database)
        function getFilteredFeedback() {
            return allFeedback.filter(f => {
                const d = datePart(f.created_at);
                if (state.start && d < state.start) return false;
                if (state.end && d > state.end) return false;
                if (fbState.category && f.category !== fbState.category) return false;
                return true;
            });
        }

        function renderFeedback() {
            const rows = getFilteredFeedback();
            document.getElementById('feedbackCount').textContent =
                `Showing ${rows.length} of ${allFeedback.length} feedback`;

            const body = document.getElementById('feedbackBody');
            if (!rows.length) {
                body.innerHTML = '<tr><td colspan="3" class="empty-row">No feedback yet.</td></tr>';
                return;
            }

            body.innerHTML = rows.map(f => `
                <tr>
                    <td class="nowrap">${esc((f.created_at || '').slice(0, 16))}</td>
                    <td><span class="badge ${f.category === 'ai_request' ? 'badge-ai' : 'badge-complaint'}">${esc(FEEDBACK_LABELS[f.category] || f.category)}</span></td>
                    <td>
                        <div class="fb-message">${esc(f.message)}</div>
                        <div class="fb-email">${esc(f.email)}</div>
                    </td>
                </tr>`).join('');
        }

        function updateExportLink() {
            const p = new URLSearchParams();
            if (state.start) p.set('start_date', state.start);
            if (state.end) p.set('end_date', state.end);
            const qs = p.toString();
            document.getElementById('downloadBtn').href = exportBase + (qs ? '?' + qs : '');
        }

        function refresh() {
            const rows = getFilteredRows();

            renderTable(rows);
            renderFeedback();

            document.getElementById('recordCount').textContent = `Showing ${rows.length} of ${allRows.length} records`;

            // KPI 1: Login Sessions = jumlah sesi login UNIK (1 sesi bisa memakai beberapa AI
            // = beberapa baris, tapi tetap dihitung 1). Sesi tanpa klik AI tidak tercatat di database.
            const sessions = new Set(rows.map(r => r.session).filter(Boolean));
            document.getElementById('kpiTotalSessions').textContent = sessions.size;

            // KPI 2 & 3: Unique Students / Unique Staff (email unik) + persentase dari total user unik
            const studentEmails = new Set();
            const staffEmails = new Set();
            rows.forEach(r => {
                const t = String(r.type || '').toLowerCase();
                if (t === 'student') studentEmails.add(r.email);
                else if (t.startsWith('staff')) staffEmails.add(r.email);
            });
            const totalUsers = studentEmails.size + staffEmails.size;
            const pct = n => totalUsers ? (n / totalUsers * 100).toFixed(1) + '%' : '0%';
            const ofTotal = totalUsers ?
                ` of ${totalUsers} unique user${totalUsers === 1 ? '' : 's'}` : '';

            document.getElementById('kpiStudents').textContent = studentEmails.size;
            document.getElementById('kpiStudentsSub').textContent = totalUsers ? pct(studentEmails.size) + ofTotal : '';
            document.getElementById('kpiStaff').textContent = staffEmails.size;
            document.getElementById('kpiStaffSub').textContent = totalUsers ? pct(staffEmails.size) + ofTotal : '';

            // KPI 4: rata-rata AI yang dibuka per sesi login
            document.getElementById('kpiAvg').textContent =
                sessions.size ? (rows.length / sessions.size).toFixed(1) : '-';

            // Line chart: pemakaian per hari
            setLine(dailyChart, buildDailySeries(rows));

            // Pie: proporsi AI Tool
            const aiSeries = toSeries(countBy(rows, r => r.tool));
            setPie(aiPie, aiSeries.labels, aiSeries.values);

            updateExportLink();
        }

        const startInput = document.getElementById('startDate');
        const endInput = document.getElementById('endDate');

        function onDateChange(changed) {
            if (changed === 'start') {
                endInput.min = startInput.value || '';
                if (endInput.value && startInput.value && endInput.value < startInput.value) {
                    endInput.value = '';
                }
            }
            state.start = startInput.value;
            state.end = endInput.value;
            refresh();
        }
        startInput.addEventListener('change', () => onDateChange('start'));
        endInput.addEventListener('change', () => onDateChange('end'));

        document.getElementById('tableHead').addEventListener('click', e => {
            const label = e.target.closest('.th-label');
            if (!label) return;
            const key = label.dataset.sort;
            if (state.sortKey === key) {
                state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                state.sortKey = key;
                state.sortDir = 'asc';
            }
            updateSortIndicators();
            renderTable(getFilteredRows());
        });

        let debounceTimer;
        document.getElementById('tableHead').addEventListener('input', e => {
            const el = e.target.closest('[data-filter]');
            if (!el) return;
            colFilters[el.dataset.filter] = el.value.trim();
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(refresh, el.tagName === 'SELECT' ? 0 : 150);
        });

        document.getElementById('feedbackCategory').addEventListener('change', e => {
            fbState.category = e.target.value;
            renderFeedback();
        });

        document.getElementById('resetBtn').addEventListener('click', () => {
            startInput.value = '';
            endInput.value = '';
            endInput.min = '';
            state.start = '';
            state.end = '';
            Object.keys(colFilters).forEach(k => colFilters[k] = '');
            document.querySelectorAll('#tableHead [data-filter]').forEach(el => el.value = '');
            fbState.category = '';
            document.getElementById('feedbackCategory').value = '';
            state.sortKey = 'date';
            state.sortDir = 'desc';
            updateSortIndicators();
            refresh();
        });

        buildHeader();
        updateSortIndicators();
        refresh();
    </script>
</body>
</html>
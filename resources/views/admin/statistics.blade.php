@extends('layouts.admin')

@section('title', 'Statistik & Laporan')

@section('content')
    <div class="page-heading page-heading--actions">
        <div>
            <h1>Statistik Kunjungan</h1>
            <p>{{ $periodLabel }}: {{ $startDate->translatedFormat('d F Y') }} sampai {{ $endDate->translatedFormat('d F Y') }}</p>
        </div>

        <a href="{{ route('admin.statistics.print', ['period' => $period]) }}" class="btn-primary" target="_blank" rel="noopener">
            <i class="ph ph-printer"></i> Cetak Laporan
        </a>
    </div>

    <form class="statistics-filter" method="GET" action="{{ route('admin.statistics') }}">
        <label>
            <span class="filter-label">Periode statistik</span>
            <select name="period" onchange="this.form.submit()">
                <option value="last_7_days" @selected($period === 'last_7_days')>7 Hari Terakhir</option>
                <option value="this_month" @selected($period === 'this_month')>Bulan Ini</option>
                <option value="last_month" @selected($period === 'last_month')>Bulan Lalu</option>
            </select>
        </label>
        <noscript><button type="submit" class="btn-primary">Terapkan</button></noscript>
    </form>

    <div class="insight-grid">
        <article class="insight-card">
            <span class="insight-card__label">Total Kunjungan</span>
            <strong class="insight-card__value">{{ number_format($totalGuests) }}</strong>
            <span class="insight-card__note">orang pada periode terpilih</span>
        </article>
        <article class="insight-card">
            <span class="insight-card__label">Rata-rata Harian</span>
            <strong class="insight-card__value">{{ number_format($averagePerDay, 1, ',', '.') }}</strong>
            <span class="insight-card__note">orang per hari kalender</span>
        </article>
        <article class="insight-card">
            <span class="insight-card__label">Hari Tersibuk</span>
            <strong class="insight-card__value insight-card__value--small">{{ $busiestDayLabel }}</strong>
            <span class="insight-card__note">{{ number_format($busiestDayTotal) }} kunjungan</span>
        </article>
    </div>

    <div class="chart-grid">
        <section class="content-card chart-card">
            <div class="content-card-header">
                <div>
                    <h2>Tren Kunjungan Harian</h2>
                    <p class="chart-description">Tinggi batang menunjukkan jumlah tamu pada setiap tanggal. Hari tanpa kunjungan tetap ditampilkan sebagai nol.</p>
                </div>
            </div>
            <div class="chart-canvas-wrap chart-canvas-wrap--wide">
                <canvas id="dailyVisitorChart" aria-label="Grafik batang jumlah kunjungan per hari" role="img"></canvas>
            </div>
        </section>

        <section class="content-card chart-card">
            <div class="content-card-header">
                <div>
                    <h2>Komposisi Jenis Tamu</h2>
                    <p class="chart-description">Perbandingan tamu internal dan eksternal terhadap seluruh kunjungan pada periode yang sama.</p>
                </div>
            </div>
            <div class="chart-canvas-wrap chart-canvas-wrap--donut">
                <canvas id="visitorTypeChart" aria-label="Grafik komposisi tamu internal dan eksternal" role="img"></canvas>
            </div>
            <div class="chart-readable-legend">
                <div>
                    <span class="legend-dot legend-dot--internal"></span>
                    <span>Internal</span>
                    <strong>{{ number_format($internalGuests) }} ({{ number_format($internalPercentage, 1, ',', '.') }}%)</strong>
                </div>
                <div>
                    <span class="legend-dot legend-dot--external"></span>
                    <span>Eksternal</span>
                    <strong>{{ number_format($externalGuests) }} ({{ number_format($externalPercentage, 1, ',', '.') }}%)</strong>
                </div>
            </div>
        </section>
    </div>

    <section class="content-card">
        <div class="content-card-header">
            <div>
                <h2>Rincian Harian</h2>
                <p>Angka tabel sama dengan angka yang dipakai grafik, jadi tidak ada sulap statistik kecil-kecilan.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jumlah Tamu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dailyReport as $row)
                        <tr>
                            <td>{{ $row->fullLabel }}</td>
                            <td><strong>{{ number_format($row->total) }}</strong> orang</td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (() => {
        const dailyCanvas = document.getElementById('dailyVisitorChart');
        const typeCanvas = document.getElementById('visitorTypeChart');
        if (!dailyCanvas || !typeCanvas || typeof Chart === 'undefined') return;

        const dailyLabels = @json($dailyLabels);
        const dailyValues = @json($dailyValues);
        const typeValues = [{{ $internalGuests }}, {{ $externalGuests }}];

        new Chart(dailyCanvas, {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Jumlah tamu',
                    data: dailyValues,
                    backgroundColor: 'rgba(37, 99, 235, 0.72)',
                    borderColor: 'rgb(37, 99, 235)',
                    borderWidth: 1,
                    borderRadius: 6,
                    maxBarThickness: 42,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: (items) => `Tanggal ${items[0].label}`,
                            label: (context) => `${context.parsed.y} orang`,
                        },
                    },
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal kunjungan',
                        },
                        grid: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        title: {
                            display: true,
                            text: 'Jumlah tamu (orang)',
                        },
                    },
                },
            },
        });

        new Chart(typeCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Internal', 'Eksternal'],
                datasets: [{
                    data: typeValues,
                    backgroundColor: ['rgb(16, 185, 129)', 'rgb(245, 158, 11)'],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 5,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '64%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const total = context.dataset.data.reduce((sum, value) => sum + Number(value), 0);
                                const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : '0.0';
                                return `${context.label}: ${context.raw} orang (${percentage}%)`;
                            },
                        },
                    },
                },
            },
        });
    })();
</script>
@endpush

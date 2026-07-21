@extends('layouts.admin')

@section('title', 'Statistik & Laporan')

@section('content')

<div class="card-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
        <h2>Grafik Kunjungan Tamu</h2>
        <div style="display: flex; align-items: center; gap: 8px;">
            <form method="GET" action="{{ route('admin.statistics') }}" style="margin: 0;">
                <select name="chart_period" class="filter-input" onchange="this.form.submit()">
                    <option value="last_7_days" {{ $chartPeriod == 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="this_month" {{ $chartPeriod == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="last_month" {{ $chartPeriod == 'last_month' ? 'selected' : '' }}>Bulan Lalu</option>
                </select>
            </form>
            <a href="{{ route('admin.statistics.print', ['chart_period' => $chartPeriod]) }}"
               target="_blank"
               class="btn-search"
               style="background: #0284c7; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; font-size: 13px;">
                <i class="ph ph-file-pdf"></i> Cetak PDF
            </a>
        </div>
    </div>
    <canvas id="dailyChart" height="80"></canvas>
</div>

<div class="card-panel">
    <h2>Riwayat Kunjungan Harian</h2>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Total Tamu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dailyReport as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('l, d F Y') }}</td>
                        <td><strong>{{ $row->total }} Tamu</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="empty-row">Belum ada data pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: @json($dailyLabels),
            datasets: [{
                label: 'Jumlah Tamu',
                data: @json($dailyValues),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>

@endsection
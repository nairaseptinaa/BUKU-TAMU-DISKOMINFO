<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kunjungan - {{ $periodLabel }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin-report.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-report-enhancements.css') }}">
</head>
<body>
    <div class="print-toolbar no-print">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        <button type="button" class="button-secondary" onclick="window.close()">Tutup</button>
    </div>

    <main class="report-page">
        <header class="report-header">
            <img src="{{ asset('img/Lambang_Kabupaten_Lumajang.png') }}" alt="Logo Kominfo" class="report-logo">
            <div>
                <h1>Laporan Kunjungan Buku Tamu</h1>
                <p>Dinas Komunikasi dan Informatika</p>
            </div>
        </header>

        <section class="report-period">
            <div>
                <span>Periode</span>
                <strong>
                    @if ($period === 'custom')
                        @php $dayCount = $startDate->copy()->startOfDay()->diffInDays($endDate->copy()->startOfDay()) + 1; @endphp
                        {{ $dayCount }} Hari ({{ $startDate->translatedFormat('d M Y') }} – {{ $endDate->translatedFormat('d M Y') }})
                    @else
                        {{ $periodLabel }}
                    @endif
                </strong>
            </div>
            <div>
                <span>Rentang tanggal</span>
                <strong>{{ $startDate->translatedFormat('d F Y') }} – {{ $endDate->translatedFormat('d F Y') }}</strong>
            </div>
            <div>
                <span>Dibuat pada</span>
                <strong>{{ $generatedAt->translatedFormat('d F Y, H:i') }}</strong>
            </div>
        </section>

        <section class="report-summary-grid">
            <article>
                <span>Total Kunjungan</span>
                <strong>{{ number_format($totalGuests) }}</strong>
                <small>orang</small>
            </article>
            <article>
                <span>Rata-rata Harian</span>
                <strong>{{ number_format($averagePerDay, 1, ',', '.') }}</strong>
                <small>orang per hari</small>
            </article>
            <article>
                <span>Tamu Internal</span>
                <strong>{{ number_format($internalGuests) }}</strong>
                <small>{{ number_format($internalPercentage, 1, ',', '.') }}% dari total</small>
            </article>
            <article>
                <span>Tamu Eksternal</span>
                <strong>{{ number_format($externalGuests) }}</strong>
                <small>{{ number_format($externalPercentage, 1, ',', '.') }}% dari total</small>
            </article>
        </section>

        <section class="report-chart-panel">
            <div class="report-section-heading">
                <h2>Komposisi Jenis Tamu</h2>
                <p>Panjang batang adalah persentase masing-masing jenis tamu terhadap total kunjungan periode.</p>
            </div>

            <div class="report-bar-chart" role="img" aria-label="Perbandingan persentase tamu internal dan eksternal">
                <div class="report-bar-row">
                    <div class="report-bar-label">Internal</div>
                    <div class="report-bar-track">
                        <div class="report-bar-fill report-bar-fill--internal" style="width: {{ $internalPercentage }}%"></div>
                    </div>
                    <div class="report-bar-value">{{ number_format($internalGuests) }} orang · {{ number_format($internalPercentage, 1, ',', '.') }}%</div>
                </div>
                <div class="report-bar-row">
                    <div class="report-bar-label">Eksternal</div>
                    <div class="report-bar-track">
                        <div class="report-bar-fill report-bar-fill--external" style="width: {{ $externalPercentage }}%"></div>
                    </div>
                    <div class="report-bar-value">{{ number_format($externalGuests) }} orang · {{ number_format($externalPercentage, 1, ',', '.') }}%</div>
                </div>
            </div>
        </section>

        <section class="report-chart-panel report-chart-panel--daily">
            <div class="report-section-heading">
                <h2>Tren Kunjungan Harian</h2>
                <p>Jumlah tamu per tanggal. Nilai nol tetap dicatat agar periode tidak terlihat menyesatkan.</p>
            </div>

            @php
                $maximumDailyValue = max(1, (int) $dailyReportChronological->max('total'));
            @endphp
            <div class="daily-mini-chart">
                @foreach ($dailyReportChronological as $row)
                    @php
                        $barWidth = ($row->total / $maximumDailyValue) * 100;
                    @endphp
                    <div class="daily-mini-row">
                        <span class="daily-mini-date">{{ $row->shortLabel }}</span>
                        <span class="daily-mini-track">
                            <span class="daily-mini-fill" style="width: {{ $barWidth }}%"></span>
                        </span>
                        <strong>{{ number_format($row->total) }}</strong>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="report-table-section">
            <div class="report-section-heading">
                <h2>Rincian Data Tamu</h2>
                <p>Daftar diurutkan dari kunjungan terbaru.</p>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>PD/Unit atau Instansi</th>
                        <th>Layanan</th>
                        <th>Kontak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($guests as $guest)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $guest->visit_date?->translatedFormat('d/m/Y H:i') ?? '-' }}</td>
                            <td>
                                {{ $guest->name }}
                                @if ($guest->position)
                                    <small>{{ $guest->position }}</small>
                                @endif
                            </td>
                            <td>{{ ucfirst($guest->visitor_type) }}</td>
                            <td>
                                @if ($guest->visitor_type === 'internal')
                                    {{ $guest->department?->department_name ?? '-' }}
                                @else
                                    {{ $guest->external_agency ?: '-' }}
                                @endif
                            </td>
                            <td>{{ $guest->serviceType?->service_name ?? '-' }}</td>
                            <td>{{ $guest->phone_number ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="report-empty">Tidak ada data kunjungan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <footer class="report-footer">
            <p>Dokumen dibuat otomatis dari Sistem Buku Tamu Diskominfo.</p>
            <p>Total data: {{ number_format($guests->count()) }} kunjungan.</p>
        </footer>
    </main>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Buku Tamu - Diskominfo Lumajang</title>
  <link rel="stylesheet" href="{{ asset('css/admin-report.css') }}">
</head>
<body>

  {{-- HEADER LAPORAN --}}
  <div class="report-header">
    <div class="report-logo-text">Pemerintah Kabupaten Lumajang</div>
    <div class="report-logo-text" style="font-size: 18px;">Dinas Komunikasi dan Informatika</div>
    <div class="report-title">Laporan Kunjungan Buku Tamu Digital</div>
    <p class="report-meta">Periode: <strong>{{ $labelPeriod }}</strong> | Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
  </div>

  {{-- RINGKASAN --}}
  <div class="summary-grid">
    <div class="summary-box">
      <div class="summary-label">Total Tamu Periode Ini</div>
      <div class="summary-value">{{ $totalPeriod }} Orang</div>
    </div>
    <div class="summary-box">
      <div class="summary-label">Kunjungan Internal</div>
      <div class="summary-value">{{ $totalInternal }} Kunjungan</div>
    </div>
    <div class="summary-box">
      <div class="summary-label">Kunjungan Eksternal</div>
      <div class="summary-value">{{ $totalExternal }} Kunjungan</div>
    </div>
  </div>

  {{-- TABEL DATA --}}
  <div class="section-title">Rincian Data Kunjungan Tamu</div>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Waktu Kunjungan</th>
        <th>Nama Tamu</th>
        <th>Jabatan</th>
        <th>No. WhatsApp</th>
        <th>Tipe</th>
        <th>Asal PD / Instansi</th>
        <th>Tujuan Kunjungan</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($guests as $index => $guest)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ \Carbon\Carbon::parse($guest->visit_date)->format('d-m-Y H:i') }}</td>
          <td><strong>{{ $guest->name }}</strong></td>
          <td>{{ $guest->position ?? '-' }}</td>
          <td>{{ $guest->phone_number }}</td>
          <td>
            @if ($guest->visitor_type == 'internal')
              <span class="badge badge-internal">Internal</span>
            @else
              <span class="badge badge-external">Eksternal</span>
            @endif
          </td>
          <td>
            {{ $guest->visitor_type == 'internal'
              ? ($guest->department->department_name ?? '-')
              : ($guest->external_agency ?? '-') }}
          </td>
          <td>{{ $guest->serviceType->service_name ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="empty-cell">Tidak ada data kunjungan tamu.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- FOOTER --}}
  <div class="report-footer">
    <div class="footer-note">
      *Laporan ini dihasilkan secara otomatis oleh Sistem Buku Tamu Digital<br>
      Dinas Komunikasi dan Informatika Kabupaten Lumajang.
    </div>
    <div class="signature-block">
      <p class="signature-label">
        Lumajang, {{ now()->translatedFormat('d F Y') }}<br>
        Petugas Pelayanan Publik
      </p>
      <div class="signature-line">Tanda Tangan &amp; Nama Terang</div>
    </div>
  </div>

  {{-- Otomatis buka dialog cetak/simpan PDF saat halaman termuat --}}
  <script>
    window.onload = function () {
      window.print();
    };
  </script>

</body>
</html>

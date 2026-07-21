<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buku Tamu - Diskominfo Lumajang</title>
    <link rel="stylesheet" href="{{ asset('css/guestbook.css') }}">
</head>
<body>
    <div class="container">
        <div class="panel-left">
            <div class="logo-row">
                <img src="{{ asset('img/Lambang_Kabupaten_Lumajang.png') }}" alt="Logo Kominfo">
                <span>PEMKAB LUMAJANG<br>DISKOMINFO</span>
            </div>
            <h1>Ruang Pelayanan Publik</h1>
            <p>Silakan isi buku tamu digital ini sebelum mendapatkan layanan dari petugas kami. Data Anda terjamin keamanannya.</p>
            <br>
            <div class="clock-box">
                <div class="label">WAKTU SAAT INI</div>
                <div class="time" id="live-clock">00:00:00</div>
                <div class="date" id="live-date">-</div>
            </div>
        </div>

        <div class="panel-right">
            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('guestbook.store') }}">
                @csrf

                <label for="name">Nama Lengkap</label>
                <div class="input-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
                    <input type="text" name="name" id="name" placeholder="Cth: Budi Santoso" value="{{ old('name') }}" required>
                </div>

                <label for="position">Jabatan</label>
                <div class="input-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    <input type="text" name="position" id="position" placeholder="Cth: Staff Administrasi" value="{{ old('position') }}" required>
                </div>

                <label for="phone_number">No. WhatsApp / HP</label>
                <div class="input-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 5c0 9 7 16 16 16l3-4-6-3-2 2c-3-1-5-3-6-6l2-2-3-6z"/></svg>
                    <input type="tel" name="phone_number" id="phone_number" placeholder="Opsional bisa di isi bisa tidak" value="{{ old('phone_number') }}">
                </div>

                <label>Tipe Kunjungan</label>
               <div class="toggle-row">
                <div class="toggle-card" id="card-internal" onclick="setVisitorType('internal')">
                    <div class="icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="3" width="16" height="18" rx="1"/><path d="M9 8h1M14 8h1M9 12h1M14 12h1M9 16h1M14 16h1"/></svg>
                    </div>
                        <div class="title">Internal Pemkab</div>
                        <div class="subtitle">Dinas/Badan/Bagian</div>
                    </div>
                    <div class="toggle-card" id="card-external" onclick="setVisitorType('external')">
                        <div class="icon-wrapper">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18z"/></svg>
                        </div>
                        <div class="title">Eksternal / Umum</div>
                        <div class="subtitle">Swasta/Masyarakat</div>
                    </div>
                </div>
                <input type="hidden" name="visitor_type" id="visitor_type" value="{{ old('visitor_type') }}" required>

                <div id="field-department" class="hidden">
                    <label for="department_id">Pilih Perangkat Daerah (PD)</label>
                    <select name="department_id" id="department_id">
                        <option value="">-- Silakan Pilih PD/Unit Kerja --</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="field-external" class="hidden">
                    <label for="external_agency">Nama Instansi</label>
                    <div class="input-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="3" width="16" height="18" rx="1"/><path d="M9 8h1M14 8h1M9 12h1M14 12h1M9 16h1M14 16h1"/></svg>
                        <input type="text" name="external_agency" id="external_agency" placeholder="Nama instansi/perusahaan" value="{{ old('external_agency') }}">
                    </div>
                </div>

                <label for="service_type_id">Tujuan Kunjungan</label>
                <select name="service_type_id" id="service_type_id" required>
                    <option value="">-- Pilih Layanan / Tujuan --</option>
                    @foreach ($serviceTypes as $serviceType)
                        <option value="{{ $serviceType->id }}" {{ old('service_type_id') == $serviceType->id ? 'selected' : '' }}>
                            {{ $serviceType->service_name }}
                        </option>
                    @endforeach
                </select>

                <label for="feedback">Kritik dan Saran</label>
                <textarea name="feedback" id="feedback" placeholder="Kritik dan saran untuk pelayanan kami">{{ old('feedback') }}</textarea>

                <button type="submit" class="submit-btn">Konfirmasi Kehadiran</button>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/guestbook.js') }}"></script>
</body>
</html>
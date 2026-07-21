@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Dashboard Buku Tamu</h1>
            <p>Ringkasan dan pemetaan data kunjungan.</p>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-card-icon icon-blue"><i class="ph ph-calendar-check"></i></div>
            <div>
                <span class="stat-card-label">Tamu Hari Ini</span>
                <strong class="stat-card-value">{{ number_format($todayGuests) }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon icon-green"><i class="ph ph-calendar-dots"></i></div>
            <div>
                <span class="stat-card-label">Tamu Bulan Ini</span>
                <strong class="stat-card-value">{{ number_format($monthlyGuests) }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon icon-orange"><i class="ph ph-buildings"></i></div>
            <div>
                <span class="stat-card-label">Internal Bulan Ini</span>
                <strong class="stat-card-value">{{ number_format($internalGuests) }}</strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon icon-purple"><i class="ph ph-users-three"></i></div>
            <div>
                <span class="stat-card-label">Eksternal Bulan Ini</span>
                <strong class="stat-card-value">{{ number_format($externalGuests) }}</strong>
            </div>
        </div>
    </div>

    <section class="content-card">
        <div class="content-card-header content-card-header--stackable">
            <div>
                <h2>Data Kunjungan</h2>
                <p>Pencarian berjalan otomatis setelah Anda berhenti mengetik.</p>
            </div>
        </div>

        <form id="guestFilterForm" class="dashboard-filter" method="GET" action="{{ route('admin.dashboard') }}">
            <label class="filter-field filter-field--search">
                <span class="filter-label">Cari tamu</span>
                <span class="input-with-icon">
                    <i class="ph ph-magnifying-glass"></i>
                    <input
                        id="searchInput"
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Nama, nomor telepon, instansi, unit kerja..."
                        autocomplete="off"
                    >
                </span>
            </label>

            <label class="filter-field">
                <span class="filter-label">Jenis tamu</span>
                <select name="visitor_type" id="visitorTypeFilter">
                    <option value="" @selected($visitorType === '')>Semua Jenis</option>
                    <option value="internal" @selected($visitorType === 'internal')>Internal</option>
                    <option value="external" @selected($visitorType === 'external')>Eksternal</option>
                </select>
            </label>

            <label class="filter-field">
                <span class="filter-label">Pemetaan waktu</span>
                <select name="period" id="periodFilter">
                    <option value="last_7_days" @selected($period === 'last_7_days')>7 Hari Terakhir</option>
                    <option value="last_1_month" @selected($period === 'last_1_month')>1 Bulan Terakhir</option>
                    <option value="all" @selected($period === 'all')>Semua Tamu</option>
                </select>
            </label>

            <button type="submit" class="btn-primary filter-submit">
                <i class="ph ph-funnel"></i> Terapkan
            </button>
        </form>

        <div id="guestResults" class="ajax-results">
            @include('admin.partials.guest-table', ['guests' => $guests])
        </div>
    </section>
@endsection

@push('scripts')
<script>
    (() => {
        const form = document.getElementById('guestFilterForm');
        const results = document.getElementById('guestResults');
        const searchInput = document.getElementById('searchInput');
        const visitorType = document.getElementById('visitorTypeFilter');
        const period = document.getElementById('periodFilter');

        if (!form || !results) return;

        let debounceTimer;
        let activeRequest;

        const loadGuests = async (url = null) => {
            activeRequest?.abort();
            activeRequest = new AbortController();

            const targetUrl = url || `${form.action}?${new URLSearchParams(new FormData(form)).toString()}`;
            results.setAttribute('aria-busy', 'true');

            try {
                const response = await fetch(targetUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: activeRequest.signal,
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();
                results.innerHTML = data.html;
                window.history.replaceState({}, '', targetUrl);
            } catch (error) {
                if (error.name !== 'AbortError') {
                    results.innerHTML = `
                        <div class="alert alert-error">
                            Data gagal dimuat. Muat ulang halaman untuk mencoba kembali.
                        </div>
                    `;
                }
            } finally {
                results.removeAttribute('aria-busy');
            }
        };

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            loadGuests();
        });

        searchInput?.addEventListener('input', () => {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(() => loadGuests(), 350);
        });

        visitorType?.addEventListener('change', () => loadGuests());
        period?.addEventListener('change', () => loadGuests());

        results.addEventListener('click', (event) => {
            const paginationLink = event.target.closest('a[href*="page="]');
            if (!paginationLink) return;

            event.preventDefault();
            loadGuests(paginationLink.href);
        });
    })();
</script>
@endpush

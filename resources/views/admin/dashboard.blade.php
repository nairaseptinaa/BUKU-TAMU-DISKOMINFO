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
            <div class="stat-card-icon icon-blue">
                <i class="ph ph-calendar-check"></i>
            </div>

            <div>
                <span class="stat-card-label">
                    Tamu Hari Ini
                </span>

                <strong class="stat-card-value">
                    {{ number_format($todayGuests) }}
                </strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon icon-green">
                <i class="ph ph-calendar-dots"></i>
            </div>

            <div>
                <span class="stat-card-label">
                    Tamu Bulan Ini
                </span>

                <strong class="stat-card-value">
                    {{ number_format($monthlyGuests) }}
                </strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon icon-orange">
                <i class="ph ph-buildings"></i>
            </div>

            <div>
                <span class="stat-card-label">
                    Internal Bulan Ini
                </span>

                <strong class="stat-card-value">
                    {{ number_format($internalGuests) }}
                </strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon icon-purple">
                <i class="ph ph-users-three"></i>
            </div>

            <div>
                <span class="stat-card-label">
                    Eksternal Bulan Ini
                </span>

                <strong class="stat-card-value">
                    {{ number_format($externalGuests) }}
                </strong>
            </div>
        </div>
    </div>

    <section class="content-card">
        <div class="content-card-header content-card-header--stackable">
            <div>
                <h2>Data Kunjungan</h2>

                <p>
                    Pencarian berjalan otomatis setelah Anda berhenti mengetik.
                </p>
            </div>
        </div>

        <form
            id="guestFilterForm"
            class="dashboard-filter"
            method="GET"
            action="{{ route('admin.dashboard') }}"
        >
            <label class="filter-field filter-field--search">
                <span class="filter-label">
                    Cari tamu
                </span>

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
                <span class="filter-label">
                    Jenis tamu
                </span>

                <select
                    name="visitor_type"
                    id="visitorTypeFilter"
                >
                    <option
                        value=""
                        @selected($visitorType === '')
                    >
                        Semua Jenis
                    </option>

                    <option
                        value="internal"
                        @selected($visitorType === 'internal')
                    >
                        Internal
                    </option>

                    <option
                        value="external"
                        @selected($visitorType === 'external')
                    >
                        Eksternal
                    </option>
                </select>
            </label>

            <label class="filter-field">
                <span class="filter-label">
                    Pemetaan waktu
                </span>

                <select
                    name="period"
                    id="periodFilter"
                >
                    <option
                        value="last_7_days"
                        @selected($period === 'last_7_days')
                    >
                        7 Hari Terakhir
                    </option>

                    <option
                        value="last_1_month"
                        @selected($period === 'last_1_month')
                    >
                        1 Bulan Terakhir
                    </option>

                    <option
                        value="all"
                        @selected($period === 'all')
                    >
                        Semua Tamu
                    </option>
                </select>
            </label>

            <button
                type="submit"
                class="btn-primary filter-submit"
            >
                <i class="ph ph-funnel"></i>
                Terapkan
            </button>
        </form>

        <div
            id="guestResults"
            class="ajax-results"
        >
            @include('admin.partials.guest-table', [
                'guests' => $guests,
            ])
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const form = document.getElementById('guestFilterForm');
            const results = document.getElementById('guestResults');
            const searchInput = document.getElementById('searchInput');
            const visitorTypeFilter = document.getElementById(
                'visitorTypeFilter'
            );
            const periodFilter = document.getElementById(
                'periodFilter'
            );

            if (!form || !results) {
                return;
            }

            let debounceTimer = null;
            let activeRequest = null;

            /**
             * Membuat URL berdasarkan filter.
             * Parameter page tidak disertakan agar filter baru
             * selalu dimulai dari halaman pertama.
             */
            const buildFilterUrl = () => {
                const url = new URL(
                    form.action,
                    window.location.origin
                );

                const formData = new FormData(form);
                const params = new URLSearchParams(formData);

                params.delete('page');

                params.forEach((value, key) => {
                    const cleanValue = value.trim();

                    if (cleanValue !== '') {
                        url.searchParams.set(key, cleanValue);
                    }
                });

                return url.toString();
            };

            /**
             * Memuat data tamu melalui AJAX.
             */
            const loadGuests = async (url = null) => {
                if (activeRequest) {
                    activeRequest.abort();
                }

                activeRequest = new AbortController();

                const targetUrl = url || buildFilterUrl();

                results.setAttribute('aria-busy', 'true');
                results.classList.add('ajax-results--loading');

                try {
                    const response = await fetch(targetUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        signal: activeRequest.signal,
                    });

                    if (!response.ok) {
                        throw new Error(
                            `Permintaan gagal dengan status ${response.status}`
                        );
                    }

                    const data = await response.json();

                    if (typeof data.html !== 'string') {
                        throw new Error(
                            'Format respons server tidak sesuai.'
                        );
                    }

                    results.innerHTML = data.html;

                    window.history.replaceState(
                        {},
                        '',
                        targetUrl
                    );
                } catch (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    console.error(error);

                    results.innerHTML = `
                        <div class="alert alert-error">
                            Data gagal dimuat. Silakan muat ulang halaman
                            dan coba kembali.
                        </div>
                    `;
                } finally {
                    results.removeAttribute('aria-busy');
                    results.classList.remove(
                        'ajax-results--loading'
                    );
                }
            };

            /**
             * Submit filter.
             */
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                loadGuests();
            });

            /**
             * Pencarian otomatis dengan debounce.
             */
            searchInput?.addEventListener('input', () => {
                window.clearTimeout(debounceTimer);

                debounceTimer = window.setTimeout(() => {
                    loadGuests();
                }, 400);
            });

            /**
             * Filter jenis tamu.
             */
            visitorTypeFilter?.addEventListener(
                'change',
                () => {
                    loadGuests();
                }
            );

            /**
             * Filter periode.
             */
            periodFilter?.addEventListener(
                'change',
                () => {
                    loadGuests();
                }
            );

            /**
             * Pagination AJAX.
             */
            results.addEventListener('click', (event) => {
                const paginationLink = event.target.closest(
                    'a.pagination-link'
                );

                if (!paginationLink) {
                    return;
                }

                event.preventDefault();

                loadGuests(paginationLink.href);
            });

            /**
             * Mendukung tombol kembali dan maju browser.
             */
            window.addEventListener('popstate', () => {
                loadGuests(window.location.href);
            });
        })();
    </script>
@endpush

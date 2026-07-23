<div class="results-meta" aria-live="polite">
    <span>
        <strong>{{ number_format($guests->total()) }}</strong>
        data tamu ditemukan
    </span>

    @if ($guests->total() > 0)
        <span>
            Menampilkan
            <strong>{{ $guests->firstItem() }}</strong>–<strong>{{ $guests->lastItem() }}</strong>
            dari
            <strong>{{ number_format($guests->total()) }}</strong>
            data
        </span>
    @endif
</div>

<div class="table-responsive">
    <table class="admin-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Tamu</th>
                <th>Kontak</th>
                <th>Jenis Tamu</th>
                <th>PD/Unit Kerja</th>
                <th>Jenis Layanan</th>
                <th>Tanggal Kunjungan</th>
                <th>Kritik dan Saran</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($guests as $guest)
                <tr>
                    <td>
                        {{ ($guests->firstItem() ?? 1) + $loop->index }}
                    </td>

                    <td>
                        <strong>{{ $guest->name }}</strong>

                        @if ($guest->position)
                            <div class="table-subtext">
                                {{ $guest->position }}
                            </div>
                        @endif
                    </td>

                    <td>
                        {{ $guest->phone_number ?: '-' }}
                    </td>

                    <td>
                        <span
                            class="visitor-badge visitor-badge--{{ $guest->visitor_type }}"
                        >
                            {{ ucfirst($guest->visitor_type) }}
                        </span>

                        @if (
                            $guest->visitor_type === 'external'
                            && $guest->external_agency
                        )
                            <div class="table-subtext">
                                {{ $guest->external_agency }}
                            </div>
                        @endif
                    </td>

                    <td>
                        @if ($guest->visitor_type === 'internal')
                            {{ $guest->department?->department_name ?? '-' }}
                        @else
                            {{ $guest->external_agency ?? '-' }}
                        @endif
                    </td>

                    <td>
                        {{ $guest->serviceType?->service_name ?? '-' }}
                    </td>

                    <td>
                        @if ($guest->visit_date)
                            {{ \Carbon\Carbon::parse($guest->visit_date)->translatedFormat('d M Y, H:i') }}
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        @if ($guest->feedback)
                            <span title="{{ $guest->feedback }}">
                                {{ \Illuminate\Support\Str::limit($guest->feedback, 40) }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    <td>
                        <div class="action-buttons">
                            <a
                                href="{{ route('admin.guests.edit', $guest) }}"
                                class="btn-search btn-edit"
                            >
                                <i class="ph ph-pencil"></i>
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="{{ route('admin.guests.destroy', $guest) }}"
                                data-confirm-title="Hapus data tamu?"
                                data-confirm="Data kunjungan “{{ $guest->name }}” akan dihapus permanen. Tindakan ini tidak dapat dibatalkan."
                                data-confirm-button="Ya, Hapus"
                                data-confirm-tone="danger"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn-delete"
                                >
                                    <i class="ph ph-trash"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-state">
                        <i class="ph ph-magnifying-glass"></i>

                        <strong>
                            Data tamu tidak ditemukan
                        </strong>

                        <span>
                            Coba ubah kata pencarian atau filter periode.
                        </span>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($guests->hasPages())
    @php
        $currentPage = $guests->currentPage();
        $lastPage = $guests->lastPage();

        $startPage = max(1, $currentPage - 1);
        $endPage = min($lastPage, $currentPage + 1);
    @endphp

    <div class="compact-pagination">
        <div class="pagination-summary">
            Halaman
            <strong>{{ $currentPage }}</strong>
            dari
            <strong>{{ $lastPage }}</strong>
        </div>

        <nav
            class="pagination-nav"
            aria-label="Navigasi halaman data tamu"
        >
            {{-- Tombol sebelumnya --}}
            @if ($guests->onFirstPage())
                <span
                    class="pagination-link pagination-link--disabled"
                    aria-disabled="true"
                    title="Halaman sebelumnya"
                >
                    <i class="ph ph-caret-left"></i>
                </span>
            @else
                <a
                    href="{{ $guests->previousPageUrl() }}"
                    class="pagination-link"
                    rel="prev"
                    aria-label="Halaman sebelumnya"
                    title="Halaman sebelumnya"
                >
                    <i class="ph ph-caret-left"></i>
                </a>
            @endif

            {{-- Halaman pertama --}}
            @if ($startPage > 1)
                <a
                    href="{{ $guests->url(1) }}"
                    class="pagination-link"
                    aria-label="Halaman 1"
                >
                    1
                </a>

                @if ($startPage > 2)
                    <span class="pagination-dots">
                        …
                    </span>
                @endif
            @endif

            {{-- Halaman di sekitar halaman aktif --}}
            @for ($page = $startPage; $page <= $endPage; $page++)
                @if ($page === $currentPage)
                    <span
                        class="pagination-link pagination-link--active"
                        aria-current="page"
                    >
                        {{ $page }}
                    </span>
                @else
                    <a
                        href="{{ $guests->url($page) }}"
                        class="pagination-link"
                        aria-label="Halaman {{ $page }}"
                    >
                        {{ $page }}
                    </a>
                @endif
            @endfor

            {{-- Halaman terakhir --}}
            @if ($endPage < $lastPage)
                @if ($endPage < $lastPage - 1)
                    <span class="pagination-dots">
                        …
                    </span>
                @endif

                <a
                    href="{{ $guests->url($lastPage) }}"
                    class="pagination-link"
                    aria-label="Halaman {{ $lastPage }}"
                >
                    {{ $lastPage }}
                </a>
            @endif

            {{-- Tombol berikutnya --}}
            @if ($guests->hasMorePages())
                <a
                    href="{{ $guests->nextPageUrl() }}"
                    class="pagination-link"
                    rel="next"
                    aria-label="Halaman berikutnya"
                    title="Halaman berikutnya"
                >
                    <i class="ph ph-caret-right"></i>
                </a>
            @else
                <span
                    class="pagination-link pagination-link--disabled"
                    aria-disabled="true"
                    title="Halaman berikutnya"
                >
                    <i class="ph ph-caret-right"></i>
                </span>
            @endif
        </nav>
    </div>
@endif

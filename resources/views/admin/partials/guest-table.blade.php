<div class="results-meta" aria-live="polite">
    <span><strong>{{ $guests->total() }}</strong> data tamu ditemukan</span>
    @if ($guests->total() > 0)
        <span>Menampilkan {{ $guests->firstItem() }}–{{ $guests->lastItem() }}</span>
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
                <th>Kritik Dan Saran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($guests as $guest)
                <tr>
                    <td>{{ $guests->firstItem() + $loop->index }}</td>
                    <td>
                        <strong>{{ $guest->name }}</strong>
                        @if ($guest->position)
                            <div class="table-subtext">{{ $guest->position }}</div>
                        @endif
                    </td>
                    <td>{{ $guest->phone_number ?: '-' }}</td>
                    <td>
                        <span class="visitor-badge visitor-badge--{{ $guest->visitor_type }}">
                            {{ ucfirst($guest->visitor_type) }}
                        </span>
                        @if ($guest->visitor_type === 'external' && $guest->external_agency)
                            <div class="table-subtext">{{ $guest->external_agency }}</div>
                        @endif
                    </td>
                    <td>{{ $guest->department?->department_name ?? '-' }}</td>
                    <td>{{ $guest->serviceType?->service_name ?? '-' }}</td>
                    <td>{{ $guest->visit_date ? \Carbon\Carbon::parse($guest->visit_date)->translatedFormat('d M Y, H:i') : '-' }}</td>
                    <td>
                        @if ($guest->feedback)
                            <span title="{{ $guest->feedback }}">{{ \Illuminate\Support\Str::limit($guest->feedback, 40) }}</span>
                        @else
                            <span style="color:#94a3b8">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.guests.edit', $guest) }}" class="btn-search btn-edit">
                                <i class="ph ph-pencil"></i> Edit
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
                                <button type="submit" class="btn-delete">
                                    <i class="ph ph-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-state">
                        <i class="ph ph-magnifying-glass"></i>
                        <strong>Data tamu tidak ditemukan</strong>
                        <span>Coba ubah kata pencarian atau filter periode.</span>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($guests->hasPages())
    <div class="pagination-wrapper">
        {{ $guests->links() }}
    </div>
@endif

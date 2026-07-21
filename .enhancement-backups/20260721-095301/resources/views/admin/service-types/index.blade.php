@extends('layouts.admin')

@section('title', 'Kelola Jenis Layanan')

@section('content')
    <section class="card-panel">
        <div class="management-heading">
            <div>
                <h2>Daftar Jenis Layanan</h2>
                <p>Jenis layanan yang masih dipakai pada data kunjungan dilindungi dari penghapusan.</p>
            </div>
            <a href="{{ route('admin.service-types.create') }}" class="btn-search">
                <i class="ph ph-plus"></i> Tambah Baru
            </a>
        </div>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Jenis Layanan</th>
                        <th>Dipakai pada Kunjungan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($serviceTypes as $serviceType)
                        <tr>
                            <td>{{ $serviceTypes->firstItem() + $loop->index }}</td>
                            <td><strong>{{ $serviceType->service_name }}</strong></td>
                            <td>{{ number_format($serviceType->guestbooks_count) }} data</td>
                            <td>
                                <div class="action-buttons">
                                    <a
                                        href="{{ route('admin.service-types.edit', $serviceType) }}"
                                        class="btn-search btn-edit"
                                        data-confirm-title="Buka halaman edit?"
                                        data-confirm="Anda akan mengubah jenis layanan “{{ $serviceType->service_name }}”."
                                        data-confirm-button="Lanjut Edit"
                                        data-confirm-tone="warning"
                                    >
                                        <i class="ph ph-pencil"></i> Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.service-types.destroy', $serviceType) }}"
                                        data-confirm-title="Hapus jenis layanan?"
                                        data-confirm="Data “{{ $serviceType->service_name }}” akan dihapus permanen. Tindakan ini tidak dapat dibatalkan."
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
                            <td colspan="4" class="empty-state">Belum ada data jenis layanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $serviceTypes->links() }}
        </div>
    </section>
@endsection

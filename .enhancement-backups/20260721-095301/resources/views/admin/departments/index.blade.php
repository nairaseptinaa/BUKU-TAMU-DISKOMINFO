@extends('layouts.admin')

@section('title', 'Kelola PD/Unit Kerja')

@section('content')
    <section class="card-panel">
        <div class="management-heading">
            <div>
                <h2>Daftar PD/Unit Kerja</h2>
                <p>Data yang sudah dipakai pada riwayat kunjungan tidak dapat dihapus.</p>
            </div>
            <a href="{{ route('admin.departments.create') }}" class="btn-search">
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
                        <th>Nama PD/Unit Kerja</th>
                        <th>Dipakai pada Kunjungan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $department)
                        <tr>
                            <td>{{ $departments->firstItem() + $loop->index }}</td>
                            <td><strong>{{ $department->department_name }}</strong></td>
                            <td>{{ number_format($department->guestbooks_count) }} data</td>
                            <td>
                                <div class="action-buttons">
                                    <a
                                        href="{{ route('admin.departments.edit', $department) }}"
                                        class="btn-search btn-edit"
                                        data-confirm-title="Buka halaman edit?"
                                        data-confirm="Anda akan mengubah PD/Unit Kerja “{{ $department->department_name }}”."
                                        data-confirm-button="Lanjut Edit"
                                        data-confirm-tone="warning"
                                    >
                                        <i class="ph ph-pencil"></i> Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.departments.destroy', $department) }}"
                                        data-confirm-title="Hapus PD/Unit Kerja?"
                                        data-confirm="Data “{{ $department->department_name }}” akan dihapus permanen. Tindakan ini tidak dapat dibatalkan."
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
                            <td colspan="4" class="empty-state">Belum ada data PD/Unit Kerja.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $departments->links() }}
        </div>
    </section>
@endsection

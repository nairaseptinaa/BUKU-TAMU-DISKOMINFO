@extends('layouts.admin')

@section('title', 'Kelola Jenis Layanan')

@section('content')

<div class="card-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h2>Daftar Jenis Layanan</h2>
        <a href="{{ route('admin.service-types.create') }}" class="btn-search">
            <i class="ph ph-plus"></i> Tambah Baru
        </a>
    </div>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Jenis Layanan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($serviceTypes as $serviceType)
                    <tr>
                        <td>{{ $loop->iteration + ($serviceTypes->currentPage() - 1) * $serviceTypes->perPage() }}</td>
                        <td><strong>{{ $serviceType->service_name }}</strong></td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.service-types.edit', $serviceType) }}" class="btn-search btn-edit">
                                    <i class="ph ph-pencil"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.service-types.destroy', $serviceType) }}" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-search btn-delete">
                                        <i class="ph ph-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-row">Belum ada data Jenis Layanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $serviceTypes->links() }}
    </div>
</div>

@endsection
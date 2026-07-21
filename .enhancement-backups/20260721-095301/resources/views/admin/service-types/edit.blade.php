@extends('layouts.admin')

@section('title', 'Edit Jenis Layanan')

@section('content')
    <div class="card-panel form-narrow">
        <h2>Edit Jenis Layanan</h2>
        <p class="form-description">Periksa kembali nama sebelum menyimpan. Riwayat kunjungan terkait akan memakai nama terbaru.</p>

        @if ($errors->any())
            <div class="error-box">{{ $errors->first('service_name') }}</div>
        @endif

        <form
            method="POST"
            action="{{ route('admin.service-types.update', $serviceType) }}"
            data-confirm-title="Simpan perubahan?"
            data-confirm="Nama jenis layanan akan diubah dari “{{ $serviceType->service_name }}” menjadi nilai yang baru Anda masukkan."
            data-confirm-button="Ya, Simpan"
            data-confirm-tone="warning"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="service_name">Nama Jenis Layanan</label>
                <input
                    type="text"
                    name="service_name"
                    id="service_name"
                    class="input-text"
                    value="{{ old('service_name', $serviceType->service_name) }}"
                    maxlength="255"
                    required
                    autofocus
                >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <i class="ph ph-floppy-disk"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.service-types.index') }}" class="back-link">&larr; Kembali ke Daftar</a>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Tambah Jenis Layanan')

@section('content')

<div class="card-panel form-narrow">
    <h2>Tambah Jenis Layanan Baru</h2>

    @if ($errors->any())
        <div class="error-box">
            {{ $errors->first('service_name') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.service-types.store') }}">
        @csrf
        <div class="form-group">
            <label for="service_name">Nama Jenis Layanan</label>
            <input type="text" name="service_name" id="service_name" class="input-text"
                   placeholder="Cth: Konsultasi" value="{{ old('service_name') }}" required autofocus>
        </div>
        <button type="submit" class="btn-save">
            <i class="ph ph-floppy-disk"></i> Simpan
        </button>
        <a href="{{ route('admin.service-types.index') }}" class="back-link">
            &larr; Kembali ke Daftar
        </a>
    </form>
</div>

@endsection
@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')

<div class="card-panel" style="max-width: 500px;">
  <h2>Pengaturan Link SKM</h2>
  <p class="hint-text">
    Tautan ini digunakan untuk mengalihkan tamu secara otomatis setelah mengisi buku tamu.
  </p>

  @if (session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('admin.update-skm') }}">
    @csrf
    <div class="form-group">
      <label for="skm_redirect_url">URL Redirect SKM</label>
      <input type="url" id="skm_redirect_url" name="skm_redirect_url" class="input-text" value="{{ $skmUrl }}" required>
    </div>
    <button type="submit" class="btn-save">
      <i class="ph ph-floppy-disk"></i> Simpan Tautan
    </button>
  </form>
</div>

@endsection
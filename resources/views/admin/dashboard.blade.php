@extends('layouts.admin')

@section('title', 'Dashboard Admin Buku Tamu')

@section('content')

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon icon-today"><i class="ph ph-users"></i></div>
    <div class="stat-info">
      <div class="label">Tamu Hari Ini</div>
      <div class="number">{{ $totalToday }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon icon-month"><i class="ph ph-calendar-check"></i></div>
    <div class="stat-info">
      <div class="label">Bulan Ini</div>
      <div class="number">{{ $totalMonth }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon icon-internal"><i class="ph ph-buildings"></i></div>
    <div class="stat-info">
      <div class="label">Internal</div>
      <div class="number">{{ $totalInternal }}</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon icon-external"><i class="ph ph-globe"></i></div>
    <div class="stat-info">
      <div class="label">Eksternal</div>
      <div class="number">{{ $totalExternal }}</div>
    </div>
  </div>
</div>

<div class="card-panel">
  <h2>Riwayat Kunjungan Tamu</h2>

  <form method="GET" action="{{ route('admin.dashboard') }}" class="filter-form">
    <input type="text" name="search" placeholder="Cari nama / no. HP..." class="filter-input search-field" value="{{ request('search') }}">
    <select name="visitor_type" class="filter-input select-field" onchange="this.form.submit()">
      <option value="">Semua Tamu</option>
      <option value="internal" {{ request('visitor_type') == 'internal' ? 'selected' : '' }}>Internal</option>
      <option value="external" {{ request('visitor_type') == 'external' ? 'selected' : '' }}>Eksternal</option>
    </select>
    <button type="submit" class="btn-search">Cari</button>
  </form>

  <div class="table-responsive">
    <table class="data-table">
      <thead>
        <tr>
          <th>Waktu</th>
          <th>Nama Tamu</th>
          <th>No. HP</th>
          <th>Tipe</th>
          <th>Asal PD / Instansi</th>
          <th>Tujuan Kunjungan</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($guests as $guest)
          <tr>
            <td>
              <small style="color:#94a3b8">
                {{ \Carbon\Carbon::parse($guest->visit_date)->format('d-m-Y H:i') }}
              </small>
            </td>
            <td><strong>{{ $guest->name }}</strong></td>
            <td>{{ $guest->phone_number }}</td>
            <td>
              @if ($guest->visitor_type == 'internal')
                <span class="badge badge-internal">Internal</span>
              @else
                <span class="badge badge-external">Eksternal</span>
              @endif
            </td>
            <td>
              {{ $guest->visitor_type == 'internal'
                ? ($guest->department->department_name ?? '-')
                : ($guest->external_agency ?? '-') }}
            </td>
            <td>{{ $guest->serviceType->service_name ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="empty-row">
              Belum ada data kunjungan tamu.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="pagination-wrap">
    {{ $guests->appends(request()->query())->links() }}
  </div>
</div>

@endsection
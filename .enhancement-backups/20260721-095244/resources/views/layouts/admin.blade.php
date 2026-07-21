<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title') - Buku Tamu Diskominfo</title>
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

  <div class="admin-layout">

    <aside class="sidebar">
      <div class="sidebar-brand">
          <img src="{{ asset('img/logokominfo.png') }}" alt="Logo Kominfo">
          <div class="sidebar-brand-title">BUKU TAMU</div>
          <div class="sidebar-brand-subtitle">Dinas Komunikasi & Informatika</div>
      </div>

      <div class="sidebar-section-label">MENU</div>
      <nav class="sidebar-menu">
          <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              <span class="menu-icon icon-blue"><i class="ph ph-layout"></i></span>
              Dashboard
          </a>
          <a href="{{ route('admin.departments.index') }}" class="{{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
              <span class="menu-icon icon-green"><i class="ph ph-buildings"></i></span>
              Kelola PD/Unit Kerja
          </a>
          <a href="{{ route('admin.service-types.index') }}" class="{{ request()->routeIs('admin.service-types.*') ? 'active' : '' }}">
              <span class="menu-icon icon-orange"><i class="ph ph-list-checks"></i></span>
              Kelola Jenis Layanan
          </a>
          <a href="{{ route('admin.statistics') }}" class="{{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
              <span class="menu-icon icon-blue"><i class="ph ph-chart-bar"></i></span>
              Statistik & Laporan
          </a>
      </nav>

      <div class="sidebar-section-label sidebar-section-bottom">SISTEM</div>
      <nav class="sidebar-menu">
          <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">
              <span class="menu-icon icon-gray"><i class="ph ph-gear"></i></span>
              Pengaturan
          </a>
      </nav>
    </aside>

    <div class="main-content">

      <nav class="admin-navbar">
        <div class="navbar-brand">
          <i class="ph ph-layout"></i>
          @yield('title')
        </div>
        <div class="navbar-right">
          <span>{{ Auth::user()->name }}</span>
          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
              <i class="ph ph-sign-out"></i> Logout
            </button>
          </form>
        </div>
      </nav>

      <div class="page-content">
        @yield('content')
      </div>

    </div>
  </div>

</body>
</html>
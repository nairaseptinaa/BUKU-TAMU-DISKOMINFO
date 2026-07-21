<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Buku Tamu Diskominfo</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-enhancements.css') }}">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @stack('head')
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
                <a href="{{ route('admin.statistics') }}" class="{{ request()->routeIs('admin.statistics*') ? 'active' : '' }}">
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

    <div id="confirmModal" class="confirm-modal" hidden>
        <div class="confirm-modal__backdrop" data-confirm-cancel></div>
        <section
            class="confirm-modal__dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="confirmModalTitle"
            aria-describedby="confirmModalMessage"
        >
            <div class="confirm-modal__icon" id="confirmModalIcon" aria-hidden="true">
                <i class="ph ph-warning-circle"></i>
            </div>
            <div class="confirm-modal__content">
                <h2 id="confirmModalTitle">Konfirmasi tindakan</h2>
                <p id="confirmModalMessage">Pastikan tindakan ini memang ingin dilanjutkan.</p>
            </div>
            <div class="confirm-modal__actions">
                <button type="button" class="btn-secondary" data-confirm-cancel>Batal</button>
                <button type="button" class="btn-confirm" id="confirmModalButton">Lanjutkan</button>
            </div>
        </section>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('confirmModal');
            const title = document.getElementById('confirmModalTitle');
            const message = document.getElementById('confirmModalMessage');
            const confirmButton = document.getElementById('confirmModalButton');
            const icon = document.getElementById('confirmModalIcon');
            let pendingAction = null;
            let previouslyFocused = null;

            if (!modal || !confirmButton) return;

            const closeModal = () => {
                modal.hidden = true;
                document.body.classList.remove('modal-open');
                pendingAction = null;
                previouslyFocused?.focus?.();
            };

            const openModal = (trigger, action) => {
                previouslyFocused = trigger;
                pendingAction = action;
                title.textContent = trigger.dataset.confirmTitle || 'Konfirmasi tindakan';
                message.textContent = trigger.dataset.confirm || 'Pastikan tindakan ini memang ingin dilanjutkan.';
                confirmButton.textContent = trigger.dataset.confirmButton || 'Lanjutkan';

                const tone = trigger.dataset.confirmTone || 'warning';
                confirmButton.className = `btn-confirm btn-confirm--${tone}`;
                icon.className = `confirm-modal__icon confirm-modal__icon--${tone}`;

                modal.hidden = false;
                document.body.classList.add('modal-open');
                confirmButton.focus();
            };

            document.addEventListener('click', (event) => {
                const link = event.target.closest('a[data-confirm]');
                if (!link) return;

                event.preventDefault();
                openModal(link, () => {
                    window.location.assign(link.href);
                });
            });

            document.addEventListener('submit', (event) => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement) || !form.matches('form[data-confirm]')) return;

                event.preventDefault();
                openModal(form, () => {
                    HTMLFormElement.prototype.submit.call(form);
                });
            });

            document.querySelectorAll('[data-confirm-cancel]').forEach((element) => {
                element.addEventListener('click', closeModal);
            });

            confirmButton.addEventListener('click', () => {
                const action = pendingAction;
                closeModal();
                action?.();
            });

            document.addEventListener('keydown', (event) => {
                if (!modal.hidden && event.key === 'Escape') {
                    closeModal();
                }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>

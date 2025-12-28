<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- [PENTING] Menghilangkan error 419 CSRF Mismatch -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Admin Pusat') - Dashboard Aktivasi</title>

  <!-- CSS -->
  <link rel="shortcut icon" type="image/png" href="{{ asset('template/assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

  <!-- Icon dari Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    /* Global Theme Variables */
    :root {
        --primary-neon: #00f2ff;
        --secondary-glow: #0066ff;
        --bg-deep: #020617;
        --card-surface: rgba(15, 23, 42, 0.7);
        --text-bright: #f8fafc;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: var(--bg-deep);
        color: var(--text-bright);
        margin: 0;
        /* Efek Grid Halus di Latar Belakang */
        background-image:
            radial-gradient(circle at 50% 0%, rgba(0, 102, 255, 0.1) 0%, transparent 50%),
            linear-gradient(rgba(255,255,255,0.01) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.01) 1px, transparent 1px);
        background-size: 100% 100%, 40px 40px, 40px 40px;
    }

    /* CSS Navbar Ikonik agar terlihat menyatu */
    .app-header-cyber {
        background: rgba(2, 6, 23, 0.85) !important;
        backdrop-filter: blur(15px);
        border-bottom: 1px solid rgba(0, 242, 255, 0.2);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    }

    .logo-box-header {
        width: 38px; height: 38px;
        background: linear-gradient(135deg, #0066ff, #00f2ff);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 0 15px rgba(0, 242, 255, 0.4);
    }

    .brand-text-sub {
        color: var(--primary-neon);
        font-size: 9px;
        letter-spacing: 2px;
        text-transform: uppercase;
        font-weight: 700;
        line-height: 1;
    }

    .nav-pill-cyber {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(0, 242, 255, 0.1);
        color: #94a3b8 !important;
        transition: all 0.3s ease;
        font-size: 0.85rem;
    }

    .nav-pill-cyber.active {
        background: rgba(0, 242, 255, 0.1) !important;
        border-color: rgba(0, 242, 255, 0.5) !important;
        color: var(--primary-neon) !important;
        box-shadow: 0 0 15px rgba(0, 242, 255, 0.1);
    }

    .user-avatar-glow {
        width: 32px; height: 32px;
        background: #1e293b;
        border: 1.5px solid var(--primary-neon);
        color: var(--primary-neon);
        box-shadow: 0 0 10px rgba(0, 242, 255, 0.2);
    }

    .dropdown-menu-cyber {
        background: #0f172a;
        border: 1px solid rgba(0, 242, 255, 0.2);
        padding: 8px;
    }

    .dropdown-item-cyber {
        color: #cbd5e1;
        border-radius: 8px;
        padding: 8px 12px;
    }

    .dropdown-item-cyber:hover {
        background: rgba(0, 242, 255, 0.1);
        color: var(--primary-neon);
    }
  </style>

  @stack('styles')
</head>

<body>
  <div class="page-wrapper" id="main-wrapper">

    <!-- HEADER START (Gunakan ID & Class yang sama dengan CSS di atas) -->
    <header class="app-header app-header-cyber fixed-top">
        <nav class="navbar navbar-expand-lg py-2">
          <div class="container-fluid px-4">

            <!-- LOGO AREA -->
            <a href="{{ route('activations.index') }}" class="d-flex align-items-center gap-3 text-decoration-none">
                <div class="logo-box-header">
                    <i class="ti ti-shield-lock text-dark fs-5"></i>
                </div>
                <div class="d-none d-sm-block">
                    <h5 class="m-0 text-white fw-bolder" style="letter-spacing: 1px;">TRUSUR</h5>
                    <div class="brand-text-sub">Licensing Center</div>
                </div>
            </a>

            <!-- NAV TENGAH -->
            <div class="mx-auto d-none d-md-block">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link nav-pill-cyber rounded-pill px-4 py-2 d-flex align-items-center gap-2 {{ request()->routeIs('activations.*') ? 'active' : '' }}"
                           href="{{ route('activations.index') }}">
                            <i class="ti ti-cpu fs-5"></i>
                            <span class="fw-bold">NODE ACTIVATION CONSOLE</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- PROFIL KANAN -->
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <a class="d-flex align-items-center gap-3 text-decoration-none dropdown-toggle p-1 pe-3" href="#" data-bs-toggle="dropdown">
                        <div class="user-avatar-glow rounded-circle d-flex align-items-center justify-content-center">
                            <i class="ti ti-user-check fs-4"></i>
                        </div>
                        <div class="d-none d-lg-block">
                            <div class="text-white fw-bold fs-2 mb-0" style="line-height: 1;">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <small style="color: #64748b; font-size: 10px;">SECURITY LEVEL 01</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-cyber mt-2 p-2 shadow-lg">
                         <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item dropdown-item-cyber d-flex align-items-center gap-2">
                                    <i class="ti ti-power text-danger"></i>
                                    <span class="fw-bold">TERMINATE SESSION</span>
                                </button>
                            </form>
                         </li>
                    </ul>
                </div>
            </div>

          </div>
        </nav>
    </header>
    <!-- HEADER END -->

    <!-- Konten Utama -->
    <div class="body-wrapper">
      @yield('content')
    </div>

  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Inisialisasi CSRF untuk semua request AJAX secara global
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
  </script>

  @stack('scripts')
</body>
</html>
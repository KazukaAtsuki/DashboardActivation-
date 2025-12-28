{{-- <style>
    /* --- ICONIC NAVBAR STYLING --- */
    .app-header-cyber {
        background: rgba(2, 6, 23, 0.8) !important; /* Deep Navy Glass */
        backdrop-filter: blur(15px);
        border-bottom: 1px solid rgba(0, 242, 255, 0.2);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    }

    /* Logo Styling */
    .logo-box-header {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #0066ff, #00f2ff);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 15px rgba(0, 242, 255, 0.4);
        position: relative;
        overflow: hidden;
    }

    .logo-box-header::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: linear-gradient(rgba(255,255,255,0.2), transparent);
        top: 0; left: 0;
    }

    .brand-text-main {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #fff;
        font-weight: 800;
        letter-spacing: 1px;
        line-height: 1;
    }

    .brand-text-sub {
        color: var(--primary-neon, #00f2ff);
        font-size: 9px;
        letter-spacing: 2px;
        text-transform: uppercase;
        font-weight: 700;
    }

    /* Center Nav Styling */
    .nav-pill-cyber {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(0, 242, 255, 0.1);
        color: #94a3b8 !important;
        transition: all 0.3s ease;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .nav-pill-cyber.active {
        background: rgba(0, 242, 255, 0.1) !important;
        border-color: rgba(0, 242, 255, 0.5) !important;
        color: var(--primary-neon, #00f2ff) !important;
        box-shadow: 0 0 15px rgba(0, 242, 255, 0.1);
    }

    .nav-pill-cyber:hover:not(.active) {
        background: rgba(255, 255, 255, 0.1);
        color: #fff !important;
    }

    /* User Profile Cyber */
    .profile-card-cyber {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 5px 15px;
        border-radius: 12px;
        transition: all 0.3s;
    }

    .profile-card-cyber:hover {
        border-color: rgba(0, 242, 255, 0.4);
        background: rgba(15, 23, 42, 0.9);
    }

    .user-avatar-glow {
        width: 30px;
        height: 30px;
        background: #1e293b;
        border: 1.5px solid var(--primary-neon, #00f2ff);
        color: var(--primary-neon, #00f2ff);
        box-shadow: 0 0 10px rgba(0, 242, 255, 0.2);
    }

    /* Dropdown Styling */
    .dropdown-menu-cyber {
        background: #0f172a;
        border: 1px solid rgba(0, 242, 255, 0.2);
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
    }

    .dropdown-item-cyber {
        color: #cbd5e1;
        transition: all 0.2s;
    }

    .dropdown-item-cyber:hover {
        background: rgba(0, 242, 255, 0.1);
        color: var(--primary-neon, #00f2ff);
    }
</style>

<header class="app-header app-header-cyber fixed-top">
    <nav class="navbar navbar-expand-lg py-2">
      <div class="container-fluid px-4">

        <!-- LOGO AREA -->
        <a href="{{ route('activations.index') }}" class="d-flex align-items-center gap-3 text-decoration-none">
            <div class="logo-box-header">
                <i class="ti ti-shield-lock text-dark fs-5"></i>
            </div>
            <div class="d-none d-sm-block">
                <h5 class="m-0 brand-text-main">TRUSUR</h5>
                <div class="brand-text-sub">Licensing Center</div>
            </div>
        </a>

        <!-- MIDDLE NAVIGATION -->
        <div class="mx-auto d-none d-md-block">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link nav-pill-cyber rounded-pill px-4 py-2 d-flex align-items-center gap-2 {{ request()->routeIs('activations.*') ? 'active' : '' }}"
                       href="{{ route('activations.index') }}">
                        <i class="ti ti-cpu fs-5"></i>
                        <span class="fw-bold">LOGGER ACTIVATION</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- RIGHT: PROFILE & ACCESS -->
        <div class="d-flex align-items-center">
            <div class="dropdown">
                <a class="d-flex align-items-center gap-3 text-decoration-none dropdown-toggle profile-card-cyber" href="#" data-bs-toggle="dropdown">
                    <div class="user-avatar-glow rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ti ti-user-check fs-4"></i>
                    </div>
                    <div class="d-none d-lg-block">
                        <div class="text-white fw-bold fs-2 mb-0" style="line-height: 1;">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <small style="color: #64748b; font-size: 10px; text-transform: uppercase;">Security Level 01</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-cyber mt-2 p-2">
                     <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item dropdown-item-cyber rounded d-flex align-items-center gap-2 py-2">
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
</header> --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Dashboard Aktivasi</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('template/assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('template/assets/css/styles.min.css') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

  <style>
      :root {
          --primary-neon: #00f2ff;
          --secondary-glow: #0066ff;
          --bg-deep: #020617;
          --card-surface: rgba(15, 23, 42, 0.8);
          --text-bright: #f8fafc;
      }

      body {
          background-color: var(--bg-deep);
          font-family: 'Plus Jakarta Sans', sans-serif;
          margin: 0;
          height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
          overflow: hidden;
      }

      /* 1. Background Aura Effect */
      .bg-aura {
          position: fixed;
          width: 100%;
          height: 100%;
          z-index: -1;
          background:
            radial-gradient(circle at 20% 30%, rgba(0, 242, 255, 0.05) 0%, transparent 40%),
            radial-gradient(circle at 80% 70%, rgba(0, 102, 255, 0.08) 0%, transparent 40%);
      }

      /* Animated Grid Line (Subtle) */
      .bg-grid {
          position: fixed;
          top: 0; left: 0; width: 100%; height: 100%;
          background-image: linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                            linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
          background-size: 50px 50px;
          z-index: -1;
      }

      /* 2. Iconic Card Design */
      .card-activation {
          border: 1px solid rgba(0, 242, 255, 0.2);
          border-radius: 30px;
          background: var(--card-surface);
          backdrop-filter: blur(20px);
          box-shadow: 0 0 40px rgba(0, 0, 0, 0.5),
                      inset 0 0 20px rgba(0, 242, 255, 0.05);
          width: 100%;
          max-width: 440px;
          padding: 45px;
          position: relative;
          overflow: hidden;
          animation: powerOn 1s ease-out;
      }

      @keyframes powerOn {
          0% { opacity: 0; transform: scale(0.9); filter: brightness(2); }
          100% { opacity: 1; transform: scale(1); filter: brightness(1); }
      }

      /* Decorative Scan Line */
      .card-activation::after {
          content: "";
          position: absolute;
          top: -100%; left: 0; width: 100%; height: 100%;
          background: linear-gradient(to bottom, transparent, rgba(0, 242, 255, 0.05), transparent);
          animation: scan 4s linear infinite;
      }

      @keyframes scan {
          0% { top: -100%; }
          100% { top: 100%; }
      }

      /* 3. Branding & Logo */
      .logo-container {
          position: relative;
          width: 80px;
          height: 80px;
          margin: 0 auto 20px;
      }

      .logo-hexagon {
          fill: none;
          stroke: var(--primary-neon);
          stroke-width: 2;
          stroke-dasharray: 200;
          animation: draw 3s infinite alternate;
      }

      @keyframes draw {
          0% { stroke-dashoffset: 200; filter: drop-shadow(0 0 2px var(--primary-neon)); }
          100% { stroke-dashoffset: 0; filter: drop-shadow(0 0 10px var(--primary-neon)); }
      }

      .brand-name {
          font-weight: 800;
          font-size: 1.6rem;
          color: var(--text-bright);
          letter-spacing: -1px;
          margin-bottom: 2px;
          text-align: center;
      }

      .brand-tagline {
          color: #94a3b8;
          font-size: 0.85rem;
          text-align: center;
          margin-bottom: 40px;
          text-transform: uppercase;
          letter-spacing: 2px;
      }

      /* 4. Form Styling */
      .input-wrapper {
          margin-bottom: 25px;
      }

      .custom-label {
          font-size: 0.75rem;
          font-weight: 700;
          color: var(--primary-neon);
          text-transform: uppercase;
          margin-bottom: 8px;
          display: block;
          opacity: 0.8;
      }

      .input-field {
          width: 100%;
          background: rgba(255, 255, 255, 0.03);
          border: 1px solid rgba(255, 255, 255, 0.1);
          border-radius: 12px;
          padding: 14px 18px;
          color: white;
          transition: all 0.3s;
          font-size: 0.95rem;
      }

      .input-field:focus {
          outline: none;
          background: rgba(0, 242, 255, 0.05);
          border-color: var(--primary-neon);
          box-shadow: 0 0 15px rgba(0, 242, 255, 0.2);
      }

      /* 5. Action Button */
      .btn-activate {
          width: 100%;
          padding: 16px;
          background: linear-gradient(90deg, var(--secondary-glow), var(--primary-neon));
          border: none;
          border-radius: 12px;
          color: #000;
          font-weight: 800;
          text-transform: uppercase;
          letter-spacing: 1px;
          cursor: pointer;
          transition: all 0.3s;
          box-shadow: 0 5px 20px rgba(0, 242, 255, 0.3);
          margin-top: 10px;
      }

      .btn-activate:hover {
          transform: translateY(-3px);
          box-shadow: 0 8px 25px rgba(0, 242, 255, 0.5);
          filter: brightness(1.1);
      }

      /* Alerts */
      .alert-custom {
          background: rgba(239, 68, 68, 0.1);
          border: 1px solid rgba(239, 68, 68, 0.2);
          color: #fca5a5;
          border-radius: 12px;
          padding: 12px;
          font-size: 0.85rem;
          margin-bottom: 20px;
      }

      .footer-text {
          text-align: center;
          margin-top: 35px;
          font-size: 0.7rem;
          color: #475569;
          letter-spacing: 1px;
      }
  </style>
</head>
<body>

  <div class="bg-aura"></div>
  <div class="bg-grid"></div>

  <div class="card-activation">

    <!-- Iconic Logo Section -->
    <div class="logo-container">
        <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <!-- Hexagon Background -->
            <path class="logo-hexagon" d="M50 5 L90 27.5 L90 72.5 L50 95 L10 72.5 L10 27.5 Z" />
            <!-- Activation Bolt -->
            <path d="M50 25 L35 55 L50 55 L45 80 L65 45 L50 45 Z" fill="var(--primary-neon)">
                <animate attributeName="opacity" values="1;0.5;1" dur="2s" repeatCount="indefinite" />
            </path>
        </svg>
    </div>

    <h1 class="brand-name">DASHBOARD AKTIVASI</h1>
    <p class="brand-tagline">System Ready for Boot</p>

    <!-- Error Handling -->
    @if ($errors->any())
        <div class="alert-custom">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login.process') }}" method="POST">
      @csrf

      <div class="input-wrapper">
        <label class="custom-label">Identifier Address</label>
        <input type="email" name="email" class="input-field" placeholder="operator@trusur.tech" value="{{ old('email') }}" required autofocus>
      </div>

      <div class="input-wrapper">
        <label class="custom-label">Security Key</label>
        <input type="password" name="password" class="input-field" placeholder="••••••••" required>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check">
              <input class="form-check-input" type="checkbox" id="trust" checked style="background-color: transparent; border-color: var(--primary-neon);">
              <label class="form-check-label small" for="trust" style="color: #94a3b8; cursor:pointer">
                  Trust this station
              </label>
          </div>
      </div>

      <button type="submit" class="btn-activate">
        Initiate Activation
      </button>

      <div class="footer-text">
        <p class="mb-1">© {{ date('Y') }} PT. TRUSUR UNGGUL TEKNUSA</p>
        <p class="mb-0">CORE ENGINE V3.0 // ACTIVE</p>
      </div>

    </form>
  </div>

  <script src="{{ asset('template/assets/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('template/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
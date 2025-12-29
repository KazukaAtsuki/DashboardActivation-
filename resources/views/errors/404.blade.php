<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Node Not Found</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-neon: #00f2ff;
            --bg-deep: #020617;
        }

        body {
            background-color: var(--bg-deep);
            color: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-image:
                radial-gradient(circle at 50% 50%, rgba(0, 242, 255, 0.05) 0%, transparent 50%),
                linear-gradient(rgba(255,255,255,0.01) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.01) 1px, transparent 1px);
            background-size: 100% 100%, 30px 30px, 30px 30px;
        }

        .error-container {
            text-align: center;
            position: relative;
        }

        /* Efek Glitch pada Angka 404 */
        .glitch-text {
            font-size: 120px;
            font-weight: 800;
            color: var(--primary-neon);
            text-shadow: 0 0 20px rgba(0, 242, 255, 0.5);
            position: relative;
            margin-bottom: 0;
            line-height: 1;
        }

        .glitch-text::after {
            content: '404';
            position: absolute;
            top: 0; left: 2px;
            text-shadow: -2px 0 #ff00c1;
            clip: rect(44px, 450px, 56px, 0);
            animation: glitch 3s infinite linear alternate-reverse;
        }

        @keyframes glitch {
            0% { clip: rect(10px, 9999px, 85px, 0); }
            20% { clip: rect(50px, 9999px, 30px, 0); }
            40% { clip: rect(10px, 9999px, 56px, 0); }
            60% { clip: rect(80px, 9999px, 90px, 0); }
            80% { clip: rect(20px, 9999px, 40px, 0); }
            100% { clip: rect(70px, 9999px, 80px, 0); }
        }

        .sub-text {
            font-size: 1.2rem;
            color: #94a3b8;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 40px;
        }

        .btn-return {
            background: transparent;
            border: 1px solid var(--primary-neon);
            color: var(--primary-neon);
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-return:hover {
            background: var(--primary-neon);
            color: #000;
            box-shadow: 0 0 30px rgba(0, 242, 255, 0.4);
            transform: translateY(-3px);
        }

        .scanline {
            width: 100%;
            height: 100px;
            z-index: 10;
            background: linear-gradient(0deg, rgba(0, 0, 0, 0) 0%, rgba(0, 242, 255, 0.05) 50%, rgba(0, 0, 0, 0) 100%);
            opacity: 0.1;
            position: absolute;
            bottom: 100%;
            animation: scanline 6s linear infinite;
        }

        @keyframes scanline {
            0% { bottom: 100%; }
            100% { bottom: -100%; }
        }
    </style>
</head>
<body>

    <div class="scanline"></div>

    <div class="error-container">
        <div class="status-box mb-4">
            <span class="badge bg-danger text-white p-2 px-3" style="letter-spacing: 2px;">SYSTEM ERROR: COORDINATE_NOT_FOUND</span>
        </div>

        <h1 class="glitch-text">404</h1>
        <p class="sub-text">Node data has been de-activated or moved</p>

        <a href="{{ url('/activations') }}" class="btn-return">
            <i class="ti ti-arrow-left"></i> RETURN TO CONSOLE
        </a>

        <div class="mt-5">
            <small style="color: #334155; letter-spacing: 5px;">PT. TRUSUR UNGGUL TEKNUSA</small>
        </div>
    </div>

</body>
</html>
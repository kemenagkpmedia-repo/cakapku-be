<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cakapku API - Sistem Kinerja Pegawai Kementerian Agama">
    <title>Cakapku API — Kementerian Agama</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-dark:  #1a5c2e;
            --green-main:  #237a3e;
            --green-mid:   #2e9e50;
            --green-light: #d4edda;
            --yellow-main: #f5c518;
            --yellow-soft: #fff8dc;
            --white:       #ffffff;
            --gray-100:    #f8f9fa;
            --gray-400:    #adb5bd;
            --gray-600:    #6c757d;
            --gray-800:    #343a40;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gray-100);
            color: var(--gray-800);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem;
            height: 64px;
            background: rgba(26, 92, 46, 0.97);
            backdrop-filter: blur(8px);
            box-shadow: 0 2px 12px rgba(0,0,0,.25);
        }
        .nav-brand {
            display: flex; align-items: center; gap: .75rem;
            text-decoration: none;
        }
        .nav-logo {
            width: 38px; height: 38px; border-radius: 8px;
            background: var(--yellow-main);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.1rem; color: var(--green-dark);
            flex-shrink: 0;
        }
        .nav-title {
            font-size: 1.1rem; font-weight: 700; color: var(--white);
            line-height: 1.2;
        }
        .nav-sub {
            font-size: .65rem; font-weight: 400;
            color: rgba(255,255,255,.65);
            letter-spacing: .04em;
        }
        .nav-btn {
            padding: .45rem 1.2rem;
            border-radius: 6px;
            background: var(--yellow-main);
            color: var(--green-dark);
            font-weight: 700; font-size: .85rem;
            text-decoration: none;
            transition: background .2s, transform .15s;
        }
        .nav-btn:hover { background: #e6b610; transform: translateY(-1px); }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-align: center;
            padding: 6rem 1.5rem 4rem;
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(155deg, var(--green-dark) 0%, var(--green-main) 55%, var(--green-mid) 100%);
        }

        /* decorative circles */
        .hero::before, .hero::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            opacity: .12;
        }
        .hero::before {
            width: 600px; height: 600px;
            background: var(--yellow-main);
            top: -180px; right: -180px;
        }
        .hero::after {
            width: 400px; height: 400px;
            background: var(--white);
            bottom: -150px; left: -120px;
        }

        .badge {
            display: inline-flex; align-items: center; gap: .45rem;
            background: rgba(245, 197, 24, .18);
            border: 1px solid rgba(245, 197, 24, .45);
            border-radius: 999px;
            padding: .35rem 1rem;
            font-size: .78rem; font-weight: 600;
            color: var(--yellow-main);
            letter-spacing: .06em;
            margin-bottom: 1.5rem;
            position: relative;
        }
        .badge-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--yellow-main);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .5; transform: scale(1.4); }
        }

        .hero h1 {
            font-size: clamp(2.2rem, 6vw, 3.8rem);
            font-weight: 800;
            color: var(--white);
            line-height: 1.15;
            margin-bottom: 1rem;
            position: relative;
        }
        .hero h1 span {
            color: var(--yellow-main);
        }

        .hero p {
            font-size: clamp(.95rem, 2.2vw, 1.15rem);
            color: rgba(255,255,255,.78);
            max-width: 540px;
            line-height: 1.7;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .hero-actions {
            display: flex; gap: 1rem; flex-wrap: wrap;
            justify-content: center;
            position: relative;
        }

        .btn-primary {
            display: inline-flex; align-items: center; gap: .6rem;
            padding: .85rem 2rem;
            border-radius: 10px;
            background: var(--yellow-main);
            color: var(--green-dark);
            font-weight: 700; font-size: 1rem;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(245,197,24,.4);
            transition: transform .2s, box-shadow .2s, background .2s;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(245,197,24,.5);
            background: #e6b610;
        }
        .btn-primary svg { width: 20px; height: 20px; }

        .btn-secondary {
            display: inline-flex; align-items: center; gap: .6rem;
            padding: .85rem 2rem;
            border-radius: 10px;
            background: rgba(255,255,255,.12);
            border: 1.5px solid rgba(255,255,255,.35);
            color: var(--white);
            font-weight: 600; font-size: 1rem;
            text-decoration: none;
            transition: background .2s, transform .2s;
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,.2);
            transform: translateY(-3px);
        }

        /* ── STATS STRIP ── */
        .stats {
            display: flex; flex-wrap: wrap; justify-content: center;
            gap: 1px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 16px;
            overflow: hidden;
            margin-top: 3rem;
            max-width: 600px;
            width: 100%;
            position: relative;
        }
        .stat-item {
            flex: 1 1 140px;
            padding: 1.2rem 1rem;
            text-align: center;
            background: rgba(255,255,255,.06);
        }
        .stat-item:not(:last-child) {
            border-right: 1px solid rgba(255,255,255,.1);
        }
        .stat-num {
            font-size: 1.7rem; font-weight: 800;
            color: var(--yellow-main);
        }
        .stat-label {
            font-size: .75rem; color: rgba(255,255,255,.6);
            margin-top: .2rem;
        }

        /* ── FEATURES ── */
        .section {
            padding: 5rem 1.5rem;
            max-width: 1080px;
            margin: 0 auto;
        }
        .section-title {
            text-align: center;
            font-size: 1.8rem; font-weight: 800;
            color: var(--green-dark);
            margin-bottom: .5rem;
        }
        .section-sub {
            text-align: center;
            color: var(--gray-600);
            margin-bottom: 3rem;
            font-size: .95rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 1.5rem;
        }
        .feature-card {
            background: var(--white);
            border: 1px solid #e0e0e0;
            border-radius: 14px;
            padding: 1.8rem 1.5rem;
            transition: box-shadow .25s, transform .25s, border-color .25s;
        }
        .feature-card:hover {
            box-shadow: 0 8px 32px rgba(35,122,62,.12);
            transform: translateY(-4px);
            border-color: var(--green-mid);
        }
        .feature-icon {
            width: 48px; height: 48px; border-radius: 12px;
            background: var(--green-light);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.4rem;
        }
        .feature-card h3 {
            font-size: 1rem; font-weight: 700;
            color: var(--green-dark);
            margin-bottom: .4rem;
        }
        .feature-card p {
            font-size: .87rem; color: var(--gray-600); line-height: 1.6;
        }

        /* ── ENDPOINT TABLE ── */
        .endpoints-wrap {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid #e0e0e0;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(0,0,0,.05);
        }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: var(--green-dark); }
        thead th {
            padding: .85rem 1.2rem;
            text-align: left;
            font-size: .8rem; font-weight: 600;
            color: var(--white); letter-spacing: .06em;
        }
        tbody tr { border-bottom: 1px solid #f0f0f0; transition: background .15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--green-light); }
        tbody td { padding: .75rem 1.2rem; font-size: .87rem; }
        .method {
            display: inline-block;
            padding: .2rem .55rem;
            border-radius: 5px;
            font-size: .72rem; font-weight: 700;
            letter-spacing: .04em;
        }
        .get    { background: #d1ecf1; color: #0c5460; }
        .post   { background: #d4edda; color: #155724; }
        .put    { background: #fff3cd; color: #856404; }
        .delete { background: #f8d7da; color: #721c24; }
        .endpoint-path {
            font-family: monospace; font-size: .83rem;
            color: var(--green-dark); font-weight: 600;
        }

        /* ── CTA BANNER ── */
        .cta {
            background: linear-gradient(135deg, var(--green-dark), var(--green-mid));
            padding: 4rem 1.5rem;
            text-align: center;
        }
        .cta h2 {
            font-size: 1.9rem; font-weight: 800;
            color: var(--white); margin-bottom: .8rem;
        }
        .cta p {
            color: rgba(255,255,255,.75);
            margin-bottom: 2rem; font-size: .97rem;
        }

        /* ── FOOTER ── */
        footer {
            background: var(--green-dark);
            border-top: 3px solid var(--yellow-main);
            padding: 1.8rem 2rem;
            text-align: center;
        }
        footer p { color: rgba(255,255,255,.5); font-size: .82rem; }
        footer span { color: var(--yellow-main); font-weight: 600; }

        @media (max-width: 600px) {
            .nav-sub { display: none; }
            .stat-item:not(:last-child) { border-right: none; border-bottom: 1px solid rgba(255,255,255,.1); }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <a href="/" class="nav-brand">
        <div class="nav-logo">K</div>
        <div>
            <div class="nav-title">Cakapku API</div>
            <div class="nav-sub">Kementerian Agama RI</div>
        </div>
    </a>
    <a href="/api/documentation" class="nav-btn">📄 API Docs</a>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="badge">
        <span class="badge-dot"></span>
        Server Aktif &amp; Siap Digunakan
    </div>

    <h1>
        Backend API<br>
        <span>Cakapku</span> Kemenag
    </h1>

    <p>
        Sistem manajemen kinerja harian pegawai berbasis REST API.
        Dibangun dengan Laravel & Sanctum untuk keamanan autentikasi modern.
    </p>

    <div class="hero-actions">
        <a href="/api/documentation" class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
            Buka API Documentation
        </a>
        <a href="#endpoints" class="btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            Lihat Endpoint
        </a>
    </div>

    <div class="stats">
        <div class="stat-item">
            <div class="stat-num">45+</div>
            <div class="stat-label">Total Route</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">7</div>
            <div class="stat-label">Resource API</div>
        </div>
        <div class="stat-item">
            <div class="stat-num">v1.0</div>
            <div class="stat-label">Versi API</div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<div class="section">
    <h2 class="section-title">Fitur Utama</h2>
    <p class="section-sub">Dirancang untuk mendukung pengelolaan kinerja pegawai secara efisien</p>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">🔐</div>
            <h3>Autentikasi Aman</h3>
            <p>Login via NIP atau Username dengan token Bearer menggunakan Laravel Sanctum.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📋</div>
            <h3>Manajemen Perkin</h3>
            <p>CRUD Perjanjian Kinerja beserta import data massal dari file Excel.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📊</div>
            <h3>Kinerja Harian</h3>
            <p>Pencatatan dan monitoring kinerja harian pegawai dengan filter per user.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">👥</div>
            <h3>Manajemen User</h3>
            <p>Role-based access control: Admin, Operator, User, dan Pimpinan Satker.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🏢</div>
            <h3>Data Satker</h3>
            <p>Pengelolaan Satuan Kerja beserta penugasan pimpinan dan pegawai.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📆</div>
            <h3>Periode Kinerja</h3>
            <p>Pengaturan periode aktif untuk siklus penilaian kinerja pegawai.</p>
        </div>
    </div>
</div>

<!-- ENDPOINTS -->
<div class="section" id="endpoints" style="padding-top:0">
    <h2 class="section-title">Daftar Endpoint</h2>
    <p class="section-sub">Semua endpoint membutuhkan Bearer Token kecuali <code style="background:#e9ecef;padding:.1rem .4rem;border-radius:4px;font-size:.85rem">/api/login</code></p>

    <div class="endpoints-wrap">
        <table>
            <thead>
                <tr>
                    <th>Method</th>
                    <th>Endpoint</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="method post">POST</span></td>
                    <td class="endpoint-path">/api/login</td>
                    <td>Login dengan NIP / Username</td>
                </tr>
                <tr>
                    <td><span class="method post">POST</span></td>
                    <td class="endpoint-path">/api/logout</td>
                    <td>Logout & hapus token</td>
                </tr>
                <tr>
                    <td><span class="method get">GET</span></td>
                    <td class="endpoint-path">/api/users</td>
                    <td>Daftar seluruh pegawai</td>
                </tr>
                <tr>
                    <td><span class="method get">GET</span></td>
                    <td class="endpoint-path">/api/satkers</td>
                    <td>Daftar Satuan Kerja</td>
                </tr>
                <tr>
                    <td><span class="method get">GET</span></td>
                    <td class="endpoint-path">/api/periodes</td>
                    <td>Daftar Periode Kinerja</td>
                </tr>
                <tr>
                    <td><span class="method get">GET</span></td>
                    <td class="endpoint-path">/api/perkins</td>
                    <td>Daftar Perjanjian Kinerja</td>
                </tr>
                <tr>
                    <td><span class="method post">POST</span></td>
                    <td class="endpoint-path">/api/perkins/import</td>
                    <td>Import Perkin dari Excel</td>
                </tr>
                <tr>
                    <td><span class="method get">GET</span></td>
                    <td class="endpoint-path">/api/iksks</td>
                    <td>Daftar Indikator Kinerja</td>
                </tr>
                <tr>
                    <td><span class="method get">GET</span></td>
                    <td class="endpoint-path">/api/kinerja-harian</td>
                    <td>Kinerja harian user login</td>
                </tr>
                <tr>
                    <td><span class="method get">GET</span></td>
                    <td class="endpoint-path">/api/dashboard/bawahan</td>
                    <td>Kinerja bawahan (Pimpinan)</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- CTA -->
<section class="cta">
    <h2>Siap Mengintegrasikan?</h2>
    <p>Buka dokumentasi lengkap Swagger UI untuk mencoba semua endpoint secara interaktif.</p>
    <a href="/api/documentation" class="btn-primary" style="display:inline-flex;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
        </svg>
        Buka Swagger UI
    </a>
</section>

<!-- FOOTER -->
<footer>
    <p>© {{ date('Y') }} <span>Cakapku</span> — Kementerian Agama Republik Indonesia. All rights reserved.</p>
</footer>

</body>
</html>

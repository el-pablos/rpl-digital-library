<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Perpustakaan Digital - Akses ribuan koleksi buku digital, jurnal ilmiah, dan referensi akademik. Platform pembelajaran modern untuk semua kalangan.">

        <title>{{ config('app.name', 'Perpustakaan Digital') }} - Jendela Ilmu Digital Anda</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&family=plus-jakarta-sans:400,500,600,700,800" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --primary: #10b981;
                --primary-dark: #059669;
                --secondary: #0ea5e9;
                --accent: #8b5cf6;
                --dark: #0f172a;
                --darker: #020617;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Plus Jakarta Sans', 'Instrument Sans', system-ui, sans-serif;
                background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 50%, #1e293b 100%);
                color: #f1f5f9;
                min-height: 100vh;
                overflow-x: hidden;
            }

            /* Animated Background */
            .bg-animated {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: -1;
                overflow: hidden;
            }

            .bg-animated::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle at 20% 80%, rgba(16, 185, 129, 0.08) 0%, transparent 50%),
                            radial-gradient(circle at 80% 20%, rgba(14, 165, 233, 0.08) 0%, transparent 50%),
                            radial-gradient(circle at 40% 40%, rgba(139, 92, 246, 0.05) 0%, transparent 50%);
                animation: bgMove 20s ease-in-out infinite;
            }

            @keyframes bgMove {
                0%, 100% { transform: translate(0, 0) rotate(0deg); }
                33% { transform: translate(2%, 2%) rotate(1deg); }
                66% { transform: translate(-1%, 1%) rotate(-1deg); }
            }

            /* Floating Book Particles */
            .book-particle {
                position: fixed;
                font-size: 1.5rem;
                opacity: 0.1;
                animation: floatBook 15s ease-in-out infinite;
                pointer-events: none;
                z-index: 0;
            }

            @keyframes floatBook {
                0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.1; }
                50% { transform: translateY(-30px) rotate(10deg); opacity: 0.2; }
            }

            /* Navbar */
            .navbar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                padding: 1rem 2rem;
                background: rgba(15, 23, 42, 0.8);
                backdrop-filter: blur(20px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                transition: all 0.3s ease;
            }

            .navbar.scrolled {
                padding: 0.75rem 2rem;
                background: rgba(15, 23, 42, 0.95);
            }

            .navbar-container {
                max-width: 1400px;
                margin: 0 auto;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .navbar-brand {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                text-decoration: none;
                color: #f1f5f9;
                font-weight: 700;
                font-size: 1.5rem;
            }

            .brand-icon {
                width: 40px;
                height: 40px;
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
            }

            .navbar-nav {
                display: flex;
                align-items: center;
                gap: 2rem;
                list-style: none;
            }

            .nav-link {
                color: #94a3b8;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s ease;
            }

            .nav-link:hover {
                color: var(--primary);
            }

            .navbar-actions {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .btn {
                padding: 0.75rem 1.5rem;
                border-radius: 10px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
                border: none;
            }

            .btn-ghost {
                background: transparent;
                color: #f1f5f9;
            }

            .btn-ghost:hover {
                background: rgba(255, 255, 255, 0.1);
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                color: white;
                box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 30px rgba(16, 185, 129, 0.4);
            }

            .btn-outline {
                background: transparent;
                border: 2px solid var(--primary);
                color: var(--primary);
            }

            .btn-outline:hover {
                background: var(--primary);
                color: white;
            }

            /* Hero Section */
            .hero {
                min-height: 100vh;
                display: flex;
                align-items: center;
                padding: 8rem 2rem 4rem;
                position: relative;
            }

            .hero-container {
                max-width: 1400px;
                margin: 0 auto;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 4rem;
                align-items: center;
            }

            .hero-content {
                z-index: 1;
            }

            .hero-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
                background: rgba(16, 185, 129, 0.1);
                border: 1px solid rgba(16, 185, 129, 0.2);
                border-radius: 50px;
                font-size: 0.875rem;
                color: var(--primary);
                margin-bottom: 1.5rem;
            }

            .hero-badge-dot {
                width: 8px;
                height: 8px;
                background: var(--primary);
                border-radius: 50%;
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.5; transform: scale(1.2); }
            }

            .hero-title {
                font-size: 4rem;
                font-weight: 800;
                line-height: 1.1;
                margin-bottom: 1.5rem;
            }

            .hero-title-gradient {
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 50%, var(--accent) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .hero-description {
                font-size: 1.25rem;
                color: #94a3b8;
                line-height: 1.7;
                margin-bottom: 2rem;
            }

            .hero-actions {
                display: flex;
                gap: 1rem;
                flex-wrap: wrap;
            }

            .btn-lg {
                padding: 1rem 2rem;
                font-size: 1.125rem;
            }

            .hero-stats {
                display: flex;
                gap: 3rem;
                margin-top: 3rem;
                padding-top: 2rem;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            .stat-item {
                text-align: center;
            }

            .stat-number {
                font-size: 2rem;
                font-weight: 700;
                color: var(--primary);
            }

            .stat-label {
                font-size: 0.875rem;
                color: #64748b;
            }

            /* Hero Visual */
            .hero-visual {
                position: relative;
            }

            .hero-card {
                background: rgba(30, 41, 59, 0.8);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 20px;
                padding: 2rem;
                backdrop-filter: blur(20px);
                transform: perspective(1000px) rotateY(-5deg);
                transition: transform 0.5s ease;
            }

            .hero-card:hover {
                transform: perspective(1000px) rotateY(0deg);
            }

            .search-preview {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 1rem 1.5rem;
                background: rgba(15, 23, 42, 0.8);
                border-radius: 12px;
                margin-bottom: 1.5rem;
            }

            .search-preview input {
                flex: 1;
                background: transparent;
                border: none;
                color: #f1f5f9;
                font-size: 1rem;
                outline: none;
            }

            .search-preview input::placeholder {
                color: #64748b;
            }

            .book-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
            }

            .book-card-mini {
                aspect-ratio: 2/3;
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                border-radius: 8px;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                padding: 0.75rem;
                position: relative;
                overflow: hidden;
            }

            .book-card-mini:nth-child(2) {
                background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%);
            }

            .book-card-mini:nth-child(3) {
                background: linear-gradient(135deg, var(--accent) 0%, #ec4899 100%);
            }

            .book-card-mini::before {
                content: '📖';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 2rem;
                opacity: 0.3;
            }

            .book-title-mini {
                font-size: 0.75rem;
                font-weight: 600;
                color: white;
                text-shadow: 0 1px 3px rgba(0,0,0,0.3);
            }

            /* Features Section */
            .section {
                padding: 6rem 2rem;
                position: relative;
            }

            .section-container {
                max-width: 1400px;
                margin: 0 auto;
            }

            .section-header {
                text-align: center;
                margin-bottom: 4rem;
            }

            .section-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
                background: rgba(16, 185, 129, 0.1);
                border-radius: 50px;
                font-size: 0.875rem;
                color: var(--primary);
                margin-bottom: 1rem;
            }

            .section-title {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
            }

            .section-description {
                font-size: 1.125rem;
                color: #94a3b8;
                max-width: 600px;
                margin: 0 auto;
            }

            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
                gap: 2rem;
            }

            .feature-card {
                background: rgba(30, 41, 59, 0.5);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 20px;
                padding: 2rem;
                transition: all 0.3s ease;
            }

            .feature-card:hover {
                transform: translateY(-5px);
                border-color: rgba(16, 185, 129, 0.3);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            }

            .feature-icon {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .feature-title {
                font-size: 1.25rem;
                font-weight: 600;
                margin-bottom: 0.75rem;
            }

            .feature-description {
                color: #94a3b8;
                line-height: 1.6;
            }

            /* Stats Section */
            .stats-section {
                background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(14, 165, 233, 0.1) 100%);
                border-top: 1px solid rgba(255, 255, 255, 0.05);
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 2rem;
            }

            .stats-card {
                text-align: center;
                padding: 2rem;
            }

            .stats-icon {
                font-size: 2.5rem;
                margin-bottom: 1rem;
            }

            .stats-number {
                font-size: 3rem;
                font-weight: 800;
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .stats-label {
                color: #94a3b8;
                font-size: 1rem;
            }

            /* Categories Section */
            .categories-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1.5rem;
            }

            .category-card {
                background: rgba(30, 41, 59, 0.5);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 15px;
                padding: 1.5rem;
                text-align: center;
                transition: all 0.3s ease;
                cursor: pointer;
                text-decoration: none;
                color: inherit;
            }

            .category-card:hover {
                transform: translateY(-3px);
                border-color: var(--primary);
                background: rgba(16, 185, 129, 0.1);
            }

            .category-icon {
                font-size: 2.5rem;
                margin-bottom: 1rem;
            }

            .category-name {
                font-weight: 600;
                margin-bottom: 0.25rem;
            }

            .category-count {
                font-size: 0.875rem;
                color: #64748b;
            }

            /* CTA Section */
            .cta-section {
                text-align: center;
            }

            .cta-card {
                background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(14, 165, 233, 0.2) 100%);
                border: 1px solid rgba(16, 185, 129, 0.3);
                border-radius: 30px;
                padding: 4rem;
            }

            .cta-title {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
            }

            .cta-description {
                font-size: 1.125rem;
                color: #94a3b8;
                margin-bottom: 2rem;
                max-width: 600px;
                margin-left: auto;
                margin-right: auto;
            }

            /* Footer */
            .footer {
                background: rgba(15, 23, 42, 0.9);
                border-top: 1px solid rgba(255, 255, 255, 0.05);
                padding: 4rem 2rem 2rem;
            }

            .footer-container {
                max-width: 1400px;
                margin: 0 auto;
            }

            .footer-grid {
                display: grid;
                grid-template-columns: 2fr 1fr 1fr 1fr;
                gap: 4rem;
                margin-bottom: 3rem;
            }

            .footer-brand {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin-bottom: 1rem;
                text-decoration: none;
                color: #f1f5f9;
                font-weight: 700;
                font-size: 1.25rem;
            }

            .footer-description {
                color: #64748b;
                line-height: 1.7;
                margin-bottom: 1.5rem;
            }

            .social-links {
                display: flex;
                gap: 1rem;
            }

            .social-link {
                width: 40px;
                height: 40px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #94a3b8;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .social-link:hover {
                background: var(--primary);
                color: white;
            }

            .footer-title {
                font-weight: 600;
                margin-bottom: 1.5rem;
                color: #f1f5f9;
            }

            .footer-links {
                list-style: none;
            }

            .footer-links li {
                margin-bottom: 0.75rem;
            }

            .footer-links a {
                color: #64748b;
                text-decoration: none;
                transition: color 0.3s ease;
            }

            .footer-links a:hover {
                color: var(--primary);
            }

            .footer-bottom {
                padding-top: 2rem;
                border-top: 1px solid rgba(255, 255, 255, 0.05);
                display: flex;
                justify-content: space-between;
                align-items: center;
                color: #64748b;
                font-size: 0.875rem;
            }

            /* Responsive */
            @media (max-width: 1024px) {
                .hero-container {
                    grid-template-columns: 1fr;
                    text-align: center;
                }

                .hero-visual {
                    display: none;
                }

                .hero-title {
                    font-size: 3rem;
                }

                .hero-stats {
                    justify-content: center;
                }

                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .footer-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 768px) {
                .navbar-nav {
                    display: none;
                }

                .hero-title {
                    font-size: 2.25rem;
                }

                .hero-stats {
                    flex-direction: column;
                    gap: 1.5rem;
                }

                .features-grid {
                    grid-template-columns: 1fr;
                }

                .stats-grid {
                    grid-template-columns: 1fr;
                }

                .footer-grid {
                    grid-template-columns: 1fr;
                    gap: 2rem;
                }

                .footer-bottom {
                    flex-direction: column;
                    gap: 1rem;
                    text-align: center;
                }

                .cta-card {
                    padding: 2rem;
                }
            }
        </style>
    </head>
    <body>
        <!-- Animated Background -->
        <div class="bg-animated"></div>

        <!-- Floating Book Particles -->
        <div class="book-particle" style="top: 15%; left: 10%;">📚</div>
        <div class="book-particle" style="top: 25%; right: 15%; animation-delay: -3s;">📖</div>
        <div class="book-particle" style="top: 60%; left: 5%; animation-delay: -6s;">📕</div>
        <div class="book-particle" style="top: 70%; right: 8%; animation-delay: -9s;">📗</div>
        <div class="book-particle" style="top: 85%; left: 20%; animation-delay: -12s;">📘</div>

        <!-- Navbar -->
        <nav class="navbar" id="navbar">
            <div class="navbar-container">
                <a href="{{ url('/') }}" class="navbar-brand">
                    <span class="brand-icon">📚</span>
                    <span>Perpustakaan Digital</span>
                </a>

                <ul class="navbar-nav">
                    <li><a href="#features" class="nav-link">Fitur</a></li>
                    <li><a href="#categories" class="nav-link">Kategori</a></li>
                    <li><a href="#stats" class="nav-link">Statistik</a></li>
                    <li><a href="#contact" class="nav-link">Kontak</a></li>
                </ul>

                <div class="navbar-actions">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-ghost">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary">Daftar Gratis</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-container">
                <div class="hero-content">
                    <div class="hero-badge">
                        <span class="hero-badge-dot"></span>
                        Platform Perpustakaan #1 Indonesia
                    </div>

                    <h1 class="hero-title">
                        Jelajahi Dunia<br>
                        <span class="hero-title-gradient">Ilmu Pengetahuan</span><br>
                        Tanpa Batas
                    </h1>

                    <p class="hero-description">
                        Akses ribuan koleksi buku digital, jurnal ilmiah, dan referensi akademik
                        kapan saja dan di mana saja. Platform pembelajaran modern untuk semua kalangan.
                    </p>

                    <div class="hero-actions">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
                                Mulai Sekarang
                            </a>
                        @endif
                        <a href="#features" class="btn btn-outline btn-lg">
                            Pelajari Lebih Lanjut
                        </a>
                    </div>

                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number">10K+</div>
                            <div class="stat-label">Koleksi Buku</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">5K+</div>
                            <div class="stat-label">Anggota Aktif</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">50+</div>
                            <div class="stat-label">Kategori</div>
                        </div>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="hero-card">
                        <div class="search-preview">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            <input type="text" placeholder="Cari buku, jurnal, atau penulis..." readonly>
                        </div>

                        <div class="book-grid">
                            <div class="book-card-mini">
                                <span class="book-title-mini">Pemrograman Web</span>
                            </div>
                            <div class="book-card-mini">
                                <span class="book-title-mini">Data Science</span>
                            </div>
                            <div class="book-card-mini">
                                <span class="book-title-mini">AI & Machine Learning</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="section" id="features">
            <div class="section-container">
                <div class="section-header">
                    <div class="section-badge">✨ Fitur Unggulan</div>
                    <h2 class="section-title">Mengapa Memilih Kami?</h2>
                    <p class="section-description">
                        Platform perpustakaan digital dengan fitur lengkap untuk mendukung
                        perjalanan belajar Anda.
                    </p>
                </div>

                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🔍</div>
                        <h3 class="feature-title">Pencarian Cerdas</h3>
                        <p class="feature-description">
                            Temukan buku yang Anda cari dengan mudah menggunakan fitur pencarian
                            cerdas berdasarkan judul, penulis, ISBN, atau kategori.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">📱</div>
                        <h3 class="feature-title">Akses Multi-Platform</h3>
                        <p class="feature-description">
                            Baca koleksi perpustakaan dari perangkat apapun - desktop, tablet,
                            atau smartphone. Responsif dan mudah digunakan.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <h3 class="feature-title">Riwayat Peminjaman</h3>
                        <p class="feature-description">
                            Lacak semua aktivitas peminjaman Anda. Lihat buku yang sedang dipinjam,
                            riwayat, dan tenggat waktu pengembalian.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">🔔</div>
                        <h3 class="feature-title">Notifikasi Otomatis</h3>
                        <p class="feature-description">
                            Dapatkan pengingat sebelum batas pengembalian tiba. Tidak perlu khawatir
                            terlambat mengembalikan buku.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">⭐</div>
                        <h3 class="feature-title">Review & Rating</h3>
                        <p class="feature-description">
                            Baca ulasan dari pembaca lain dan berikan rating untuk membantu
                            pembaca lain menemukan buku terbaik.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">🔒</div>
                        <h3 class="feature-title">Keamanan Data</h3>
                        <p class="feature-description">
                            Data Anda aman bersama kami. Enkripsi tingkat tinggi dan
                            perlindungan privasi yang ketat.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="section stats-section" id="stats">
            <div class="section-container">
                <div class="stats-grid">
                    <div class="stats-card">
                        <div class="stats-icon">📚</div>
                        <div class="stats-number">10,000+</div>
                        <div class="stats-label">Koleksi Buku</div>
                    </div>

                    <div class="stats-card">
                        <div class="stats-icon">👥</div>
                        <div class="stats-number">5,000+</div>
                        <div class="stats-label">Anggota Terdaftar</div>
                    </div>

                    <div class="stats-card">
                        <div class="stats-icon">📂</div>
                        <div class="stats-number">50+</div>
                        <div class="stats-label">Kategori Buku</div>
                    </div>

                    <div class="stats-card">
                        <div class="stats-icon">🕐</div>
                        <div class="stats-number">24/7</div>
                        <div class="stats-label">Akses Online</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="section" id="categories">
            <div class="section-container">
                <div class="section-header">
                    <div class="section-badge">📖 Kategori</div>
                    <h2 class="section-title">Jelajahi Berdasarkan Kategori</h2>
                    <p class="section-description">
                        Temukan buku sesuai minat Anda dari berbagai kategori yang tersedia.
                    </p>
                </div>

                <div class="categories-grid">
                    <div class="category-card">
                        <div class="category-icon">💻</div>
                        <div class="category-name">Teknologi & Komputer</div>
                        <div class="category-count">1,234 buku</div>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">📈</div>
                        <div class="category-name">Bisnis & Ekonomi</div>
                        <div class="category-count">856 buku</div>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">🔬</div>
                        <div class="category-name">Sains & Penelitian</div>
                        <div class="category-count">723 buku</div>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">📚</div>
                        <div class="category-name">Sastra & Bahasa</div>
                        <div class="category-count">1,567 buku</div>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">⚕️</div>
                        <div class="category-name">Kesehatan & Medis</div>
                        <div class="category-count">445 buku</div>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">🎨</div>
                        <div class="category-name">Seni & Desain</div>
                        <div class="category-count">389 buku</div>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">📜</div>
                        <div class="category-name">Sejarah & Budaya</div>
                        <div class="category-count">678 buku</div>
                    </div>

                    <div class="category-card">
                        <div class="category-icon">🧠</div>
                        <div class="category-name">Psikologi & Self-Help</div>
                        <div class="category-count">512 buku</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="section cta-section">
            <div class="section-container">
                <div class="cta-card">
                    <h2 class="cta-title">Siap Memulai Perjalanan Belajar Anda?</h2>
                    <p class="cta-description">
                        Bergabunglah dengan ribuan pembaca lainnya dan akses koleksi
                        perpustakaan digital kami secara gratis.
                    </p>
                    <div class="hero-actions" style="justify-content: center;">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                                Daftar Sekarang - Gratis!
                            </a>
                        @endif
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn btn-outline btn-lg">
                                Sudah Punya Akun? Masuk
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer" id="contact">
            <div class="footer-container">
                <div class="footer-grid">
                    <div>
                        <a href="{{ url('/') }}" class="footer-brand">
                            <span class="brand-icon">📚</span>
                            <span>Perpustakaan Digital</span>
                        </a>
                        <p class="footer-description">
                            Platform perpustakaan digital modern untuk mendukung
                            pembelajaran dan pengembangan pengetahuan masyarakat Indonesia.
                        </p>
                        <div class="social-links">
                            <a href="#" class="social-link" title="Facebook">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="social-link" title="Twitter">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                            <a href="#" class="social-link" title="Instagram">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a href="#" class="social-link" title="YouTube">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h4 class="footer-title">Navigasi</h4>
                        <ul class="footer-links">
                            <li><a href="#features">Fitur</a></li>
                            <li><a href="#categories">Kategori</a></li>
                            <li><a href="#stats">Statistik</a></li>
                            <li><a href="{{ route('login') }}">Masuk</a></li>
                            <li><a href="{{ route('register') }}">Daftar</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="footer-title">Bantuan</h4>
                        <ul class="footer-links">
                            <li><a href="#">FAQ</a></li>
                            <li><a href="#">Panduan Penggunaan</a></li>
                            <li><a href="#">Hubungi Kami</a></li>
                            <li><a href="#">Kebijakan Privasi</a></li>
                            <li><a href="#">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="footer-title">Kontak</h4>
                        <ul class="footer-links">
                            <li>📍 Jl. Pendidikan No. 123</li>
                            <li>📧 info@perpusdigital.id</li>
                            <li>📞 (021) 1234-5678</li>
                            <li>🕐 Senin - Jumat: 08:00 - 17:00</li>
                        </ul>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p>&copy; {{ date('Y') }} Perpustakaan Digital. Hak Cipta Dilindungi.</p>
                    <p>Dibuat dengan ❤️ untuk Indonesia</p>
                </div>
            </div>
        </footer>

        <script>
            // Navbar scroll effect
            window.addEventListener('scroll', function() {
                const navbar = document.getElementById('navbar');
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        </script>
    </body>
</html>
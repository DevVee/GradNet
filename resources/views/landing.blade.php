<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GradNet — Where Alumni Connect & Grow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #fff; color: #1c1e21; overflow-x: hidden; }

        /* ── Topbar ─────────────────────────────────────────────────── */
        .land-nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            padding: 0 24px; height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(0, 21, 64, 0.96);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.07);
            box-shadow: 0 2px 20px rgba(0,21,64,0.5);
        }
        .land-nav-brand {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
        }
        .land-nav-brand-icon {
            width: 36px; height: 36px;
            background: rgba(196,151,47,0.2);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: #c4972f;
        }
        .land-nav-brand-name {
            font-size: 1.15rem; font-weight: 800;
            color: #fff; letter-spacing: -0.02em;
        }
        .land-nav-brand-name span { color: #c4972f; }
        .land-nav-links { display: flex; align-items: center; gap: 8px; }
        .land-nav-link {
            padding: 8px 16px; border-radius: 999px;
            font-size: 0.8rem; font-weight: 600;
            color: rgba(255,255,255,0.75); text-decoration: none;
            transition: all 0.2s;
        }
        .land-nav-link:hover { color: #fff; background: rgba(255,255,255,0.1); }
        .land-btn-cta {
            padding: 9px 22px; border-radius: 999px;
            font-size: 0.8rem; font-weight: 700;
            background: linear-gradient(135deg, #c4972f, #e8b84b);
            color: #001540; text-decoration: none;
            transition: all 0.2s; border: none; cursor: pointer;
            box-shadow: 0 2px 10px rgba(196,151,47,0.4);
        }
        .land-btn-cta:hover { transform: translateY(-1px); box-shadow: 0 4px 20px rgba(196,151,47,0.55); color: #001540; }

        /* ── Hero Section ─────────────────────────────────────────────── */
        .land-hero {
            min-height: 100vh;
            background: linear-gradient(145deg, #001540 0%, #003087 50%, #1e4db7 100%);
            display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden; padding-top: 64px;
        }
        /* Decorative circles */
        .land-hero::before {
            content: '';
            position: absolute; top: -120px; right: -120px;
            width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(196,151,47,0.12) 0%, transparent 70%);
        }
        .land-hero::after {
            content: '';
            position: absolute; bottom: -100px; left: -80px;
            width: 400px; height: 400px; border-radius: 50%;
            background: radial-gradient(circle, rgba(30,77,183,0.3) 0%, transparent 70%);
        }
        .land-hero-content {
            text-align: center; z-index: 1; padding: 60px 24px;
            max-width: 780px; margin: 0 auto;
        }
        .land-hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(196,151,47,0.15);
            border: 1px solid rgba(196,151,47,0.3);
            border-radius: 999px; padding: 6px 16px;
            font-size: 0.75rem; font-weight: 600; color: #c4972f;
            margin-bottom: 28px; letter-spacing: 0.04em;
        }
        .land-hero-title {
            font-size: clamp(2.4rem, 6vw, 4rem);
            font-weight: 900; color: #fff;
            line-height: 1.1; letter-spacing: -0.03em;
            margin-bottom: 20px;
        }
        .land-hero-title span { color: #c4972f; }
        .land-hero-sub {
            font-size: 1.05rem; color: rgba(255,255,255,0.72);
            line-height: 1.7; margin-bottom: 44px;
            max-width: 560px; margin-left: auto; margin-right: auto;
        }
        .land-hero-ctas {
            display: flex; align-items: center; justify-content: center;
            gap: 14px; flex-wrap: wrap; margin-bottom: 56px;
        }
        .land-cta-primary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 32px; border-radius: 999px;
            font-size: 0.95rem; font-weight: 700;
            background: linear-gradient(135deg, #c4972f, #e8b84b);
            color: #001540; text-decoration: none;
            box-shadow: 0 4px 20px rgba(196,151,47,0.45);
            transition: all 0.25s;
        }
        .land-cta-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 28px rgba(196,151,47,0.6); color: #001540; }
        .land-cta-secondary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 14px 32px; border-radius: 999px;
            font-size: 0.95rem; font-weight: 600;
            background: rgba(255,255,255,0.1);
            border: 1.5px solid rgba(255,255,255,0.25);
            color: #fff; text-decoration: none;
            backdrop-filter: blur(4px);
            transition: all 0.25s;
        }
        .land-cta-secondary:hover { background: rgba(255,255,255,0.18); color: #fff; transform: translateY(-2px); }

        /* Stats row */
        .land-stats {
            display: flex; align-items: center; justify-content: center;
            gap: 48px; flex-wrap: wrap;
        }
        .land-stat-item { text-align: center; }
        .land-stat-number {
            font-size: 2.2rem; font-weight: 800; color: #fff;
            line-height: 1;
        }
        .land-stat-label {
            font-size: 0.75rem; font-weight: 500;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase; letter-spacing: 0.08em;
            margin-top: 4px;
        }
        .land-stat-divider { width: 1px; height: 44px; background: rgba(255,255,255,0.15); }

        /* ── Features Section ──────────────────────────────────────── */
        .land-features {
            padding: 96px 24px;
            background: #f8f9fb;
        }
        .land-section-label {
            font-size: 0.75rem; font-weight: 700; letter-spacing: 0.1em;
            text-transform: uppercase; color: #003087;
            margin-bottom: 12px;
        }
        .land-section-title {
            font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 800;
            color: #1c1e21; line-height: 1.15; letter-spacing: -0.025em;
            margin-bottom: 16px;
        }
        .land-section-sub {
            font-size: 1rem; color: #65676b; line-height: 1.7;
            max-width: 520px;
        }
        .land-feature-card {
            background: #fff;
            border-radius: 16px;
            padding: 32px 28px;
            border: 1px solid #eaecef;
            transition: all 0.25s;
            height: 100%;
        }
        .land-feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,48,135,0.1);
            border-color: rgba(0,48,135,0.2);
        }
        .land-feature-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; margin-bottom: 20px;
        }
        .land-feature-title {
            font-size: 1rem; font-weight: 700; color: #1c1e21;
            margin-bottom: 10px;
        }
        .land-feature-desc {
            font-size: 0.85rem; color: #65676b; line-height: 1.65;
        }

        /* ── Social proof / Avatars strip ──────────────────────────── */
        .land-social {
            padding: 80px 24px;
            background: #fff;
            text-align: center;
        }
        .land-avatar-strip {
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 28px;
        }
        .land-avatar-strip img {
            width: 48px; height: 48px; border-radius: 50%;
            border: 3px solid #fff; object-fit: cover;
            margin-left: -12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .land-avatar-strip img:first-child { margin-left: 0; }
        .land-avatar-count {
            width: 48px; height: 48px; border-radius: 50%;
            background: #003087; color: #fff;
            font-size: 0.75rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            border: 3px solid #fff; margin-left: -12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .land-social-text {
            font-size: 1.1rem; font-weight: 700; color: #1c1e21;
            margin-bottom: 8px;
        }
        .land-social-sub { font-size: 0.9rem; color: #65676b; }

        /* ── CTA Banner ────────────────────────────────────────────── */
        .land-cta-banner {
            padding: 96px 24px;
            background: linear-gradient(135deg, #001540 0%, #003087 55%, #1e4db7 100%);
            text-align: center; position: relative; overflow: hidden;
        }
        .land-cta-banner::before {
            content: '';
            position: absolute; top: -80px; right: -80px;
            width: 320px; height: 320px; border-radius: 50%;
            background: radial-gradient(circle, rgba(196,151,47,0.15) 0%, transparent 70%);
        }
        .land-cta-banner-title {
            font-size: clamp(1.8rem, 4vw, 2.6rem); font-weight: 800;
            color: #fff; margin-bottom: 16px;
        }
        .land-cta-banner-sub {
            font-size: 1rem; color: rgba(255,255,255,0.65); margin-bottom: 36px;
            max-width: 480px; margin-left: auto; margin-right: auto; line-height: 1.65;
        }

        /* ── Footer ─────────────────────────────────────────────────── */
        .land-footer {
            padding: 32px 24px;
            background: #0a0f1e;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 16px;
        }
        .land-footer-brand {
            display: flex; align-items: center; gap: 8px;
            text-decoration: none;
        }
        .land-footer-brand-icon {
            width: 28px; height: 28px; border-radius: 8px;
            background: rgba(196,151,47,0.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; color: #c4972f;
        }
        .land-footer-brand-name {
            font-size: 0.9rem; font-weight: 700; color: rgba(255,255,255,0.7);
        }
        .land-footer-brand-name span { color: #c4972f; }
        .land-footer-copy { font-size: 0.75rem; color: rgba(255,255,255,0.35); }
        .land-footer-links { display: flex; gap: 20px; }
        .land-footer-links a { font-size: 0.75rem; color: rgba(255,255,255,0.45); text-decoration: none; }
        .land-footer-links a:hover { color: rgba(255,255,255,0.75); }

        @media (max-width: 768px) {
            .land-nav-links .land-nav-link { display: none; }
            .land-stats { gap: 28px; }
            .land-stat-divider { display: none; }
            .land-footer { justify-content: center; text-align: center; }
        }
    </style>
</head>
<body>

    {{-- ── Navigation ─────────────────────────────────────────────── --}}
    <nav class="land-nav">
        <a href="{{ route('landing') }}" class="land-nav-brand">
            <div class="land-nav-brand-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <span class="land-nav-brand-name">Grad<span>Net</span></span>
        </a>
        <div class="land-nav-links">
            <a href="#features" class="land-nav-link">Features</a>
            <a href="#community" class="land-nav-link">Community</a>
            <a href="{{ route('login') }}" class="land-nav-link">Sign In</a>
            <a href="{{ route('register') }}" class="land-btn-cta">Get Started</a>
        </div>
    </nav>

    {{-- ── Hero ──────────────────────────────────────────────────── --}}
    <section class="land-hero">
        <div class="land-hero-content">

            <div class="land-hero-badge">
                <i class="fas fa-graduation-cap"></i>
                Alumni Network Platform
            </div>

            <h1 class="land-hero-title">
                Where Graduates<br>
                <span>Connect &amp; Grow</span>
            </h1>

            <p class="land-hero-sub">
                GradNet brings your alumni community to life — reconnect with batchmates,
                share milestones, discover career opportunities, and stay close to your roots.
            </p>

            <div class="land-hero-ctas">
                <a href="{{ route('register') }}" class="land-cta-primary">
                    <i class="fas fa-user-plus"></i> Join the Network
                </a>
                <a href="{{ route('login') }}" class="land-cta-secondary">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </a>
            </div>

            {{-- Live stats --}}
            <div class="land-stats">
                <div class="land-stat-item">
                    <div class="land-stat-number">{{ number_format($stats['alumni']) }}+</div>
                    <div class="land-stat-label">Alumni Members</div>
                </div>
                <div class="land-stat-divider"></div>
                <div class="land-stat-item">
                    <div class="land-stat-number">{{ number_format($stats['connections']) }}+</div>
                    <div class="land-stat-label">Connections Made</div>
                </div>
                <div class="land-stat-divider"></div>
                <div class="land-stat-item">
                    <div class="land-stat-number">{{ number_format($stats['events']) }}</div>
                    <div class="land-stat-label">Upcoming Events</div>
                </div>
            </div>

        </div>
    </section>

    {{-- ── Features ──────────────────────────────────────────────── --}}
    <section class="land-features" id="features">
        <div style="max-width:1100px;margin:0 auto;">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="land-section-label">Everything You Need</div>
                    <h2 class="land-section-title">Built for the<br>Alumni Experience</h2>
                    <p class="land-section-sub">
                        From your first post to finding your batchmates years later —
                        GradNet has every tool to keep your alumni community thriving.
                    </p>
                </div>
            </div>

            <div class="row g-4">

                <div class="col-md-6 col-lg-4">
                    <div class="land-feature-card">
                        <div class="land-feature-icon" style="background:#e8eef8;">
                            <i class="fas fa-rss" style="color:#003087;"></i>
                        </div>
                        <div class="land-feature-title">Live Activity Feed</div>
                        <div class="land-feature-desc">
                            See what your connections are up to. Share posts, photos, milestones,
                            and reactions — just like you remember from college, but better.
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="land-feature-card">
                        <div class="land-feature-icon" style="background:#fef3c7;">
                            <i class="fas fa-user-group" style="color:#d97706;"></i>
                        </div>
                        <div class="land-feature-title">Find Your Batchmates</div>
                        <div class="land-feature-desc">
                            Search by program, graduation year, and location.
                            Connect with your whole cohort in seconds — no matter where they are now.
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="land-feature-card">
                        <div class="land-feature-icon" style="background:#e6f4ed;">
                            <i class="fas fa-message" style="color:#1a7f4b;"></i>
                        </div>
                        <div class="land-feature-title">Direct Messaging</div>
                        <div class="land-feature-desc">
                            Chat directly with fellow alumni. Send messages, share files,
                            and coordinate with your batchmates in real-time.
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="land-feature-card">
                        <div class="land-feature-icon" style="background:#f3e8ff;">
                            <i class="fas fa-calendar-days" style="color:#7c3aed;"></i>
                        </div>
                        <div class="land-feature-title">Events & Reunions</div>
                        <div class="land-feature-desc">
                            Never miss a homecoming again. Browse upcoming events, RSVP,
                            and get reminders so you can always show up for your community.
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="land-feature-card">
                        <div class="land-feature-icon" style="background:#fde8e8;">
                            <i class="fas fa-newspaper" style="color:#d32f2f;"></i>
                        </div>
                        <div class="land-feature-title">News & Announcements</div>
                        <div class="land-feature-desc">
                            Stay updated with the latest from your institution.
                            News, achievements, and announcements delivered straight to your feed.
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="land-feature-card">
                        <div class="land-feature-icon" style="background:#e0f2f1;">
                            <i class="fas fa-people-group" style="color:#0097a7;"></i>
                        </div>
                        <div class="land-feature-title">Alumni Groups</div>
                        <div class="land-feature-desc">
                            Create or join groups by program, batch, industry, or interest.
                            Your tightest circles — all in one platform.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ── Social Proof ─────────────────────────────────────────── --}}
    <section class="land-social" id="community">
        <div style="max-width:600px;margin:0 auto;">

            {{-- Avatar strip pulled from demo data --}}
            <div class="land-avatar-strip">
                <img src="https://i.pravatar.cc/200?img=1"  alt="alumna">
                <img src="https://i.pravatar.cc/200?img=12" alt="alumnus">
                <img src="https://i.pravatar.cc/200?img=2"  alt="alumna">
                <img src="https://i.pravatar.cc/200?img=13" alt="alumnus">
                <img src="https://i.pravatar.cc/200?img=20" alt="alumna">
                <img src="https://i.pravatar.cc/200?img=21" alt="alumnus">
                <div class="land-avatar-count">+{{ max(0, $stats['alumni'] - 6) }}</div>
            </div>

            <div class="land-social-text">
                Join {{ number_format($stats['alumni']) }}+ alumni already on GradNet
            </div>
            <div class="land-social-sub" style="margin-bottom:36px;">
                From fresh graduates to department heads — your community is here.
            </div>

            <div style="display:flex;align-items:center;justify-content:center;gap:32px;flex-wrap:wrap;">
                <div style="text-align:center;">
                    <div style="font-size:1.8rem;font-weight:800;color:#003087;">{{ number_format($stats['connections']) }}+</div>
                    <div style="font-size:0.75rem;color:#9a9ea5;text-transform:uppercase;letter-spacing:0.06em;margin-top:4px;">Connections</div>
                </div>
                <div style="width:1px;height:36px;background:#eaecef;"></div>
                <div style="text-align:center;">
                    <div style="font-size:1.8rem;font-weight:800;color:#003087;">{{ number_format($stats['events']) }}</div>
                    <div style="font-size:0.75rem;color:#9a9ea5;text-transform:uppercase;letter-spacing:0.06em;margin-top:4px;">Upcoming Events</div>
                </div>
                <div style="width:1px;height:36px;background:#eaecef;"></div>
                <div style="text-align:center;">
                    <div style="font-size:1.8rem;font-weight:800;color:#003087;">8</div>
                    <div style="font-size:0.75rem;color:#9a9ea5;text-transform:uppercase;letter-spacing:0.06em;margin-top:4px;">Programs</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Final CTA Banner ─────────────────────────────────────── --}}
    <section class="land-cta-banner">
        <div style="position:relative;z-index:1;max-width:640px;margin:0 auto;">
            <h2 class="land-cta-banner-title">
                Ready to reconnect with your<br>graduating class?
            </h2>
            <p class="land-cta-banner-sub">
                Create your free GradNet account in minutes.
                Your batchmates are already waiting.
            </p>
            <div style="display:flex;align-items:center;justify-content:center;gap:14px;flex-wrap:wrap;">
                <a href="{{ route('register') }}" class="land-cta-primary">
                    <i class="fas fa-user-plus"></i> Create Free Account
                </a>
                <a href="{{ route('login') }}" class="land-cta-secondary">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </a>
            </div>
        </div>
    </section>

    {{-- ── Footer ──────────────────────────────────────────────── --}}
    <footer class="land-footer">
        <a href="{{ route('landing') }}" class="land-footer-brand">
            <div class="land-footer-brand-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <span class="land-footer-brand-name">Grad<span>Net</span></span>
        </a>
        <span class="land-footer-copy">© {{ date('Y') }} GradNet. All rights reserved.</span>
        <div class="land-footer-links">
            <a href="{{ route('login') }}">Sign In</a>
            <a href="{{ route('register') }}">Register</a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const target = document.querySelector(a.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
        // Fade in hero on load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('.land-hero-content').style.animation = 'fadeInUp 0.7s ease';
        });
    </script>
</body>
</html>

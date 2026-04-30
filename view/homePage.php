<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>biniru — Vinyl Records</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        :root {
            --black:   #0d0d0d;
            --surface: #161616;
            --card:    #1c1c1c;
            --border:  #2a2a2a;
            --accent:  #f5e642;
            --white:   #f0ede8;
            --muted:   #555;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; background: var(--black); color: var(--white); font-family: 'DM Sans', sans-serif; overflow-x: hidden; }

            .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            height: 62px; padding: 0 48px;
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(13,13,13,0); backdrop-filter: blur(0px);
            transition: background .4s, backdrop-filter .4s, border-color .4s;
            border-bottom: 1px solid transparent;
        }
        .navbar.scrolled {
            background: rgba(13,13,13,0.95);
            backdrop-filter: blur(16px);
            border-bottom-color: var(--border);
        }
        .nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .nav-logo img { height: 40px; width: 40px; object-fit: contain;}
        .nav-logo-text { font-family: 'Bebas Neue', sans-serif; font-size: 24px; letter-spacing: 5px; color: var(--white); }
        .nav-links { display: flex; align-items: center; gap: 28px; }
        .nav-links a { color: rgba(240,237,232,0.55); text-decoration: none; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; transition: color .2s; }
        .nav-links a:hover { color: var(--white); }
        .nav-cta { background: var(--accent) !important; color: var(--black) !important; padding: 9px 20px; border-radius: 3px; font-weight: 600 !important; }

        .hero {
            min-height: 100svh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            padding: 80px 48px 48px;
            gap: 60px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none; opacity: .5;
        }

        .hero::after {
            content: '';
            position: absolute; inset: 0;
            background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 80px 80px;
            opacity: .18;
            pointer-events: none;
        }

        .hero-left { position: relative; z-index: 1; }
        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: 10px;
            font-size: 10px; letter-spacing: 5px; text-transform: uppercase; color: var(--accent);
            margin-bottom: 22px;
        }
        .hero-eyebrow::before { content: ''; width: 28px; height: 1px; background: var(--accent); }

        .hero-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(72px, 8vw, 120px);
            letter-spacing: 4px;
            line-height: .9;
            color: var(--white);
            margin-bottom: 28px;
        }
        .hero-title .outline {
            -webkit-text-stroke: 1px var(--white);
            color: transparent;
        }
        .hero-title .accent { color: var(--accent); }

        .hero-desc {
            font-size: 15px; line-height: 1.75;
            color: var(--muted); max-width: 400px;
            margin-bottom: 40px;
        }

        .hero-actions { display: flex; gap: 14px; align-items: center; }
        .btn-primary {
            padding: 13px 30px;
            background: var(--accent); color: var(--black);
            border: none; border-radius: 3px;
            font-family: 'DM Sans', sans-serif;
            font-size: 11px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase;
            cursor: pointer; text-decoration: none;
            transition: background .2s, transform .2s;
        }
        .btn-primary:hover { background: #ffe900; transform: translateY(-2px); }
        .btn-ghost {
            padding: 13px 28px;
            background: transparent; color: var(--muted);
            border: 1px solid var(--border); border-radius: 3px;
            font-size: 11px; letter-spacing: 3px; text-transform: uppercase;
            cursor: pointer; text-decoration: none;
            transition: border-color .2s, color .2s;
        }
        .btn-ghost:hover { border-color: #555; color: var(--white); }

        /* HERO RIGHT — large spinning disc */
        .hero-right {
            position: relative; z-index: 1;
            display: flex; align-items: center; justify-content: center;
        }

        .big-disc {
            width: min(420px, 42vw);
            height: min(420px, 42vw);
            border-radius: 50%;
            background:
                radial-gradient(circle at 50% 50%,
                    transparent 0 12%, rgba(255,255,255,0.04) 12% 13%,
                    transparent 13% 18%, rgba(255,255,255,0.04) 18% 19%,
                    transparent 19% 26%, rgba(255,255,255,0.04) 26% 27%,
                    transparent 27% 35%, rgba(255,255,255,0.04) 35% 36%,
                    transparent 36% 44%, rgba(255,255,255,0.04) 44% 45%,
                    transparent 45% 50%),
                conic-gradient(from 0deg,
                    #1a1a1a 0deg, #222 60deg, #1a1a1a 120deg,
                    #222 180deg, #1a1a1a 240deg, #222 300deg, #1a1a1a 360deg);
            animation: spinDisc 12s linear infinite;
            box-shadow: 0 0 80px rgba(0,0,0,0.6), inset 0 0 60px rgba(0,0,0,0.4);
            position: relative;
        }

        .big-disc::after {
            content: '';
            position: absolute; left: 50%; top: 50%;
            transform: translate(-50%,-50%);
            width: 18%; height: 18%;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 0 4px rgba(245,230,66,0.15);
        }

        /* label text on disc */
        .disc-label {
            position: absolute; left: 50%; top: 50%;
            transform: translate(-50%,-50%);
            z-index: 2; text-align: center; pointer-events: none;
        }
        .disc-label span {
            display: block;
            font-size: 9px; letter-spacing: 2px; text-transform: uppercase;
            color: var(--black); font-weight: 700;
        }
        .disc-label .hole {
            width: 10px; height: 10px; border-radius: 50%;
            background: #0d0d0d; margin: 4px auto;
        }

        @keyframes spinDisc { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        /* Glow ring */
        .disc-glow {
            position: absolute;
            width: calc(min(420px, 42vw) + 80px);
            height: calc(min(420px, 42vw) + 80px);
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245,230,66,0.06) 0%, transparent 65%);
            pointer-events: none;
        }

        /* ── MARQUEE ── */
        .marquee-strip {
            overflow: hidden;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
            background: var(--surface);
        }
        .marquee-inner {
            display: flex; gap: 32px;
            animation: marquee 22s linear infinite;
            white-space: nowrap;
        }
        .marquee-inner span { font-family: 'Bebas Neue', sans-serif; font-size: 13px; letter-spacing: 5px; color: var(--muted); }
        .marquee-inner .dot { color: var(--accent); }
        @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }

        /* ── ABOUT STRIP ── */
        .about-strip {
            display: grid; grid-template-columns: 1fr 1fr 1fr;
            border-top: 1px solid var(--border);
        }
        .about-col {
            padding: 48px 40px;
            border-right: 1px solid var(--border);
        }
        .about-col:last-child { border-right: none; }
        .about-col .num {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 52px; letter-spacing: 2px;
            color: var(--accent); margin-bottom: 6px;
        }
        .about-col .label {
            font-size: 10px; letter-spacing: 4px; text-transform: uppercase;
            color: var(--muted); margin-bottom: 12px;
        }
        .about-col p { font-size: 13px; color: var(--muted); line-height: 1.7; }

        /* ── CTA BOTTOM ── */
        .cta-bottom {
            display: flex; align-items: center; justify-content: space-between;
            padding: 56px 48px;
            border-top: 1px solid var(--border);
            background: var(--surface);
        }
        .cta-bottom h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(36px, 4vw, 56px); letter-spacing: 4px; color: var(--white);
        }
        .cta-bottom h2 span { color: var(--accent); }

        .footer {
            padding: 24px 48px;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .footer p { font-size: 11px; letter-spacing: 2px; color: #333; text-transform: uppercase; }
    </style>
</head>
<body> 

    <nav class="navbar" id="navbar">
        <a class="nav-logo" href="homePage.php">
            <img src="../img/biniru_logo.png" alt="biniru">
            <span class="nav-logo-text">BINIRU</span>
        </a>
        <div class="nav-links">
            <a href="productsPage.php">Records</a>
            <a href="Dashboard.php">Dashboard</a>
            <a href="adminPage.php">Admin</a>
            <a href="loginPage.php" class="nav-cta">Logout</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-left">
            <p class="hero-eyebrow">Est. 2025 · Analog Sound</p>
            <h1 class="hero-title">
                THE<br>
                <span class="outline">SOUND</span><br>
                <span class="accent">NEVER</span><br>
                LIES
            </h1>
            <p class="hero-desc">
                biniru is a curated vinyl record shop for those who hear music the way it was meant to be heard — warm, wide, and without compromise.
            </p>
            <div class="hero-actions">
                <a class="btn-primary" href="productsPage.php">Browse Records</a>
                <a class="btn-ghost" href="#about">Our Story</a>
            </div>
        </div>
        <div class="hero-right">
            <div class="disc-glow"></div>
            <div class="big-disc">
                <div class="disc-label">
                    <span>Biniru</span>
                    <div class="hole"></div>
                    <span>Side A</span>
                </div>
            </div>
        </div>
    </section>

    <!-- MARQUEE -->
    <div class="marquee-strip">
        <div class="marquee-inner">
            <span>NEW ARRIVALS</span><span class="dot">✦</span>
            <span>LIMITED PRESS</span><span class="dot">✦</span>
            <span>ORIGINAL CUTS</span><span class="dot">✦</span>
            <span>SIDE A &amp; SIDE B</span><span class="dot">✦</span>
            <span>33⅓ RPM</span><span class="dot">✦</span>
            <span>ANALOG FOREVER</span><span class="dot">✦</span>
            <span>NEW ARRIVALS</span><span class="dot">✦</span>
            <span>LIMITED PRESS</span><span class="dot">✦</span>
            <span>ORIGINAL CUTS</span><span class="dot">✦</span>
            <span>SIDE A &amp; SIDE B</span><span class="dot">✦</span>
            <span>33⅓ RPM</span><span class="dot">✦</span>
            <span>ANALOG FOREVER</span><span class="dot">✦</span>
        </div>
    </div>

    <div class="about-strip" id="about">
        <div class="about-col">
            <div class="num">100%</div>
            <div class="label">Analog</div>
            <p>Every record pressed to the highest standard. No digital shortcuts, no compromises.</p>
        </div>
        <div class="about-col">
            <div class="num">33⅓</div>
            <div class="label">RPM Forever</div>
            <p>We believe in the format that defined generations. Long-play, full depth, real warmth.</p>
        </div>
        <div class="about-col">
            <div class="num">∞</div>
            <div class="label">Plays</div>
            <p>Vinyl that holds up. Curated records built to outlast trends and fill rooms with sound.</p>
        </div>
    </div>

    <!-- CTA BOTTOM -->
    <div class="cta-bottom">
        <h2>DROP THE<br><span>NEEDLE.</span></h2>
        <a class="btn-primary" href="productsPage.php" style="font-size:12px;padding:16px 36px">Shop Records →</a>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <p>© 2025 biniru · Vinyl Records</p>
        <p>All rights reserved</p>
    </footer>

    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 40);
        });
    </script>
</body>
</html>

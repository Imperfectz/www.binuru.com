<?php
session_start();
require_once '../bl/vinylManagement.php';

$vm = new VinylManagement();

$id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vinyl = $id ? $vm->getVinyl($id) : null;

if (!$vinyl) {
    header('Location: productsPage.php');
    exit;
}

// Cycle accent colors by vinyl_id
$accents   = ['#f5e642', '#ff3c3c', '#7df3c0'];
$sleeveBgs = ['#151510', '#150808', '#081510'];
$sleeveBds = ['#2e2b10', '#2e1010', '#102e1e'];
$ci        = ($vinyl['vinyl_id'] - 1) % 3;
$accent    = $accents[$ci];
$sbg       = $sleeveBgs[$ci];
$sbd       = $sleeveBds[$ci];
$short     = strtoupper(explode(' ', $vinyl['title'])[0]);
$price     = '₱' . number_format($vinyl['price'], 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>biniru — <?= htmlspecialchars($vinyl['title']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        :root { --black:#0d0d0d;--surface:#161616;--card:#1c1c1c;--border:#2a2a2a;--accent:<?= $accent ?>;--white:#f0ede8;--muted:#555; }
        *,*::before,*::after { box-sizing:border-box;margin:0;padding:0; }
        html,body { background:var(--black);color:var(--white);font-family:'DM Sans',sans-serif;min-height:100%; }

        /* NAV */
        .navbar{position:sticky;top:0;z-index:100;background:rgba(13,13,13,0.95);backdrop-filter:blur(16px);border-bottom:1px solid var(--border);padding:0 48px;height:62px;display:flex;align-items:center;justify-content:space-between;}
        .nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none;}
        .nav-logo img{height:30px;width:30px;object-fit:contain;filter:brightness(1.2);} 
        .nav-logo-text{font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:5px;color:var(--white);}
        .nav-links{display:flex;align-items:center;gap:28px;}
        .nav-links a{color:var(--muted);text-decoration:none;font-size:11px;letter-spacing:2px;text-transform:uppercase;transition:color .2s;}
        .nav-links a:hover{color:var(--white);}
        .nav-cta{background:var(--accent)!important;color:var(--black)!important;padding:9px 20px;border-radius:3px;font-weight:600!important;}

        /* BREADCRUMB */
        .breadcrumb{
            padding:20px 48px;border-bottom:1px solid var(--border);
            display:flex;align-items:center;gap:10px;
            font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);
        }
        .breadcrumb a{color:var(--muted);text-decoration:none;transition:color .2s;}
        .breadcrumb a:hover{color:var(--white);}
        .breadcrumb .sep{color:var(--border);}
        .breadcrumb .current{color:var(--accent);}

        /* DETAIL LAYOUT */
        .detail{
            display:grid;
            grid-template-columns:1fr 1fr;
            min-height:calc(100svh - 62px - 49px);
        }

        /* LEFT — vinyl animation */
        .detail-visual{
            border-right:1px solid var(--border);
            display:flex;flex-direction:column;
            align-items:center;justify-content:center;
            padding:60px 48px;
            background:var(--surface);
            position:relative;
            overflow:hidden;
        }

        /* subtle grid */
        .detail-visual::before{
            content:'';position:absolute;inset:0;
            background-image:linear-gradient(var(--border) 1px,transparent 1px),linear-gradient(90deg,var(--border) 1px,transparent 1px);
            background-size:60px 60px;opacity:.15;pointer-events:none;
        }

        /* glow */
        .detail-visual::after{
            content:'';position:absolute;inset:0;
            background:radial-gradient(ellipse 60% 60% at 50% 50%, rgba(255,255,255,0.03) 0%, transparent 70%);
            pointer-events:none;
        }

        /* ── VINYL PEEK ANIMATION ── */
        .vinyl-scene{
            position:relative;
            width:320px;height:500px;
            cursor:pointer;
            z-index:1;
        }

        .record{
            position:absolute;left:0;bottom:0;
            width:320px;height:320px;border-radius:50%;
            background:
                radial-gradient(circle at 50% 50%,
                    transparent 0 13%, rgba(255,255,255,0.035) 13% 14%,
                    transparent 14% 18%, rgba(255,255,255,0.035) 18% 19%,
                    transparent 19% 25%, rgba(255,255,255,0.035) 25% 26%,
                    transparent 26% 33%, rgba(255,255,255,0.035) 33% 34%,
                    transparent 34% 42%, rgba(255,255,255,0.035) 42% 43%,
                    transparent 43% 50%),
                conic-gradient(from 0deg,
                    #1a1a1a 0deg,#222 60deg,#1a1a1a 120deg,
                    #222 180deg,#1a1a1a 240deg,#222 300deg,#1a1a1a 360deg);
            transform:translateY(0);
            transition:transform .8s cubic-bezier(0.34,1.4,0.64,1);
            will-change:transform;
            z-index:1;
        }

        <?php if (!empty($vinyl['vinyl_image'])): ?>
        .record.has-vinyl-img{
            background: url('../<?= htmlspecialchars($vinyl['vinyl_image']) ?>') center/cover no-repeat;
        }
        .record.has-vinyl-img::after{ display:none; }
        <?php endif; ?>

        .vinyl-scene:hover .record{
            transform:translateY(-185px) rotate(270deg);
            transition:transform .85s cubic-bezier(0.34,1.3,0.64,1);
        }

        .record::after{
            content:'';position:absolute;left:50%;top:50%;
            transform:translate(-50%,-50%);
            width:90px;height:90px;border-radius:50%;
            background:var(--accent);
            box-shadow:0 0 0 5px color-mix(in srgb,var(--accent) 30%,transparent);
        }

        .sleeve{
            position:absolute;left:0;bottom:0;
            width:320px;height:320px;border-radius:4px;
            z-index:3;overflow:hidden;
            box-shadow:4px 4px 0 rgba(0,0,0,0.3);
            background:<?= $sbg ?>;
            border:1px solid <?= $sbd ?>;
            background-size:cover;background-position:center;
        }
        .sleeve.no-img{
            background:<?= $sbg ?> repeating-linear-gradient(180deg,transparent 0,transparent 10px,rgba(255,255,255,0.02) 10px,rgba(255,255,255,0.02) 11px);
        }

        .hover-hint{
            margin-top:20px;z-index:1;
            font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--muted);
            transition:opacity .3s;
        }
        .vinyl-scene:hover ~ .hover-hint{opacity:0;}

        /* RIGHT — info panel */
        .detail-info{
            padding:60px 56px;
            display:flex;flex-direction:column;gap:0;
        }

        .info-eyebrow{
            font-size:10px;letter-spacing:5px;text-transform:uppercase;
            color:var(--accent);margin-bottom:12px;
        }
        .info-title{
            font-family:'Bebas Neue',sans-serif;
            font-size:clamp(48px,5vw,72px);letter-spacing:3px;
            color:var(--white);line-height:.95;
            margin-bottom:6px;
        }
        .info-artist{
            font-size:15px;color:var(--muted);margin-bottom:32px;
        }

        /* DIVIDER */
        .info-divider{height:1px;background:var(--border);margin:28px 0;}

        /* META GRID */
        .meta-grid{
            display:grid;grid-template-columns:1fr 1fr;gap:20px;
            margin-bottom:28px;
        }
        .meta-item .meta-label{
            font-size:9px;letter-spacing:4px;text-transform:uppercase;
            color:var(--muted);margin-bottom:4px;
        }
        .meta-item .meta-value{
            font-family:'Bebas Neue',sans-serif;
            font-size:22px;letter-spacing:2px;color:var(--white);
        }

        /* DESCRIPTION */
        .info-desc{
            font-size:14px;line-height:1.8;color:var(--wh);
            margin-bottom:36px;flex:1;
        }

        /* PRICE + BUY */
        .buy-row{
            display:flex;align-items:center;justify-content:space-between;
            padding-top:28px;border-top:1px solid var(--border);
            margin-top:auto;
        }
        .buy-price{
            font-family:'Bebas Neue',sans-serif;
            font-size:52px;letter-spacing:2px;color:var(--accent);
            line-height:1;
        }
        .buy-price .currency{font-size:24px;vertical-align:super;}
        .buy-right{display:flex;flex-direction:column;align-items:flex-end;gap:10px;}
        .stock-badge{
            font-size:10px;letter-spacing:2px;text-transform:uppercase;
            color:var(--muted);
        }
        .btn-buy{
            padding:14px 32px;
            background:var(--accent);color:var(--black);
            border:none;border-radius:3px;
            font-size:11px;font-weight:700;letter-spacing:3px;text-transform:uppercase;
            cursor:pointer;transition:.2s;
            font-family:'DM Sans',sans-serif;
        }
        .btn-buy:hover{filter:brightness(1.08);transform:translateY(-2px);}
        .btn-back{
            display:inline-flex;align-items:center;gap:6px;
            font-size:11px;letter-spacing:2px;text-transform:uppercase;
            color:var(--muted);text-decoration:none;margin-bottom:28px;
            transition:color .2s;
        }
        .btn-back:hover{color:var(--white);}
    </style>
</head>
<body>

    <nav class="navbar">
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

    <div class="breadcrumb">
        <a href="homePage.php">Home</a>
        <span class="sep">/</span>
        <a href="productsPage.php">Records</a>
        <span class="sep">/</span>
        <span class="current"><?= htmlspecialchars($vinyl['title']) ?></span>
    </div>

    <div class="detail">

        <!-- LEFT: VINYL ANIMATION -->
        <div class="detail-visual">
            <div class="vinyl-scene">
                <!-- Record slides up on hover -->
                <?php $hasVinylImg = !empty($vinyl['vinyl_image']); ?>
                <div class="record<?= $hasVinylImg ? ' has-vinyl-img' : '' ?>"></div>
                <!-- Sleeve stays -->
                <?php
                    $hasCover = !empty($vinyl['cover_image']);
                    $sleeveClass = 'sleeve ' . ($hasCover ? 'has-img' : 'no-img');
                    $sleeveStyle = $hasCover
                        ? 'background-image:url(\'../' . htmlspecialchars($vinyl['cover_image']) . '\')'
                        : '';
                ?>
                <div class="<?= $sleeveClass ?>" style="<?= $sleeveStyle ?>"></div>
            </div>
            <p class="hover-hint">hover to reveal the record</p>
        </div>

        <!-- RIGHT: INFO -->
        <div class="detail-info">
            <a class="btn-back" href="productsPage.php">← Back to Records</a>

            <p class="info-eyebrow"><?= htmlspecialchars($vinyl['genre_name'] ?? 'Vinyl Record') ?></p>
            <h1 class="info-title"><?= htmlspecialchars($vinyl['title']) ?></h1>
            <p class="info-artist"><?= htmlspecialchars($vinyl['artist_name'] ?? '—') ?></p>

            <div class="meta-grid">
                <?php if ($vinyl['release_year']): ?>
                <div class="meta-item">
                    <div class="meta-label">Release Year</div>
                    <div class="meta-value"><?= $vinyl['release_year'] ?></div>
                </div>
                <?php endif; ?>
                <div class="meta-item">
                    <div class="meta-label">Format</div>
                    <div class="meta-value">33⅓ RPM</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">In Stock</div>
                    <div class="meta-value"><?= (int)$vinyl['stock'] ?> units</div>
                </div>
                <?php if ($vinyl['genre_name']): ?>
                <div class="meta-item">
                    <div class="meta-label">Genre</div>
                    <div class="meta-value"><?= htmlspecialchars($vinyl['genre_name']) ?></div>
                </div>
                <?php endif; ?>
            </div>

            <div class="info-divider"></div>

            <?php if ($vinyl['description']): ?>
            <p class="info-desc"><?= nl2br(htmlspecialchars($vinyl['description'])) ?></p>
            <?php else: ?>
            <p class="info-desc" style="font-style:italic">No description available for this record.</p>
            <?php endif; ?>

            <div class="buy-row">
                <div class="buy-price"><?= $price ?></div>
                <div class="buy-right">
                    <span class="stock-badge"><?= $vinyl['stock'] > 0 ? '✦ Available' : '✕ Sold Out' ?></span>
                    <button class="btn-buy" <?= $vinyl['stock'] < 1 ? 'disabled style="opacity:.4;cursor:not-allowed"' : '' ?>>
                        <?= $vinyl['stock'] > 0 ? 'Add to Cart' : 'Sold Out' ?>
                    </button>
                </div>
            </div>
        </div>

    </div>

<?php if (!empty($vinyl['cover_image'])): ?>
<script>
(function(){
    function rgbToHsl(r,g,b){
        r/=255;g/=255;b/=255;
        const max=Math.max(r,g,b),min=Math.min(r,g,b),l=(max+min)/2,d=max-min;
        if(d===0)return[0,0,l];
        const s=d/(1-Math.abs(2*l-1));
        let h=max===r?((g-b)/d+6)%6:max===g?(b-r)/d+2:(r-g)/d+4;
        return[h*60,s,l];
    }
    function hslToHex(h,s,l){
        const a=s*Math.min(l,1-l);
        const f=n=>{const k=(n+h/30)%12,c=l-a*Math.max(-1,Math.min(k-3,9-k,1));return Math.round(c*255).toString(16).padStart(2,'0');};
        return'#'+f(0)+f(8)+f(4);
    }
    const img=new Image();
    img.crossOrigin='anonymous';
    img.onload=function(){
        try{
            const c=document.createElement('canvas');
            c.width=c.height=50;
            const ctx=c.getContext('2d');
            ctx.drawImage(img,0,0,50,50);
            const px=ctx.getImageData(0,0,50,50).data;
            let best=-1,bh=0;
            for(let i=0;i<px.length;i+=4){
                const[h,s,l]=rgbToHsl(px[i],px[i+1],px[i+2]);
                const score=s*(1-Math.abs(l*2-1));
                if(score>best){best=score;bh=h;}
            }
            if(best>=0.08){
                document.documentElement.style.setProperty('--accent',hslToHex(bh,0.88,0.62));
            }
        }catch(e){}
    };
    img.src='../<?= htmlspecialchars($vinyl['cover_image']) ?>';
})();
</script>
<?php endif; ?>
</body>
</html>

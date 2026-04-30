<?php
session_start();
require_once '../bl/vinylManagement.php';

$vm     = new VinylManagement();
$vinyls = $vm->getAllVinyls();

$accents = ['#f5e642', '#ff3c3c', '#7df3c0'];
$sleeveBg = ['#151510', '#150808', '#081510'];
$sleeveBd = ['#2e2b10', '#2e1010', '#102e1e'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>biniru — Records</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root { --black:#0d0d0d;--surface:#161616;--card:#1c1c1c;--border:#2a2a2a;--accent:#f5e642;--white:#f0ede8;--muted:#555; }
        *,*::before,*::after { box-sizing:border-box;margin:0;padding:0; }
        html,body { background:var(--black);color:var(--white);font-family:'DM Sans',sans-serif;min-height:100%; }

        .navbar{position:sticky;top:0;z-index:100;background:rgba(13,13,13,0.95);backdrop-filter:blur(16px);border-bottom:1px solid var(--border);padding:0 48px;height:62px;display:flex;align-items:center;justify-content:space-between;}
        .nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none;}
        .nav-logo img{height:30px;width:30px;object-fit:contain;filter:brightness(1.2);} 
        .nav-logo-text{font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:5px;color:var(--white);}
        .nav-links{display:flex;align-items:center;gap:28px;}
        .nav-links a{color:var(--muted);text-decoration:none;font-size:11px;letter-spacing:2px;text-transform:uppercase;transition:color .2s;}
        .nav-links a:hover,.nav-links a.active{color:var(--white);}
        .nav-links a.active{color:var(--accent);}
        .nav-cta{background:var(--accent)!important;color:var(--black)!important;padding:9px 20px;border-radius:3px;font-weight:600!important;}

        .page-header{padding:56px 48px 0;border-bottom:1px solid var(--border);display:flex;align-items:flex-end;justify-content:space-between;padding-bottom:32px;}
        .page-header .eyebrow{font-size:10px;letter-spacing:6px;text-transform:uppercase;color:var(--accent);margin-bottom:6px;}
        .page-header h1{font-family:'Bebas Neue',sans-serif;font-size:56px;letter-spacing:4px;}
        .page-header p{font-size:13px;color:var(--muted);margin-top:4px;}
        .record-count{font-family:'Bebas Neue',sans-serif;font-size:72px;letter-spacing:2px;color:var(--border);}

        /* GRID */
        .products-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:var(--border);}
        /* Fallback for empty / fewer than 3 */
        @media(max-width:860px){.products-grid{grid-template-columns:1fr 1fr;}}
        @media(max-width:560px){.products-grid{grid-template-columns:1fr;}}

        /* PRODUCT CARD */
        .vinyl-card{background:var(--card);display:flex;flex-direction:column;text-decoration:none;color:inherit;transition:background .25s;}
        .vinyl-card:hover{background:#202020;}

        .card-visual{
            padding:32px 32px 0;
            display:flex;flex-direction:column;align-items:center;
        }

        .sleeve-static{
            width:240px;height:240px;border-radius:3px;
            overflow:hidden;position:relative;
            box-shadow:4px 4px 0 rgba(0,0,0,0.4);
            background-size:cover;background-position:center;
        }
        .sleeve-static.no-img{
            background:repeating-linear-gradient(180deg,transparent 0,transparent 9px,rgba(255,255,255,0.02) 9px,rgba(255,255,255,0.02) 10px);
        }

        .card-info{padding:24px 28px 28px;flex:1;display:flex;flex-direction:column;gap:10px;}
        .card-meta{display:flex;align-items:center;justify-content:space-between;}
        .card-genre{font-size:11px;letter-spacing:3px;text-transform:uppercase;}
        .card-year{font-size:11px;color:var(--muted);}
        .card-title{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:2px;}
        .card-artist{font-size:12px;color:var(--white);}
        .card-bottom{display:flex;align-items:center;justify-content:space-between;margin-top:auto;padding-top:16px;border-top:1px solid var(--border);}
        .card-price{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:1px;}
        .card-stock{font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--muted);}
        .btn-view{
            padding:8px 18px;border:1px solid var(--border);border-radius:3px;
            font-size:10px;letter-spacing:2px;text-transform:uppercase;
            color:var(--muted);background:transparent;cursor:pointer;
            transition:border-color .2s,color .2s;text-decoration:none;
        }
        .vinyl-card:hover .btn-view{border-color:#555;color:var(--white);}

        .empty-state{
            grid-column:1/-1;padding:100px;text-align:center;
        }
        .empty-state h2{font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:4px;color:var(--muted);margin-bottom:12px;}
        .empty-state p{font-size:13px;color:#333;}
    </style>
</head>
<body>

    <nav class="navbar">
        <a class="nav-logo" href="homePage.php">
            <img src="../img/biniru_logo.png" alt="biniru">
            <span class="nav-logo-text">BINIRU</span>
        </a>
        <div class="nav-links">
            <a href="productsPage.php" class="active">Records</a>
            <a href="Dashboard.php">Dashboard</a>
            <a href="adminPage.php">Admin</a>
            <a href="loginPage.php" class="nav-cta">Logout</a>
        </div>
    </nav>

    <div class="page-header">
        <div>
            <p class="eyebrow">Catalog</p>
            <h1>VINYL RECORDS</h1>
            <p>Click any record to learn more</p>
        </div>
        <span class="record-count"><?= str_pad(count($vinyls), 2, '0', STR_PAD_LEFT) ?></span>
    </div>

    <div class="products-grid">
        <?php if (empty($vinyls)): ?>
            <div class="empty-state">
                <h2>No Records Yet</h2>
                <p>Add vinyl records through the admin panel to display them here.</p>
            </div>
        <?php else: ?>
            <?php foreach ($vinyls as $i => $vinyl):
                $ci      = $i % 3; 
                $fallback = $accents[$ci];
                $sbg     = $sleeveBg[$ci];
                $sbd     = $sleeveBd[$ci];
                $price   = '₱' . number_format($vinyl['price'], 2);
                $hasCover = !empty($vinyl['cover_image']);
            ?>
            <a class="vinyl-card"
               href="vinylDetail.php?id=<?= $vinyl['vinyl_id'] ?>"
               data-cover="<?= $hasCover ? '../' . htmlspecialchars($vinyl['cover_image']) : '' ?>"
               data-fallback="<?= $fallback ?>"
               style="--card-accent:<?= $fallback ?>">
                <div class="card-visual">
                    <?php
                        $sleeveClass = $hasCover ? 'sleeve-static has-img' : 'sleeve-static no-img';
                        $sleeveStyle = $hasCover
                            ? 'background-image:url(\'../' . htmlspecialchars($vinyl['cover_image']) . '\')'
                            : 'background:' . $sbg . ';border:1px solid ' . $sbd;
                    ?>
                    <div class="<?= $sleeveClass ?>" style="<?= $sleeveStyle ?>"></div>
                </div>
                <div class="card-info">
                    <div class="card-meta">
                        <span class="card-genre" style="color:var(--card-accent)"><?= htmlspecialchars($vinyl['genre_name'] ?? 'Vinyl') ?></span>
                        <span class="card-year"><?= htmlspecialchars($vinyl['release_year'] ?? '') ?></span>
                    </div>
                    <p class="card-title"><?= htmlspecialchars($vinyl['title']) ?></p>
                    <p class="card-artist"><?= htmlspecialchars($vinyl['artist_name'] ?? '—') ?></p>
                    <div class="card-bottom">
                        <span class="card-price" style="color:var(--card-accent)"><?= $price ?></span>
                        <div style="display:flex;align-items:center;gap:12px">
                            <span class="card-stock"><?= $vinyl['stock'] ?> in stock</span>
                            <span class="btn-view">View →</span>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

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
    function extractVibrant(img,fallback){
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
            if(best<0.08)return fallback;
            return hslToHex(bh,0.88,0.62);
        }catch(e){return fallback;}
    }
    document.querySelectorAll('.vinyl-card[data-cover]').forEach(card=>{
        const src=card.dataset.cover;
        if(!src){return;}
        const img=new Image();
        img.crossOrigin='anonymous';
        img.onload=()=>{
            const color=extractVibrant(img,card.dataset.fallback);
            card.style.setProperty('--card-accent',color);
        };
        img.src=src;
    });
})();
</script>
</body>
</html>

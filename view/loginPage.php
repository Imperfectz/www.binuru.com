<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>biniru — Login</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root{--black:#0d0d0d;--surface:#161616;--card:#1c1c1c;--border:#2a2a2a;--accent:#f5e642;--white:#f0ede8;--muted:#666}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html,body{height:100%;background:var(--black);color:var(--white);font-family:'DM Sans',sans-serif;overflow:hidden}
        body{display:flex;align-items:center;justify-content:center;position:relative}
        /* Subtle background grid */
        body::before{content:'';position:absolute;inset:0;background-image:linear-gradient(var(--border) 1px,transparent 1px),linear-gradient(90deg,var(--border) 1px,transparent 1px);background-size:60px 60px;opacity:.4;pointer-events:none}
        /* Glow */
        body::after{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 50% 50% at 50% 50%,rgba(245,230,66,0.05) 0%,transparent 70%);pointer-events:none}
        .wrapper{position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;gap:32px}
        .brand{text-align:center}
        .brand .logo-row{display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:8px}
        .brand .logo-row img{height:40px;width:40px;object-fit:contain;}
        .brand h1{font-family:'Bebas Neue',sans-serif;font-size:52px;letter-spacing:8px;color:var(--white);line-height:1}
        .brand p{font-size:12px;color:var(--muted);letter-spacing:2px;text-transform:uppercase}
        .login-card{width:400px;background:rgba(22,22,22,0.9);border:1px solid var(--border);border-radius:5px;overflow:hidden;backdrop-filter:blur(20px)}
        .card-top{height:3px;background:var(--accent)}
        .card-body{padding:36px}
        .card-title{font-family:'Bebas Neue',sans-serif;font-size:30px;letter-spacing:3px;color:var(--white);margin-bottom:28px;text-align:center}
        .field{margin-bottom:18px}
        .field label{display:block;font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
        .field input{width:100%;background:#111;border:1px solid var(--border);color:var(--white);padding:12px 16px;border-radius:3px;font-size:14px;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .2s}
        .field input:focus{border-color:#3a3a3a}
        .field input::placeholder{color:#333}
        /* Real-time validation states */
        .field input.is-error{border-color:#ff3c3c!important;box-shadow:0 0 0 3px rgba(255,60,60,.13);}
        .field input.is-valid{border-color:#7df3c0!important;}
        .field-hint{font-size:10px;color:#ff3c3c;letter-spacing:.5px;margin-top:5px;display:none;line-height:1.4;}
        .btn-login{width:100%;padding:14px;background:var(--accent);color:var(--black);border:none;border-radius:3px;font-size:12px;font-weight:700;letter-spacing:4px;text-transform:uppercase;cursor:pointer;transition:.2s;margin-top:8px}
        .btn-login:hover{background:#ffe900;transform:translateY(-2px);box-shadow:0 8px 20px rgba(245,230,66,0.2)}
        .card-footer{padding:16px 36px;border-top:1px solid var(--border);text-align:center;font-size:12px;color:var(--muted)}
        .card-footer a{color:var(--accent);text-decoration:none}
        .card-footer a:hover{text-decoration:underline}
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="brand">
            <div class="logo-row">
                <img src="../img/biniru_logo.png" alt="biniru">
            </div>
            <h1>BINIRU</h1>
            <p>Vinyl Records · Est. 2025</p>
        </div>

        <div class="login-card">
            <div class="card-top"></div>
            <div class="card-body">
                <p class="card-title">SIGN IN</p>
                <div class="field">
                    <label>Email</label>
                    <input id="logemail" type="email" placeholder="your@email.com">
                    <span class="field-hint"></span>
                </div>
                <div class="field">
                    <label>Password</label>
                    <input id="logpass" type="password" placeholder="••••••••">
                    <span class="field-hint"></span>
                </div>
                <button class="btn-login" onclick="loginFunc()">Login →</button>
            </div>
            <div class="card-footer">
                No account? <a href="registPage.php">Register now</a>
            </div>
        </div>
    </div>

    <script src="../scripts/service.js"></script>
</body>
</html>

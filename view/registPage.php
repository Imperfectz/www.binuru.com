<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>biniru — Sign Up</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root{--black:#0d0d0d;--surface:#161616;--card:#1c1c1c;--border:#2a2a2a;--accent:#f5e642;--white:#f0ede8;--muted:#666}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html,body{background:var(--black);color:var(--white);font-family:'DM Sans',sans-serif;min-height:100%;display:flex;flex-direction:column}
        .navbar{background:rgba(13,13,13,0.94);border-bottom:1px solid var(--border);padding:0 40px;height:62px;display:flex;align-items:center;justify-content:space-between}
        .nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none}
        .nav-logo img{height:32px;width:32px;object-fit:contain;filter:invert(1) brightness(1.2)}
        .nav-logo-text{font-family:'Bebas Neue',sans-serif;font-size:26px;letter-spacing:4px;color:var(--white)}
        .nav-link{color:var(--muted);text-decoration:none;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;transition:color .2s}
        .nav-link:hover{color:var(--white)}
        .page-body{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 20px}
        .brand-mark{text-align:center;margin-bottom:32px}
        .brand-mark p{font-size:10px;letter-spacing:6px;text-transform:uppercase;color:var(--accent);margin-bottom:6px}
        .brand-mark h1{font-family:'Bebas Neue',sans-serif;font-size:44px;letter-spacing:6px;color:var(--white)}
        .signup-box{width:100%;max-width:460px;background:var(--card);border:1px solid var(--border);border-radius:5px;overflow:hidden}
        .box-header{padding:24px 32px;border-bottom:1px solid var(--border)}
        .box-header h2{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:3px;color:var(--white)}
        .box-header p{font-size:12px;color:var(--muted);margin-top:2px;letter-spacing:.5px}
        .box-body{padding:28px 32px 32px}
        .field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px}
        .field{margin-bottom:16px}
        .field label{display:block;font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
        .field input{width:100%;background:#111;border:1px solid var(--border);color:var(--white);padding:11px 14px;border-radius:3px;font-size:13px;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .2s}
        .field input:focus{border-color:#444}
        .field input::placeholder{color:#333}
        .btn-signup{width:100%;padding:13px;background:var(--accent);color:var(--black);border:none;border-radius:3px;font-size:12px;font-weight:700;letter-spacing:3px;text-transform:uppercase;cursor:pointer;transition:.2s;margin-top:4px}
        .btn-signup:hover{background:#ffe900;transform:translateY(-1px)}
        .box-footer{padding:16px 32px;border-top:1px solid var(--border);text-align:center;font-size:12px;color:var(--muted)}
        .box-footer a{color:var(--accent);text-decoration:none;font-weight:500}
        .box-footer a:hover{text-decoration:underline}
    </style>
</head>
<body>
    <nav class="navbar">
        <a class="nav-logo" href="loginPage.php">
            <img src="../img/biniru_logo.png" alt="biniru">
            <span class="nav-logo-text">BINIRU</span>
        </a>
        <a href="loginPage.php" class="nav-link">← Back to Login</a>
    </nav>

    <div class="page-body">
        <div class="brand-mark">
            <p>Vinyl Records</p>
            <h1>JOIN THE CLUB</h1>
        </div>

        <div class="signup-box">
            <div class="box-header">
                <h2>Create Account</h2>
                <p>Fill in your details to get started</p>
            </div>
            <div class="box-body">
                <div class="field-row">
                    <div class="field"><label>First Name</label><input id="fName" type="text" placeholder="Juan"></div>
                    <div class="field"><label>Last Name</label><input id="lName" type="text" placeholder="Dela Cruz"></div>
                </div>
                <div class="field"><label>Username</label><input id="uName" type="text" placeholder="juanrecords"></div>
                <div class="field"><label>Email</label><input id="email" type="email" placeholder="juan@email.com"></div>
                <div class="field"><label>Password</label><input id="password" type="password" placeholder="••••••••"></div>
                <button class="btn-signup" onclick="addFunc()">Create Account →</button>
            </div>
            <div class="box-footer">
                Already have an account? <a href="loginPage.php">Sign in</a>
            </div>
        </div>
    </div>

    <script src="../scripts/service.js"></script>
</body>
</html>

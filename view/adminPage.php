<?php
session_start();
require_once '../bl/userManagement.php';
require_once '../bl/vinylManagement.php';

$um     = new UserManagement();
$vm     = new VinylManagement();
$users  = $um->getUser();
$vinyls = $vm->getAllVinyls();
$artists = $vm->getArtists();
$genres  = $vm->getGenres();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>biniru — Admin</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --black: #0d0d0d;
            --surface: #161616;
            --card: #1c1c1c;
            --border: #2a2a2a;
            --accent: #f5e642;
            --accent2: #ff3c3c;
            --white: #f0ede8;
            --muted: #555;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            background: var(--black);
            color: var(--white);
            font-family: 'DM Sans', sans-serif;
            min-height: 100%;
        }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(13, 13, 13, 0.95);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 0 48px;
            height: 62px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .nav-logo img {
            height: 30px;
            width: 30px;
            object-fit: contain;
        }

        .nav-logo-text {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 24px;
            letter-spacing: 5px;
            color: var(--white);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: color .2s;
        }

        .nav-links a:hover {
            color: var(--white);
        }

        .nav-links a.active {
            color: var(--accent);
        }

        .nav-cta {
            background: var(--accent) !important;
            color: var(--black) !important;
            padding: 9px 20px;
            border-radius: 3px;
            font-weight: 600 !important;
        }

        .main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 48px 48px 80px;
        }

        /* TABS */
        .tabs {
            display: flex;
            gap: 0;
            border-bottom: 1px solid var(--border);
            margin-bottom: 36px;
        }

        .tab-btn {
            padding: 14px 28px;
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--muted);
            background: none;
            border: none;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: color .2s, border-color .2s;
            font-family: 'DM Sans', sans-serif;
        }

        .tab-btn.active {
            color: var(--white);
            border-bottom-color: var(--accent);
        }

        .tab-btn:hover:not(.active) {
            color: #aaa;
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        /* SECTION HEAD */
        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .section-head-left .eyebrow {
            font-size: 10px;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 4px;
        }

        .section-head-left h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 36px;
            letter-spacing: 4px;
        }

        .btn-add {
            padding: 10px 22px;
            background: var(--accent);
            color: var(--black);
            border: none;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            text-decoration: none;
            transition: .2s;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-add:hover {
            background: #ffe900;
            transform: translateY(-1px);
        }

        /* TABLE */
        .table-wrap {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 4px;
            overflow: hidden;
        }

        .dataTables_wrapper {
            padding: 20px 24px;
        }

        .dataTables_filter input {
            background: #111;
            border: 1px solid var(--border);
            color: var(--white);
            padding: 8px 14px;
            border-radius: 3px;
            font-size: 13px;
            outline: none;
        }

        .dataTables_filter input:focus {
            border-color: #444;
        }

        .dataTables_length select {
            background: #111;
            border: 1px solid var(--border);
            color: var(--white);
            padding: 6px;
            border-radius: 3px;
        }

        .dataTables_info,
        .dataTables_paginate {
            font-size: 12px;
            color: var(--muted);
        }

        .dataTables_paginate .paginate_button {
            color: var(--muted) !important;
            padding: 6px 10px;
            border-radius: 3px;
            cursor: pointer;
        }

        .dataTables_paginate .paginate_button.current,
        .dataTables_paginate .paginate_button:hover {
            background: var(--accent) !important;
            color: var(--black) !important;
            border: none !important;
        }

        table.dataTable thead th {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 9px;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 400;
            padding: 13px 14px;
        }

        table.dataTable tbody tr {
            background: var(--card);
            border-bottom: 1px solid #1e1e1e;
            transition: background .15s;
        }

        table.dataTable tbody tr:hover {
            background: #1e1e1e;
        }

        table.dataTable tbody td {
            padding: 12px 14px;
            font-size: 13px;
            color: var(--white);
            border: none;
        }

        .btn-edit {
            padding: 5px 14px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--muted);
            border-radius: 3px;
            font-size: 9px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: .2s;
        }

        .btn-edit:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .btn-delete {
            padding: 5px 14px;
            background: transparent;
            border: 1px solid #2e1010;
            color: var(--accent2);
            border-radius: 3px;
            font-size: 9px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: .2s;
        }

        .btn-delete:hover {
            background: rgba(255, 60, 60, .08);
        }

        /* MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 999;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            justify-content: center;
            align-items: center;
        }

        .modal-box {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 5px;
            padding: 36px;
            width: 100%;
            max-width: 520px;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-box h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            letter-spacing: 3px;
            margin-bottom: 24px;
        }

        .close-btn {
            position: absolute;
            top: 16px;
            right: 20px;
            font-size: 20px;
            cursor: pointer;
            background: none;
            border: none;
            color: var(--muted);
            line-height: 1;
        }

        .close-btn:hover {
            color: var(--white);
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .field {
            margin-bottom: 16px;
        }

        .field label {
            display: block;
            font-size: 9px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            background: #111;
            border: 1px solid var(--border);
            color: var(--white);
            padding: 10px 14px;
            border-radius: 3px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color .2s;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: #444;
        }

        .field select option {
            background: #111;
        }

        .field textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-save {
            width: 100%;
            padding: 12px;
            background: var(--accent);
            color: var(--black);
            border: none;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            cursor: pointer;
            transition: .2s;
            font-family: 'DM Sans', sans-serif;
            margin-top: 4px;
        }

        .btn-save:hover {
            background: #ffe900;
        }

        .file-upload-wrap {
            position: relative;
            border: 1px dashed var(--border);
            border-radius: 3px;
            padding: 14px;
            text-align: center;
            transition: border-color .2s;
            cursor: pointer;
        }

        .file-upload-wrap:hover {
            border-color: #444;
        }

        .file-upload-wrap input[type=file] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .file-upload-label {
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 1px;
        }

        .file-upload-label span {
            color: var(--accent);
        }

        .img-preview {
            display: none;
            margin-top: 10px;
            max-width: 100%;
            max-height: 110px;
            border-radius: 3px;
            object-fit: cover;
            border: 1px solid var(--border);
        }

        .img-preview.round {
            border-radius: 50%;
            width: 90px;
            height: 90px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a class="nav-logo" href="homePage.php">
            <img src="../img/biniru_logo.png" alt="biniru">
            <span class="nav-logo-text">BINIRU</span>
        </a>
        <div class="nav-links">
            <a href="homePage.php">Home</a>
            <a href="productsPage.php">Records</a>
            <a href="Dashboard.php">Dashboard</a>
            <a href="adminPage.php" class="active">Admin</a>
            <a href="loginPage.php" class="nav-cta">Logout</a>
        </div>
    </nav>

    <main class="main">
        <!-- TABS -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('users', this)">Users</button>
            <button class="tab-btn" onclick="switchTab('vinyls', this)">Vinyl Records</button>
        </div>

        <!-- USERS TAB -->
        <div id="tab-users" class="tab-panel active">
            <div class="section-head">
                <div class="section-head-left">
                    <p class="eyebrow">Management</p>
                    <h2>USER RECORDS</h2>
                </div>
                <a class="btn-add" href="registPage.php">+ Add User</a>
            </div>
            <div class="table-wrap">
                <table id="usersTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): foreach ($users as $i => $u): ?>
                                <tr>
                                    <td style="color:var(--muted)"><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($u['firstName']) ?></td>
                                    <td><?= htmlspecialchars($u['lastName'])  ?></td>
                                    <td style="color:var(--muted)">@<?= htmlspecialchars($u['userName']) ?></td>
                                    <td style="color:var(--muted)"><?= htmlspecialchars($u['email'])    ?></td>
                                    <td>
                                        <button class="btn-edit" onclick="openUserModal(<?= $u['userID'] ?>,'<?= htmlspecialchars($u['firstName'], ENT_QUOTES) ?>','<?= htmlspecialchars($u['lastName'], ENT_QUOTES) ?>','<?= htmlspecialchars($u['userName'], ENT_QUOTES) ?>','<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>')">Edit</button>
                                        <button class="btn-delete" onclick="deleteFunc(<?= $u['userID'] ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--muted);padding:32px">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- VINYLS TAB -->
        <div id="tab-vinyls" class="tab-panel">
            <div class="section-head">
                <div class="section-head-left">
                    <p class="eyebrow">Catalog</p>
                    <h2>VINYL RECORDS</h2>
                </div>
                <button class="btn-add" onclick="openVinylModal()">+ Add Record</button>
            </div>
            <div class="table-wrap">
                <table id="vinylsTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Artist</th>
                            <th>Genre</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($vinyls)): foreach ($vinyls as $i => $v): ?>
                                <tr>
                                    <td style="color:var(--muted)"><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($v['title'])       ?></td>
                                    <td style="color:var(--muted)"><?= htmlspecialchars($v['artist_name'] ?? '—') ?></td>
                                    <td style="color:var(--muted)"><?= htmlspecialchars($v['genre_name']  ?? '—') ?></td>
                                    <td>₱<?= number_format($v['price'], 2)       ?></td>
                                    <td style="color:var(--muted)"><?= $v['stock'] ?></td>
                                    <td style="color:var(--muted)"><?= $v['release_year'] ?? '—' ?></td>
                                    <td>
                                        <button class="btn-edit" onclick="openVinylModal(<?= htmlspecialchars(json_encode($v['vinyl_id']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($v['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode((int)($v['artist_id'] ?? 0)), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode((int)($v['genre_id'] ?? 0)), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($v['price'] ?? 0), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($v['stock'] ?? 0), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($v['release_year'] ?? ''), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($v['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($v['cover_image'] ? '../' . $v['cover_image'] : ''), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($v['vinyl_image'] ? '../' . $v['vinyl_image'] : ''), ENT_QUOTES, 'UTF-8') ?>)">Edit</button>
                                        <button class="btn-delete" onclick="deleteVinyl(<?= $v['vinyl_id'] ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="8" style="text-align:center;color:var(--muted);padding:32px">No records found. Add your first vinyl.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- USER EDIT MODAL -->
    <div class="modal-overlay" id="userModal">
        <div class="modal-box">
            <button class="close-btn" onclick="closeModal('userModal')">&times;</button>
            <h2>Edit User</h2>
            <div class="field-row">
                <div class="field"><label>First Name</label><input id="txtfName" type="text"></div>
                <div class="field"><label>Last Name</label><input id="txtlName" type="text"></div>
            </div>
            <div class="field"><label>Username</label><input id="txtuName" type="text"></div>
            <div class="field"><label>Email</label><input id="txtemail" type="email"></div>
            <div class="field"><label>New Password <span style="color:var(--muted);font-size:9px;letter-spacing:1px">(leave blank to keep current)</span></label><input id="txtpassword" type="password" placeholder="Enter new password…"></div>
            <button id="btnSave" class="btn-save">Save Changes</button>
        </div>
    </div>

    <!-- VINYL ADD/EDIT MODAL -->
    <div class="modal-overlay" id="vinylModal">
        <div class="modal-box">
            <button class="close-btn" onclick="closeModal('vinylModal')">&times;</button>
            <h2 id="vinylModalTitle">Add Vinyl</h2>
            <input type="hidden" id="vinyl_id" value="">
            <div class="field"><label>Title</label><input id="v_title" type="text" placeholder="Album title"></div>
            <div class="field-row">
                <div class="field">
                    <label>Artist</label>
                    <select id="v_artist">
                        <option value="">— Select —</option>
                        <?php foreach ($artists as $a): ?>
                            <option value="<?= $a['artist_id'] ?>"><?= htmlspecialchars($a['artist_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label>Genre</label>
                    <select id="v_genre">
                        <option value="">— Select —</option>
                        <?php foreach ($genres as $g): ?>
                            <option value="<?= $g['genre_id'] ?>"><?= htmlspecialchars($g['genre_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="field-row">
                <div class="field"><label>Price (₱)</label><input id="v_price" type="number" step="0.01" placeholder="0.00"></div>
                <div class="field"><label>Stock</label><input id="v_stock" type="number" placeholder="0"></div>
            </div>
            <div class="field"><label>Release Year</label><input id="v_year" type="number" placeholder="2025" min="1900" max="2099"></div>
            <div class="field"><label>Description</label><textarea id="v_desc" placeholder="About this record..."></textarea></div>
            <div class="field-row">
                <div class="field">
                    <label>Album Cover</label>
                    <div class="file-upload-wrap">
                        <input type="file" id="v_cover_image" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewImg(this,'v_cover_prev')">
                        <p class="file-upload-label">Drop or <span>browse</span> · max 5 MB</p>
                    </div>
                    <img id="v_cover_prev" class="img-preview" alt="Cover preview">
                </div>
                <div class="field">
                    <label>Vinyl Disc Image</label>
                    <div class="file-upload-wrap">
                        <input type="file" id="v_vinyl_image" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewImg(this,'v_vinyl_prev')">
                        <p class="file-upload-label">Drop or <span>browse</span> · max 5 MB</p>
                    </div>
                    <img id="v_vinyl_prev" class="img-preview round" alt="Vinyl preview">
                </div>
            </div>
            <button id="btnSaveVinyl" class="btn-save" onclick="saveVinyl()">Save Record</button>
        </div>
    </div>

    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>
    <script src="../scripts/service.js"></script>
    <script>
        // TABS
        function switchTab(name, btn) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + name).classList.add('active');
            btn.classList.add('active');
        }

        // MODAL HELPERS
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        window.addEventListener('click', e => {
            ['userModal', 'vinylModal'].forEach(id => {
                if (e.target === document.getElementById(id)) closeModal(id);
            });
        });

        // USER MODAL
        function openUserModal(uid, fn, ln, un, em) {
            document.getElementById('txtfName').value = fn;
            document.getElementById('txtlName').value = ln;
            document.getElementById('txtuName').value = un;
            document.getElementById('txtemail').value = em;
            document.getElementById('txtpassword').value = ''; // always blank — never prefill hash
            document.getElementById('btnSave').setAttribute('onclick', 'updateFunc(' + uid + ')');
            document.getElementById('userModal').style.display = 'flex';
        }

        function openVinylModal(id = '', title = '', artist = '', genre = '', price = '', stock = '', year = '', desc = '', coverSrc = '', vinylSrc = '') {
            document.getElementById('vinyl_id').value = id;
            document.getElementById('v_title').value = title;
            document.getElementById('v_artist').value = artist;
            document.getElementById('v_genre').value = genre;
            document.getElementById('v_price').value = price;
            document.getElementById('v_stock').value = stock;
            document.getElementById('v_year').value = year;
            document.getElementById('v_desc').value = desc;
            document.getElementById('vinylModalTitle').textContent = id ? 'Edit Record' : 'Add Record';
            // Reset file inputs
            document.getElementById('v_cover_image').value = '';
            document.getElementById('v_vinyl_image').value = '';
            // Show existing images if editing
            const cp = document.getElementById('v_cover_prev');
            const vp = document.getElementById('v_vinyl_prev');
            if (coverSrc) {
                cp.src = coverSrc;
                cp.style.display = 'block';
            } else {
                cp.style.display = 'none';
                cp.src = '';
            }
            if (vinylSrc) {
                vp.src = vinylSrc;
                vp.style.display = 'block';
            } else {
                vp.style.display = 'none';
                vp.src = '';
            }
            document.getElementById('vinylModal').style.display = 'flex';
        }

        // Image preview helper
        function previewImg(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);
            if (!file) {
                preview.style.display = 'none';
                return;
            }
            const allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!allowed.includes(file.type)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid File',
                    text: 'Only JPEG, PNG, WEBP, or GIF allowed.',
                    background: '#1a1a2e',
                    color: '#e0e0e0'
                });
                input.value = '';
                preview.style.display = 'none';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Too Large',
                    text: 'Max image size is 5 MB.',
                    background: '#1a1a2e',
                    color: '#e0e0e0'
                });
                input.value = '';
                preview.style.display = 'none';
                return;
            }
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }

        function saveVinyl() {
            const id = document.getElementById('vinyl_id').value;
            const title = document.getElementById('v_title').value.trim();
            const price = parseFloat(document.getElementById('v_price').value);
            const stock = document.getElementById('v_stock').value;
            const year = document.getElementById('v_year').value;

            // Client-side validation
            const errs = [];
            if (!title) errs.push('Title is required.');
            if (isNaN(price) || price <= 0) errs.push('Price must be greater than 0.');
            if (stock === '' || parseInt(stock) < 0) errs.push('Stock must be 0 or more.');
            const curYear = new Date().getFullYear();
            if (year && (year < 1900 || year > curYear)) errs.push('Year must be between 1900 and ' + curYear + '.');
            if (errs.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Input',
                    html: errs.map(e => '• ' + e).join('<br>'),
                    background: '#1a1a2e',
                    color: '#e0e0e0'
                });
                return;
            }

            const fd = new FormData();
            if (id) fd.append('v_id', id);
            fd.append('v_title', title);
            fd.append('v_artist', document.getElementById('v_artist').value);
            fd.append('v_genre', document.getElementById('v_genre').value);
            fd.append('v_price', price);
            fd.append('v_stock', parseInt(stock));
            fd.append('v_year', year);
            fd.append('v_desc', document.getElementById('v_desc').value.trim());
            const coverFile = document.getElementById('v_cover_image').files[0];
            const vinylFile = document.getElementById('v_vinyl_image').files[0];
            if (coverFile) fd.append('v_cover_image', coverFile);
            if (vinylFile) fd.append('v_vinyl_image', vinylFile);

            $.ajax({
                url: '../controllers/vinylController.php',
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data == 'true') {
                        Swal.fire({
                                icon: 'success',
                                title: id ? 'Updated!' : 'Added!',
                                text: 'Record saved.',
                                background: '#1a1a2e',
                                color: '#e0e0e0'
                            })
                            .then(() => location.reload(true));
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: data,
                            background: '#1a1a2e',
                            color: '#e0e0e0'
                        });
                    }
                },
                error: xhr => Swal.fire({
                    icon: 'error',
                    title: 'Error ' + xhr.status,
                    text: xhr.responseText,
                    background: '#1a1a2e',
                    color: '#e0e0e0'
                })
            });
        }

        function deleteVinyl(id) {
            Swal.fire({
                title: 'Delete this record?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#ff3c3c',
                background: '#1c1c1c',
                color: '#f0ede8'
            }).then(r => {
                if (r.isConfirmed) {
                    $.ajax({
                        url: '../controllers/vinylController.php',
                        type: 'POST',
                        data: {
                            v_delete_id: id
                        },
                        success: d => d == 'true' ? location.reload(true) : Swal.fire('Error', 'Delete failed: ' + d, 'error')
                    });
                }
            });
        }

        $(document).ready(function() {
            $('#usersTable').DataTable();
            $('#vinylsTable').DataTable();
        });
    </script>
</body>

</html>
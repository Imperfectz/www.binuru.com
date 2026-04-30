<?php
session_start();
require_once '../bl/userManagement.php';
require_once '../bl/vinylManagement.php';

$um  = new UserManagement();
$vm  = new VinylManagement();

$users      = $um->getUser();
$userCount  = count($users);
$vinylCount = $vm->countVinyls();
$orderCount = $vm->countOrders();
$revenue    = $vm->totalRevenue();

$revenueByMonth = $vm->getRevenueByMonth(6);
$ordersByMonth = $vm->getOrdersByMonth(6);
$orderStatuses = $vm->getOrderStatusDistribution();

$monthLabels = [];
$revenueData = [];
$orderData = [];

foreach ($revenueByMonth as $data) {
    $monthLabels[] = date('M', strtotime($data['month']));
    $revenueData[] = (int)$data['revenue'];
}

foreach ($ordersByMonth as $data) {
    $orderData[] = (int)$data['count'];
}

while (count($monthLabels) < 6) {
    array_unshift($monthLabels, date('M', strtotime('-' . (6 - count($monthLabels)) . ' months')));
    array_unshift($revenueData, 0);
    array_unshift($orderData, 0);
}

$statusLabels = [];
$statusCounts = [];
$statusColors = [];
foreach ($orderStatuses as $status) {
    $statusLabels[] = $status['status'];
    $statusCounts[] = (int)$status['count'];
    switch($status['status']) {
        case 'Completed': $statusColors[] = '#7df3c0'; break;
        case 'Shipped': $statusColors[] = '#f5e642'; break;
        case 'Cancelled': $statusColors[] = '#ff3c3c'; break;
        default: $statusColors[] = '#888';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>biniru — Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root{--black:#0d0d0d;--surface:#161616;--card:#1c1c1c;--border:#2a2a2a;--accent:#f5e642;--accent2:#ff3c3c;--accent3:#7df3c0;--white:#f0ede8;--muted:#555;}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        html,body{background:var(--black);color:var(--white);font-family:'DM Sans',sans-serif;min-height:100%;}

        .navbar{position:sticky;top:0;z-index:100;background:rgba(13,13,13,0.95);backdrop-filter:blur(16px);border-bottom:1px solid var(--border);padding:0 48px;height:62px;display:flex;align-items:center;justify-content:space-between;}
        .nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none;}
        .nav-logo img{height:30px;width:30px;object-fit:contain;filter:brightness(1.2);} 
        .nav-logo-text{font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:5px;color:var(--white);}
        .nav-links{display:flex;align-items:center;gap:28px;}
        .nav-links a{color:var(--muted);text-decoration:none;font-size:11px;letter-spacing:2px;text-transform:uppercase;transition:color .2s;}
        .nav-links a:hover{color:var(--white);}
        .nav-links a.active{color:var(--accent);}
        .nav-cta{background:var(--accent)!important;color:var(--black)!important;padding:9px 20px;border-radius:3px;font-weight:600!important;}

        .main{max-width:1140px;margin:0 auto;padding:48px 48px 80px;}
        .page-header{margin-bottom:44px;}
        .page-header .eyebrow{font-size:10px;letter-spacing:6px;text-transform:uppercase;color:var(--accent);margin-bottom:6px;}
        .page-header h1{font-family:'Bebas Neue',sans-serif;font-size:52px;letter-spacing:4px;}
        .page-header p{font-size:13px;color:var(--muted);margin-top:4px;}

        .stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--border);border:1px solid var(--border);border-radius:5px;overflow:hidden;margin-bottom:48px;}
        .stat-card{background:var(--card);padding:28px 28px 24px;position:relative;overflow:hidden;transition:background .2s;}
        .stat-card:hover{background:#202020;}
        .stat-top-bar{position:absolute;top:0;left:0;right:0;height:2px;}
        .stat-card:nth-child(1) .stat-top-bar{background:var(--accent);}
        .stat-card:nth-child(2) .stat-top-bar{background:var(--accent2);}
        .stat-card:nth-child(3) .stat-top-bar{background:var(--accent3);}
        .stat-card:nth-child(4) .stat-top-bar{background:#a78bfa;}
        .stat-label{font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--muted);margin-bottom:16px;}
        .stat-value{font-family:'Bebas Neue',sans-serif;font-size:52px;letter-spacing:2px;line-height:1;color:var(--white);margin-bottom:4px;}
        .stat-card:nth-child(1) .stat-value{color:var(--accent);}
        .stat-card:nth-child(2) .stat-value{color:var(--accent2);}
        .stat-card:nth-child(3) .stat-value{color:var(--accent3);}
        .stat-card:nth-child(4) .stat-value{color:#a78bfa;}
        .stat-sub{font-size:11px;color:var(--muted);letter-spacing:.5px;}

        /* decorative mini disc */
        .disc-deco{position:absolute;right:-16px;bottom:-16px;width:72px;height:72px;border-radius:50%;opacity:.06;background:conic-gradient(#fff 0deg,rgba(255,255,255,.4) 60deg,#fff 120deg,rgba(255,255,255,.4) 180deg,#fff 240deg,rgba(255,255,255,.4) 300deg,#fff 360deg);}

        /* SECTION HEAD */
        .section-head{display:flex;align-items:center;gap:16px;margin-bottom:18px;}
        .section-head h2{font-family:'Bebas Neue',sans-serif;font-size:20px;letter-spacing:4px;white-space:nowrap;}
        .section-head hr{flex:1;border:none;border-top:1px solid var(--border);}
        .section-head a{font-size:10px;letter-spacing:2px;color:var(--accent);text-decoration:none;text-transform:uppercase;white-space:nowrap;}

        /* TWO-COL */
        .two-col{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:32px;}

        /* TABLE */
        .table-wrap{background:var(--card);border:1px solid var(--border);border-radius:4px;overflow:hidden;}
        table{width:100%;border-collapse:collapse;}
        thead th{padding:12px 18px;text-align:left;font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--muted);font-weight:400;background:var(--surface);border-bottom:1px solid var(--border);}
        tbody tr{border-bottom:1px solid #1e1e1e;transition:background .15s;}
        tbody tr:last-child{border-bottom:none;}
        tbody tr:hover{background:#1e1e1e;}
        tbody td{padding:13px 18px;font-size:13px;}
        .pill{display:inline-block;padding:3px 10px;border-radius:2px;font-size:9px;letter-spacing:2px;text-transform:uppercase;font-weight:600;}
        .pill-green{background:rgba(125,243,192,.12);color:var(--accent3);}
        .pill-yellow{background:rgba(245,230,66,.12);color:var(--accent);}
        .pill-red{background:rgba(255,60,60,.12);color:var(--accent2);}
        .pill-muted{background:rgba(255,255,255,.06);color:var(--muted);}

        /* SPINNING VINYL ACCENT ROW */
        .vinyl-accent-row{display:flex;gap:16px;align-items:center;padding:36px 0 0;}
        .mini-disc{width:56px;height:56px;border-radius:50%;background:conic-gradient(from 0deg,#1a1a1a 0deg,#252525 60deg,#1a1a1a 120deg,#252525 180deg,#1a1a1a 240deg,#252525 300deg,#1a1a1a 360deg);display:flex;align-items:center;justify-content:center;}
        .mini-disc:nth-child(1){animation:spin 5s linear infinite;}
        .mini-disc:nth-child(2){animation:spin 7s linear infinite;opacity:.6;}
        .mini-disc:nth-child(3){animation:spin 4s linear infinite reverse;opacity:.35;}
        .mini-disc-center{width:18px;height:18px;border-radius:50%;background:var(--card);}
        @keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}

        /* CHARTS */
        .charts-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:48px;}
        .chart-card{background:var(--card);border:1px solid var(--border);border-radius:4px;padding:24px;position:relative;}
        .chart-card h3{font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:2px;margin-bottom:20px;color:var(--white);}
        .chart-container{position:relative;height:280px;}

        /* STATUS EDITOR */
        .status-select{
            -webkit-appearance:none;appearance:none;
            border:1px solid transparent;border-radius:2px;
            background:transparent;color:inherit;
            font-size:9px;letter-spacing:2px;text-transform:uppercase;font-weight:600;
            font-family:'DM Sans',sans-serif;cursor:pointer;padding:3px 8px;
            transition:border-color .2s,background .2s;
        }
        .status-select:hover,.status-select:focus{border-color:var(--border);background:var(--surface);outline:none;}
        .status-select.saving{opacity:.5;pointer-events:none;}
        .status-select option{background:#1c1c1c;color:#f0ede8;}

        /* flash animation when saved */
        @keyframes saved-flash{0%{opacity:1}40%{opacity:.3}100%{opacity:1}}
        .status-select.saved{animation:saved-flash .4s ease;}

        /* inline save indicator */
        .save-dot{display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--accent3);
            margin-left:6px;opacity:0;transition:opacity .3s;vertical-align:middle;}
        .save-dot.visible{opacity:1;}
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
            <a href="adminPage.php">Admin</a>
            <a href="Dashboard.php" class="active">Dashboard</a>
            <a href="loginPage.php" class="nav-cta">Logout</a>
        </div>
    </nav>

    <main class="main">
        <div class="page-header">
            <p class="eyebrow">Overview</p>
            <h1>DASHBOARD</h1>
            <p>Live stats from your biniru store.</p>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-top-bar"></div>
                <div class="stat-label">Users</div>
                <div class="stat-value"><?= str_pad($userCount,  2,'0',STR_PAD_LEFT) ?></div>
                <div class="stat-sub">Registered accounts</div>
                <div class="disc-deco"></div>
            </div>
            <div class="stat-card">
                <div class="stat-top-bar"></div>
                <div class="stat-label">Vinyl Records</div>
                <div class="stat-value"><?= str_pad($vinylCount, 2,'0',STR_PAD_LEFT) ?></div>
                <div class="stat-sub">In catalog</div>
                <div class="disc-deco"></div>
            </div>
            <div class="stat-card">
                <div class="stat-top-bar"></div>
                <div class="stat-label">Total Sales</div>
                <div class="stat-value"><?= str_pad($orderCount, 2,'0',STR_PAD_LEFT) ?></div>
                <div class="stat-sub">Orders placed</div>
                <div class="disc-deco"></div>
            </div>
            <div class="stat-card">
                <div class="stat-top-bar"></div>
                <div class="stat-label">Revenue</div>
                <div class="stat-value" style="font-size:36px;">₱<?= number_format($revenue, 0) ?></div>
                <div class="stat-sub">Total earnings</div>
                <div class="disc-deco"></div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h3>REVENUE TREND</h3>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3>SALES BY MONTH</h3>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3>ORDER STATUS</h3>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="two-col">
            <div>
                <div class="section-head">
                    <h2>RECENT USERS</h2>
                    <hr>
                    <a href="adminPage.php">View All →</a>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Name</th><th>Username</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php
                            $recent = array_slice($users, 0, 6);
                            if (!empty($recent)): foreach($recent as $u): ?>
                            <tr>
                                <td><?= htmlspecialchars($u['firstName'].' '.$u['lastName']) ?></td>
                                <td style="color:var(--muted)">@<?= htmlspecialchars($u['userName']) ?></td>
                                <td><span class="pill pill-green">Active</span></td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="3" style="color:var(--muted);text-align:center;padding:28px">No users yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="section-head">
                    <h2>RECENT ORDERS</h2>
                    <hr>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>#</th><th>Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php
                            $orders = $vm->getRecentOrders(6);
                            if (!empty($orders)): foreach($orders as $o):
                                $pillClass = match($o['status']) {
                                    'Completed' => 'pill-green',
                                    'Shipped'   => 'pill-yellow',
                                    'Cancelled' => 'pill-red',
                                    default     => 'pill-muted'
                                };
                                $selectColor = match($o['status']) {
                                    'Completed' => 'color:var(--accent3)',
                                    'Shipped'   => 'color:var(--accent)',
                                    'Cancelled' => 'color:var(--accent2)',
                                    default     => 'color:var(--muted)'
                                };
                            ?>
                            <tr>
                                <td style="color:var(--muted)">#<?= $o['order_id'] ?></td>
                                <td>₱<?= number_format($o['total_amount'], 2) ?></td>
                                <td>
                                    <select class="status-select pill <?= $pillClass ?>"
                                            style="<?= $selectColor ?>"
                                            data-order-id="<?= $o['order_id'] ?>"
                                            data-current="<?= $o['status'] ?>">
                                        <?php foreach(['Pending','Shipped','Completed','Cancelled'] as $s): ?>
                                        <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="save-dot" id="dot-<?= $o['order_id'] ?>"></span>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="3" style="color:var(--muted);text-align:center;padding:28px">No orders yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- spinning vinyl accent -->
        <div class="vinyl-accent-row">
            <div class="mini-disc"><div class="mini-disc-center"></div></div>
            <div class="mini-disc"><div class="mini-disc-center"></div></div>
            <div class="mini-disc"><div class="mini-disc-center"></div></div>
        </div>

    </main>

    <script>
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#f0ede8',
                        font: { family: "'DM Sans', sans-serif", size: 11 }
                    }
                }
            },
            scales: {
                y: {
                    ticks: { color: '#555' },
                    grid: { color: '#2a2a2a' },
                    border: { color: '#2a2a2a' }
                },
                x: {
                    ticks: { color: '#555' },
                    grid: { color: '#2a2a2a' },
                    border: { color: '#2a2a2a' }
                }
            }
        };

        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($monthLabels) ?>,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: <?= json_encode($revenueData) ?>,
                    borderColor: '#f5e642',
                    backgroundColor: 'rgba(245, 230, 66, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#f5e642',
                    pointBorderColor: '#1c1c1c',
                    pointBorderWidth: 2
                }]
            },
            options: chartOptions
        });

        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($monthLabels) ?>,
                datasets: [{
                    label: 'Orders',
                    data: <?= json_encode($orderData) ?>,
                    backgroundColor: ['#7df3c0','#7df3c0','#7df3c0','#7df3c0','#7df3c0','#7df3c0'],
                    borderRadius: 3,
                    borderSkipped: false
                }]
            },
            options: chartOptions
        });

        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($statusLabels) ?>,
                datasets: [{
                    data: <?= json_encode($statusCounts) ?>,
                    backgroundColor: <?= json_encode($statusColors) ?>,
                    borderColor: '#1c1c1c',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#f0ede8',
                            font: { family: "'DM Sans', sans-serif", size: 11 },
                            padding: 16
                        }
                    }
                }
            }
        });

        // ── STATUS COLOUR MAP ──
        const STATUS_COLORS = {
            Completed: '#7df3c0',
            Shipped:   '#f5e642',
            Cancelled: '#ff3c3c',
            Pending:   '#888'
        };
        const PILL_CLASSES = {
            Completed: 'pill-green',
            Shipped:   'pill-yellow',
            Cancelled: 'pill-red',
            Pending:   'pill-muted'
        };
        const SELECT_COLORS = {
            Completed: 'color:var(--accent3)',
            Shipped:   'color:var(--accent)',
            Cancelled: 'color:var(--accent2)',
            Pending:   'color:var(--muted)'
        };

        function refreshStatusChart(statusRows) {
            const labels = statusRows.map(r => r.status);
            const counts = statusRows.map(r => parseInt(r.count));
            const colors = statusRows.map(r => STATUS_COLORS[r.status] || '#888');
            statusChart.data.labels = labels;
            statusChart.data.datasets[0].data   = counts;
            statusChart.data.datasets[0].backgroundColor = colors;
            statusChart.update();
        }

        // ── INLINE STATUS EDITOR ──
        document.querySelectorAll('.status-select').forEach(sel => {
            sel.addEventListener('change', async function () {
                const orderId = this.dataset.orderId;
                const newStatus = this.value;
                const dot = document.getElementById('dot-' + orderId);

                // Swap pill class immediately for snappy feel
                this.className = 'status-select pill ' + (PILL_CLASSES[newStatus] || 'pill-muted');
                this.style.cssText = SELECT_COLORS[newStatus] || 'color:var(--muted)';
                this.classList.add('saving');

                try {
                    const res  = await fetch('../controllers/vinylController.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-Action': 'updateOrderStatus' },
                        body: JSON.stringify({ order_id: orderId, status: newStatus })
                    });
                    const data = await res.json();

                    if (data.success) {
                        // Animate saved dot
                        dot.classList.add('visible');
                        setTimeout(() => dot.classList.remove('visible'), 1400);

                        // Live-update status chart
                        refreshStatusChart(data.statusRows);

                        // Update revenue stat card if it changed (cancelled orders excluded)
                        const revEl = document.querySelector('.stat-card:nth-child(4) .stat-value');
                        if (revEl) {
                            revEl.textContent = '₱' + Math.round(data.totalRevenue).toLocaleString();
                        }

                        this.dataset.current = newStatus;
                    } else {
                        // Revert on failure
                        this.value = this.dataset.current;
                        alert(data.message || 'Update failed');
                    }
                } catch (err) {
                    this.value = this.dataset.current;
                    alert('Network error. Please try again.');
                } finally {
                    this.classList.remove('saving');
                }
            });
        });
    </script>
</body>

</html>

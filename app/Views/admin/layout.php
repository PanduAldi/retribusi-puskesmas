<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin' ?> - Retribusi Puskesmas</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-dark: #0a58ca;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --body-bg: #f0f2f5;
            --navbar-bg: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: var(--dark-color);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: #1a237e;
            color: #fff;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1001;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 25px 20px;
            font-size: 1.2rem;
            font-weight: 800;
            background: rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-menu { list-style: none; padding: 15px 0; }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 25px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: 0.3s;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .sidebar-menu li a:hover, .sidebar-menu li a.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left: 4px solid var(--warning-color);
        }

        .sidebar-menu li a i { width: 20px; text-align: center; }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
        }

        /* Navbar (Header) */
        .navbar {
            background: #fff;
            height: 70px;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .user-nav { display: flex; align-items: center; gap: 20px; }
        .user-info { text-align: right; }
        .role-badge {
            background: var(--light-color);
            color: var(--primary-color);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .content-body { padding: 30px; }

        /* Components */
        .card {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .card-header {
            margin-bottom: 25px;
            border-bottom: 2px solid var(--light-color);
            padding-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 { color: #1a237e; font-weight: 700; }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-primary { background: var(--primary-color); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-success { background: var(--success-color); color: #fff; }
        .btn-danger { background: var(--danger-color); color: #fff; }
        .btn-warning { background: var(--warning-color); color: #212529; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f0f2f5; }
        th { background: #f8f9fa; color: #1a237e; font-weight: 700; font-size: 0.9rem; }
        tr:hover { background-color: #fcfdfe; }

        /* Forms */
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; font-size: 0.9rem; }
        input[type="text"], input[type="password"], input[type="number"], select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: 0.2s;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        /* Alerts */
        .alert {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .alert-success { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .alert-danger { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: 0.3s; width: 240px; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .navbar { padding: 0 15px; }
            .mobile-toggle { display: block !important; }
        }

        .mobile-toggle {
            display: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #1a237e;
            padding: 10px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.show { display: block; }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-hospital-user"></i>
            <span>RETRIBUSI PKM</span>
        </div>
        <ul class="sidebar-menu">
            <?php if (in_array(session()->get('role'), ['admin_kabupaten', 'admin_puskesmas'])) : ?>
            <li>
                <a href="<?= base_url('admin/dashboard') ?>" class="<?= strpos(current_url(), 'dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </li>
            <?php endif; ?>

            <li>
                <a href="<?= base_url('eretribusi/transaksi') ?>" class="<?= strpos(current_url(), 'transaksi') ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice-dollar"></i> Transaksi
                </a>
            </li>

            <?php if (session()->get('role') === 'admin_kabupaten') : ?>
            <li>
                <a href="<?= base_url('admin/puskesmas') ?>" class="<?= strpos(current_url(), 'puskesmas') && !strpos(current_url(), 'jenis') ? 'active' : '' ?>">
                    <i class="fas fa-hospital"></i> Data Puskesmas
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/puskesmas/jenis') ?>" class="<?= strpos(current_url(), 'puskesmas/jenis') ? 'active' : '' ?>">
                    <i class="fas fa-map-marked-alt"></i> Mapping Layanan PKM
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/jenis-retribusi') ?>" class="<?= strpos(current_url(), 'jenis-retribusi') ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i> Master Layanan & Tarif
                </a>
            </li>
            <?php endif; ?>

            <?php if (in_array(session()->get('role'), ['admin_kabupaten', 'admin_puskesmas'])) : ?>
            <li>
                <a href="<?= base_url('admin/users') ?>" class="<?= strpos(current_url(), 'users') ? 'active' : '' ?>">
                    <i class="fas fa-users-cog"></i> Manajemen User
                </a>
            </li>
            <?php endif; ?>
            <!-- cek status billing -->
            <li>
                <a href="<?= base_url('eretribusi/billing/cek-status') ?>" class="<?= strpos(current_url(), 'cek-status') ? 'active' : '' ?>">
                    <i class="fas fa-search"></i> Cek Status Billing
                </a>

            <li style="margin-top: 20px;">
                <a href="<?= base_url('logout') ?>" style="color: #ff6b6b;">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </li>
        </ul>
    </div>

    <!-- Modals Section -->
    <?= $this->renderSection('modals') ?>

    <div class="main-content">
        <div class="navbar">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="mobile-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </div>
                <h2 style="font-size: 1.2rem; font-weight: 700; color: #1a237e;"><?= $title ?? 'Admin Panel' ?></h2>
            </div>
            <div class="user-nav">
                <div class="user-info">
                    <div style="font-weight: 700; font-size: 0.9rem;"><?= session()->get('nama') ?></div>
                    <div class="role-badge"><?= str_replace('_', ' ', session()->get('role') ?? 'User') ?></div>
                </div>
                <div style="width: 40px; height: 40px; background: #e0e0e0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #999;">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>

        <div class="content-body">
            <?php if (session()->getFlashdata('notif_sukses')) : ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= session()->getFlashdata('notif_sukses') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('notif_gagal')) : ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= session()->getFlashdata('notif_gagal') ?>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
    </script>
</body>
</html>

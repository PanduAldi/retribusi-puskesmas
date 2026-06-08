<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - Retribusi Puskesmas</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: var(--dark-color);
            line-height: 1.6;
        }

        .navbar {
            background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
        }

        .user-nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            font-size: 0.9rem;
            text-align: right;
        }

        .role-badge {
            background: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .logout-btn {
            background: var(--danger-color);
            color: #fff;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: 0.2s;
        }

        .logout-btn:hover { background: #bb2d3b; }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 50px;
        }

        .welcome-section h1 {
            font-size: 2.5rem;
            color: #1a237e;
            margin-bottom: 10px;
            font-weight: 800;
        }

        .welcome-section p {
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .alert-danger { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .menu-item {
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: var(--dark-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .menu-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            border-color: var(--primary-color);
        }

        .icon-box {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: var(--light-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 2rem;
            color: var(--primary-color);
            transition: 0.3s;
        }

        .menu-item:hover .icon-box {
            background: var(--primary-color);
            color: #fff;
        }

        .menu-item h3 {
            margin-bottom: 12px;
            color: #1a237e;
            font-weight: 700;
        }

        .menu-item p {
            color: var(--secondary-color);
            font-size: 0.95rem;
        }

        .card {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-top: 50px;
            border-left: 5px solid var(--warning-color);
        }

        .card h3 { margin-bottom: 15px; color: #1a237e; }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: 0.2s;
            margin-top: 15px;
        }

        .btn-primary { background: var(--primary-color); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }

        @media (max-width: 768px) {
            .navbar { padding: 1rem; }
            .user-info { display: none; }
            .welcome-section h1 { font-size: 2rem; }
            .menu-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-brand">
            <i class="fas fa-hospital-user"></i>
            <span>RETRIBUSI PUSKESMAS</span>
        </div>
        <div class="user-nav">
            <div class="user-info">
                <div style="font-weight: 600;"><?= session()->get('nama') ?></div>
                <div class="role-badge"><?= str_replace('_', ' ', session()->get('role') ?? 'User') ?></div>
            </div>
            <a href="<?= base_url('logout') ?>" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> <span>Keluar</span>
            </a>
        </div>
    </div>

    <div class="container">
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

        <div class="welcome-section">
            <h1>Selamat Datang</h1>
            <p>Sistem Informasi Retribusi Puskesmas Terintegrasi</p>
        </div>

        <div class="menu-grid">
            <a href="<?= base_url('eretribusi/transaksi/new') ?>" class="menu-item">
                <div class="icon-box">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h3>Input Transaksi</h3>
                <p>Mencatat layanan retribusi baru dan membuat tagihan pembayaran.</p>
            </a>

            <?php if (session()->get('role') === 'admin_puskesmas') : ?>
            <a href="#" class="menu-item">
                <div class="icon-box">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Laporan Unit</h3>
                <p>Melihat rekap pendapatan retribusi di unit kerja Anda secara real-time.</p>
            </a>
            <?php endif; ?>

            <a href="<?= base_url('eretribusi/billing/cek-status') ?>" class="menu-item">
                <div class="icon-box">
                    <i class="fas fa-search-dollar"></i>
                </div>
                <h3>Cek Status Billing</h3>
                <p>Verifikasi status pembayaran ID Billing yang sudah diterbitkan.</p>
            </a>
        </div>

        <div class="card">
            <h3><i class="fas fa-flask"></i> Mode Pengujian (Testing)</h3>
            <p>Gunakan link di bawah ini untuk mensimulasikan alur sistem (Input -> Konfirmasi -> Generate):</p>
            <a href="<?= base_url('eretribusi/konfirmasi/INV-2026-001') ?>" class="btn btn-primary">
                <i class="fas fa-vial"></i> Test Flow INV-2026-001
            </a>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin' ?> - Retribusi Puskesmas</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; display: flex; background-color: #f4f7f6; }
        .sidebar { width: 260px; background: #2c3e50; color: #fff; min-height: 100vh; position: fixed; }
        .sidebar-header { padding: 20px; font-size: 1.2em; font-weight: bold; background: #1a252f; text-align: center; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li a { display: block; padding: 15px 20px; color: #bdc3c7; text-decoration: none; transition: 0.3s; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: #34495e; color: #fff; border-left: 4px solid #3498db; }

        .main-content { flex: 1; margin-left: 260px; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: #fff; padding: 15px 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin: -30px -30px 30px -30px; }
        .user-info { font-size: 0.9em; }
        .logout-btn { color: #e74c3c; text-decoration: none; font-weight: bold; margin-left: 15px; }

        .card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .btn { padding: 10px 15px; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 14px; display: inline-block; border: none; }
        .btn-primary { background: #3498db; color: #fff; }
        .btn-success { background: #2ecc71; color: #fff; }
        .btn-danger { background: #e74c3c; color: #fff; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #333; font-weight: 600; }

        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #444; }
        input[type="text"], input[type="password"], input[type="number"], select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">RETRIBUSI PKM</div>
        <ul class="sidebar-menu">
            <li><a href="<?= base_url('admin/dashboard') ?>" class="<?= strpos(current_url(), 'dashboard') ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="<?= base_url('admin/puskesmas') ?>" class="<?= strpos(current_url(), 'puskesmas') ? 'active' : '' ?>">Data Puskesmas</a></li>
            <li><a href="<?= base_url('admin/jenis-retribusi') ?>" class="<?= strpos(current_url(), 'jenis-retribusi') ? 'active' : '' ?>">Jenis Retribusi</a></li>
            <li><a href="<?= base_url('admin/tarif') ?>" class="<?= strpos(current_url(), 'tarif') ? 'active' : '' ?>">Atur Tarif</a></li>
            <li><a href="<?= base_url('admin/users') ?>" class="<?= strpos(current_url(), 'users') ? 'active' : '' ?>">Manajemen User</a></li>
            <li><a href="<?= base_url('logout') ?>" style="color: #e74c3c;">Keluar</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="header">
            <h2 style="margin:0;"><?= $title ?? 'Admin' ?></h2>
            <div class="user-info">
                Role: <strong><?= strtoupper(session()->get('role')) ?></strong> |
                User: <strong><?= session()->get('nama') ?></strong>
            </div>
        </div>

        <?php if (session()->getFlashdata('notif_sukses')) : ?>
            <div class="alert alert-success"><?= session()->getFlashdata('notif_sukses') ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('notif_gagal')) : ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('notif_gagal') ?></div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</body>
</html>

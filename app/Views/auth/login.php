<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Retribusi Puskesmas</title>
    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/brebes.png') ?>">
    <style>
        body { font-family: sans-serif; background-color: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .logos { display: flex; justify-content: center; align-items: center; gap: 20px; margin-bottom: 25px; }
        .logos img { height: 50px; object-fit: contain; }
        .logo-bank { background-color: #f5f5f500; padding: 5px; border-radius: 4px; } /* Background for white logo */
        h2 { text-align: center; color: #333; margin-bottom: 30px; font-size: 1.5rem; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; color: #666; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background-color: #007bff; border: none; border-radius: 4px; color: #fff; font-size: 16px; cursor: pointer; font-weight: bold; }
        .btn:hover { background-color: #0056b3; }
        .notif { padding: 10px; margin-bottom: 20px; border-radius: 4px; text-align: center; font-size: 14px; }
        .notif-gagal { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logos">
            <img src="<?= base_url('assets/img/brebes.png') ?>" alt="Logo Brebes">
            <img src="<?= base_url('assets/img/brebes_beres_mh.png') ?>" alt="Logo Brebes Beres">
            <img src="<?= base_url('assets/img/logo-bank-jateng-white.png') ?>" alt="Logo Bank Jateng" class="logo-bank">
        </div>
        <h2>eRetribusi Puskesmas</h2>

        <?php if (session()->getFlashdata('notif_gagal')) : ?>
            <div class="notif notif-gagal">
                <?= session()->getFlashdata('notif_gagal') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>

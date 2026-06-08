<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="card" style="max-width: 600px; margin: auto;">
    <h3><?= $title ?></h3>
    <form action="<?= base_url('admin/users/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" required placeholder="Contoh: Petugas Loket A">
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Contoh: petugas_katang">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter">
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" required>
                <option value="">Pilih Role</option>
                <option value="admin_kabupaten">Admin Kabupaten</option>
                <option value="admin_puskesmas">Admin Puskesmas</option>
                <option value="petugas">Petugas Puskesmas</option>
                <option value="viewer">Viewer / Bendahara</option>
            </select>
        </div>
        <div class="form-group">
            <label>Unit Kerja / Puskesmas</label>
            <select name="id_puskesmas">
                <option value="">Semua Unit (Khusus Admin Kabupaten / Global)</option>
                <?php foreach ($puskesmas as $p) : ?>
                <option value="<?= $p['id'] ?>"><?= esc($p['prasarana']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Simpan Data</button>
            <a href="<?= base_url('admin/users') ?>" class="btn btn-danger">Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

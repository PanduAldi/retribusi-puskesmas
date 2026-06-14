<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="card" style="max-width: 600px; margin: auto;">
    <h3><?= $title ?></h3>
    <form action="<?= isset($user) ? base_url('admin/users/update/' . $user['id']) : base_url('admin/users/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" value="<?= old('nama', $user['nama'] ?? '') ?>" required placeholder="Contoh: Petugas Loket A">
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?= old('username', $user['username'] ?? '') ?>" required placeholder="Contoh: petugas_katang">
        </div>
        <div class="form-group">
            <label>Password <?= isset($user) ? '<small class="text-muted">(Kosongkan jika tidak ingin mengubah password)</small>' : '' ?></label>
            <input type="password" name="password" <?= isset($user) ? '' : 'required' ?> minlength="6" placeholder="Minimal 6 karakter">
        </div>

        <?php if (session()->get('role') === 'admin_kabupaten') : ?>
        <div class="form-group">
            <label>Role</label>
            <select name="role" required>
                <option value="">Pilih Role</option>
                <option value="admin_kabupaten" <?= old('role', $user['role'] ?? '') === 'admin_kabupaten' ? 'selected' : '' ?>>Admin Kabupaten</option>
                <option value="admin_puskesmas" <?= old('role', $user['role'] ?? '') === 'admin_puskesmas' ? 'selected' : '' ?>>Admin Puskesmas</option>
                <option value="petugas" <?= old('role', $user['role'] ?? '') === 'petugas' ? 'selected' : '' ?>>Petugas Puskesmas</option>
                <option value="viewer" <?= old('role', $user['role'] ?? '') === 'viewer' ? 'selected' : '' ?>>Viewer / Bendahara</option>
            </select>
        </div>

        <div class="form-group">
            <label>Unit Kerja / Puskesmas</label>
            <select name="id_puskesmas">
                <option value="">Semua Unit (Khusus Admin Kabupaten / Global)</option>
                <?php foreach ($puskesmas as $p) : ?>
                <option value="<?= $p['id'] ?>" <?= old('id_puskesmas', $user['id_puskesmas'] ?? '') == $p['id'] ? 'selected' : '' ?>><?= esc($p['prasarana']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php else: ?>
            <!-- Admin Puskesmas can't change role or unit -->
            <input type="hidden" name="role" value="<?= $user['role'] ?? 'petugas' ?>">
            <div class="form-group">
                <label>Role</label>
                <div style="padding: 10px; background: #f8f9fa; border-radius: 6px; border: 1px solid #ddd; color: #666;">
                    <?= strtoupper($user['role'] ?? 'Petugas Puskesmas') ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($user)) : ?>
        <div class="form-group">
            <label>Status Akun</label>
            <select name="is_active" required>
                <option value="1" <?= old('is_active', $user['is_active'] ?? '') == 1 ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= old('is_active', $user['is_active'] ?? '') == 0 ? 'selected' : '' ?>>Nonaktif (Blokir)</option>
            </select>
        </div>
        <?php endif; ?>

        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">
                <?= isset($user) ? 'Simpan Perubahan' : 'Tambah User' ?>
            </button>
            <a href="<?= base_url('admin/users') ?>" class="btn btn-danger">Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

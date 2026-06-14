<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="card">
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin:0;">Manajemen Pengguna Sistem</h3>
        <a href="<?= base_url('admin/users/new') ?>" class="btn btn-primary">Tambah User</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Role</th>
                <th>Unit Kerja (Puskesmas)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u) : ?>
            <tr>
                <td><?= esc($u['nama']) ?></td>
                <td><?= esc($u['username']) ?></td>
                <td><strong><?= strtoupper($u['role']) ?></strong></td>
                <td><?= $u['nama_puskesmas'] ?: '<span style="color:#999;">Kabupaten (Global)</span>' ?></td>
                <td><?= $u['is_active'] ? '<span style="color:green; font-weight:700;">Aktif</span>' : '<span style="color:red; font-weight:700;">Nonaktif</span>' ?></td>
                <td>
                    <div style="display:flex; gap: 8px;">
                        <a href="<?= base_url('admin/users/edit/' . $u['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="<?= base_url('admin/users/delete/' . $u['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menonaktifkan user ini demi keamanan data?')">Hapus</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

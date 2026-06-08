<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="card">
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin:0;">Data Puskesmas / Unit Layanan</h3>
        <a href="<?= base_url('admin/puskesmas/new') ?>" class="btn btn-primary">Tambah Puskesmas</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Puskesmas</th>
                <th>Kode Retribusi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($puskesmas as $p) : ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= esc($p['prasarana']) ?></td>
                <td><?= esc($p['kode_retribusi']) ?></td>
                <td>
                    <a href="<?= base_url('admin/puskesmas/edit/' . $p['id']) ?>" class="btn btn-success btn-sm">Edit</a>
                    <a href="<?= base_url('admin/puskesmas/delete/' . $p['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

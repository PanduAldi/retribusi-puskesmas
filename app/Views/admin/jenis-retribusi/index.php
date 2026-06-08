<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <div class="card">
        <h3>Daftar Jenis Layanan Retribusi</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Jenis Layanan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jenis as $j) : ?>
                <tr>
                    <td><?= $j['id'] ?></td>
                    <td>
                        <form action="<?= base_url('admin/jenis-retribusi/update/' . $j['id']) ?>" method="post" style="display:flex; gap: 10px;">
                            <?= csrf_field() ?>
                            <input type="text" name="jenis" value="<?= esc($j['jenis']) ?>" required style="padding: 5px;">
                            <button type="submit" class="btn btn-success btn-sm">Update</button>
                        </form>
                    </td>
                    <td>
                        <a href="<?= base_url('admin/jenis-retribusi/delete/' . $j['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>Tambah Jenis Baru</h3>
        <form action="<?= base_url('admin/jenis-retribusi/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Nama Layanan</label>
                <input type="text" name="jenis" required placeholder="Contoh: Rawat Jalan Umum">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Tambah Data</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

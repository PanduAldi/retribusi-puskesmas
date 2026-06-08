<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="card-grid">
    <div class="card">
        <h3>Total Puskesmas</h3>
        <div class="value"><?= $total_puskesmas ?></div>
    </div>
    <div class="card">
        <h3>Total Transaksi</h3>
        <div class="value"><?= $total_transaksi ?></div>
    </div>
    <div class="card">
        <h3>Total Pendapatan</h3>
        <div class="value">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
    </div>
</div>

<div class="card">
    <h3>Daftar Puskesmas</h3>
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
            <?php foreach ($puskesmas_list as $p) : ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= esc($p['prasarana']) ?></td>
                <td><?= esc($p['kode_retribusi']) ?></td>
                <td><a href="<?= base_url('admin/puskesmas/edit/' . $p['id']) ?>" class="btn btn-primary btn-sm">Detail</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <div class="card">
    <h3>Daftar Tarif Retribusi</h3>
    <?php
        $grouped = [];
        foreach ($tarif as $t) {
            $grouped[$t['nama_puskesmas']][] = $t;
        }
    ?>
    <?php foreach ($grouped as $puskesmasName => $items): ?>
        <h4 style="margin-top:12px; font-weight:600; color:#1a237e;"><?= esc($puskesmasName) ?></h4>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jenis Layanan</th>
                    <th>Tarif</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $t): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><?= esc($t['nama_jenis']) ?></td>
                    <td>Rp <?= number_format($t['tarif'], 0, ',', '.') ?></td>
                    <td>
                        <a href="<?= base_url('admin/tarif/delete/' . $t['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus tarif ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>

    <div class="card">
        <h3>Atur Tarif Baru / Update</h3>
        <form action="<?= base_url('admin/tarif/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Puskesmas</label>
                <select name="id_puskesmas" required>
                    <option value="">Pilih Puskesmas</option>
                    <?php foreach ($puskesmas as $p) : ?>
                    <option value="<?= $p['id'] ?>"><?= esc($p['prasarana']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Jenis Layanan</label>
                <select name="id_jenis" required>
                    <option value="">Pilih Jenis</option>
                    <?php foreach ($jenis as $j) : ?>
                    <option value="<?= $j['id'] ?>"><?= esc($j['jenis']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Nominal Tarif (Rp)</label>
                <input type="number" name="tarif" required min="0" value="0">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Tarif</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

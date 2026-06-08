<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="card" style="max-width: 600px; margin: auto;">
    <h3><?= $title ?></h3>
    <form action="<?= isset($puskesmas) ? base_url('admin/puskesmas/update/' . $puskesmas['id']) : base_url('admin/puskesmas/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Nama Puskesmas / Prasarana</label>
            <input type="text" name="prasarana" value="<?= isset($puskesmas) ? esc($puskesmas['prasarana']) : '' ?>" required placeholder="Contoh: Puskesmas Ketanggungan">
        </div>
        <div class="form-group">
            <label>Kode Retribusi</label>
            <input type="text" name="kode_retribusi" value="<?= isset($puskesmas) ? esc($puskesmas['kode_retribusi']) : '' ?>" required placeholder="Contoh: PKM001">
        </div>
        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Simpan Data</button>
            <a href="<?= base_url('admin/puskesmas') ?>" class="btn btn-danger">Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

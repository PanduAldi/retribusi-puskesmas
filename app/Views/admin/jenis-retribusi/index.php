<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<style>
    /* Autocomplete Datalist styling */
    .autocomplete-wrapper {
        position: relative;
    }

    /* Modal CSS */
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
    }

    .modal-dialog {
        position: relative;
        width: auto;
        margin: 50px auto;
        max-width: 500px;
    }

    .modal-content {
        position: relative;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,.2);
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,.5);
        padding: 30px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .modal-header h3 {
        margin: 0;
        color: #1a237e;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        font-weight: 700;
        color: #aaa;
        cursor: pointer;
    }

    .close-modal:hover {
        color: #000;
    }
</style>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3>Daftar Jenis Layanan & Tarif Retribusi</h3>
        <button type="button" class="btn btn-primary" onclick="openModal()">
            <i class="fas fa-plus"></i> Tambah Layanan Baru
        </button>
    </div>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 200px;">Kategori</th>
                    <th>Nama Jenis Layanan</th>
                    <th style="width: 150px;">Tarif (Rp)</th>
                    <th style="width: 180px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Collect existing categories for datalist autocompletes
                $existingCategories = [];
                foreach ($jenis as $j) {
                    if (!empty($j['kategori'])) {
                        $existingCategories[] = $j['kategori'];
                    }
                }
                $existingCategories = array_unique($existingCategories);
                sort($existingCategories);
                ?>

                <!-- Shared Datalist for Autocomplete Kategori -->
                <datalist id="kategori-list">
                    <?php foreach ($existingCategories as $cat) : ?>
                        <option value="<?= esc($cat) ?>">
                    <?php endforeach; ?>
                </datalist>

                <?php foreach ($jenis as $j) : ?>
                <tr>
                    <form action="<?= base_url('admin/jenis-retribusi/update/' . $j['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <td>
                        <textarea name="kategori" list="kategori-list" placeholder="Ketik/Pilih Kategori" style="padding: 8px; width: 100%; border-radius: 6px; border: 1px solid #dee2e6; resize: vertical; min-height: 40px; font-family: inherit; font-size: 0.95rem;"><?= esc($j['kategori'] ?? '') ?></textarea>
                    </td>
                    <td>
                        <textarea name="jenis" required style="padding: 8px; width: 100%; border-radius: 6px; border: 1px solid #dee2e6; resize: vertical; min-height: 40px; font-family: inherit; font-size: 0.95rem;"><?= esc($j['jenis']) ?></textarea>
                    </td>
                    <td>
                        <input type="number" name="tarif" value="<?= $j['tarif'] ?>" step="0.01" required style="padding: 8px; width: 100%; border-radius: 6px; border: 1px solid #dee2e6;">
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <button type="submit" class="btn btn-success btn-sm" style="padding: 6px 12px; font-size: 0.8rem;">Update</button>
                            <a href="<?= base_url('admin/jenis-retribusi/delete/' . $j['id']) ?>" class="btn btn-danger btn-sm" style="padding: 6px 12px; font-size: 0.8rem;" onclick="return confirm('Hapus data ini?')">Hapus</a>
                        </div>
                    </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<!-- Modal Tambah Jenis Baru -->
<div id="addModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tambah Layanan Baru</h3>
                <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form action="<?= base_url('admin/jenis-retribusi/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label>Kategori</label>
                    <div class="autocomplete-wrapper">
                        <!-- Text Input linked with existing datalist for autocomplete capability -->
                        <input type="text" name="kategori" list="kategori-list" placeholder="Contoh: UGD, Rawat Inap (bisa ketik baru)" style="width: 100%;" autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label>Nama Layanan</label>
                    <input type="text" name="jenis" required placeholder="Contoh: Rawat Jalan Umum" style="width: 100%;">
                </div>

                <div class="form-group">
                    <label>Tarif (Rp)</label>
                    <input type="number" name="tarif" step="0.01" required placeholder="0.00" style="width: 100%;">
                </div>

                <div style="margin-top: 25px; display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn" style="background: #e9ecef; color: #495057;" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('addModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    // Close modal when clicking outside of the modal content
    window.onclick = function(event) {
        var modal = document.getElementById('addModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<?= $this->endSection() ?>

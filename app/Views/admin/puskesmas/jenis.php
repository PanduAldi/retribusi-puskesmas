<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<style>
    .badge-info {
        background-color: var(--info-color);
        color: #fff;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .mapping-btn {
        background-color: #fff;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }

    .mapping-btn:hover {
        background-color: var(--primary-color);
        color: #fff;
    }

    /* Modal Styling */
    .modal {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(2px);
    }

    .modal-dialog {
        position: relative;
        width: 90%;
        max-width: 800px;
        margin: 30px auto;
    }

    .modal-content {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        padding: 0;
        overflow: hidden;
    }

    .modal-header {
        padding: 20px 30px;
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 { margin: 0; color: #1a237e; font-size: 1.25rem; }

    .modal-body {
        padding: 30px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .modal-footer {
        padding: 20px 30px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .category-section {
        margin-bottom: 25px;
    }

    .category-title {
        font-weight: 700;
        color: #1a237e;
        border-bottom: 2px solid #eef0f7;
        padding-bottom: 8px;
        margin-bottom: 15px;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
    }

    .service-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #f0f2f5;
        transition: 0.2s;
        cursor: pointer;
    }

    .service-item:hover {
        background-color: #f8f9ff;
        border-color: #d0d7de;
    }

    .service-item input[type="checkbox"] {
        margin-top: 3px;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .service-item label {
        cursor: pointer;
        margin: 0;
        font-size: 0.9rem;
        font-weight: 500;
        line-height: 1.4;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        font-weight: 700;
        color: #aaa;
        cursor: pointer;
    }
</style>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-map-marked-alt"></i> Mapping Layanan per Puskesmas</h3>
        <p>Klik tombol <b>Atur Layanan</b> untuk menentukan jenis layanan yang tersedia pada masing-masing Puskesmas.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 60px; text-align: center;">No</th>
                <th>Puskesmas</th>
                <th style="width: 150px; text-align: center;">Kode</th>
                <th style="width: 200px; text-align: center;">Jumlah Layanan Aktif</th>
                <th style="width: 180px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($allPuskesmas as $p) :
                $activeCount = isset($mappings[$p['id']]) ? count($mappings[$p['id']]) : 0;
            ?>
            <tr>
                <td style="text-align: center; color: #666;"><?= $no++ ?></td>
                <td>
                    <div style="font-weight: 700; color: #1a237e; font-size: 1rem;"><?= esc($p['prasarana']) ?></div>
                </td>
                <td style="text-align: center;">
                    <code style="background: #f0f2f5; padding: 3px 8px; border-radius: 4px;"><?= esc($p['kode_retribusi']) ?></code>
                </td>
                <td style="text-align: center;">
                    <span class="badge-info">
                        <?= $activeCount ?> Layanan
                    </span>
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn btn-sm mapping-btn" onclick="openMappingModal(<?= $p['id'] ?>)">
                        <i class="fas fa-cog"></i> Atur Layanan
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>

<?= $this->section('modals') ?>
<?php foreach ($allPuskesmas as $p) : ?>
<div id="modal-mapping-<?= $p['id'] ?>" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-hospital"></i> Mapping: <?= esc($p['prasarana']) ?></h3>
                <button type="button" class="close-modal" onclick="closeMappingModal(<?= $p['id'] ?>)">&times;</button>
            </div>
            <form action="<?= base_url('admin/puskesmas/jenis/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_puskesmas" value="<?= $p['id'] ?>">

                <div class="modal-body">
                    <p style="margin-bottom: 20px; color: #666; font-size: 0.9rem;">Pilih jenis layanan yang disediakan oleh <strong><?= esc($p['prasarana']) ?></strong>:</p>

                    <?php
                    // Group services by category
                    $groupedServices = [];
                    foreach ($allJenis as $j) {
                        $cat = $j['kategori'] ?? 'Lain-lain';
                        $groupedServices[$cat][] = $j;
                    }
                    ksort($groupedServices);

                    foreach ($groupedServices as $category => $services) :
                    ?>
                    <div class="category-section">
                        <div class="category-title"><?= esc($category) ?></div>
                        <div class="services-grid">
                            <?php foreach ($services as $j) :
                                $isChecked = isset($mappings[$p['id']]) && in_array($j['id'], $mappings[$p['id']]);
                            ?>
                            <div class="service-item" onclick="toggleCheckbox(this)">
                                <input type="checkbox" name="id_jenis[]" value="<?= $j['id'] ?>" <?= $isChecked ? 'checked' : '' ?> onclick="event.stopPropagation()">
                                <label><?= esc($j['jenis']) ?></label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" style="background: #e9ecef; color: #495057;" onclick="closeMappingModal(<?= $p['id'] ?>)">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
    function openMappingModal(id) {
        document.getElementById('modal-mapping-' + id).style.display = 'block';
        document.body.style.overflow = 'hidden'; // Disable scroll
    }

    function closeMappingModal(id) {
        document.getElementById('modal-mapping-' + id).style.display = 'none';
        document.body.style.overflow = 'auto'; // Enable scroll
    }

    function toggleCheckbox(element) {
        const checkbox = element.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
    }

    // Close on click outside
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = "none";
            document.body.style.overflow = 'auto';
        }
    }
</script>
<?= $this->endSection() ?>

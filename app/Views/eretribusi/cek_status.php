<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-search-dollar"></i> Cek Status Billing</h3>
    </div>

    <div style="max-width: 500px;">
        <p style="color: #666; margin-bottom: 25px; line-height: 1.8;">
            Pilih cara pencarian:
        </p>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-3" id="checkTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-billing-tab" data-bs-toggle="tab"
                   data-bs-target="#tab-billing" type="button" role="tab"
                   aria-selected="true"><i class="fas fa-barcode"></i> Via ID Billing</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-nrm-tab" data-bs-toggle="tab"
                   data-bs-target="#tab-nrm" type="button" role="tab"
                   aria-selected="false"><i class="fas fa-id-card"></i> Via No. RM</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Via ID Billing -->
            <div class="tab-pane fade show active" id="tab-billing" role="tabpanel"
                 aria-labelledby="tab-billing-tab">
                <form action="<?= base_url('eretribusi/billing/cek-status') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="id_billing">ID Billing</label>
                        <input type="text" name="id_billing" id="id_billing"
                               placeholder="Masukkan ID Billing, contoh: 202506080001"
                               required autofocus
                               style="font-size: 1.1rem; font-weight: 600; letter-spacing: 1px;">
                    </div>
                    <button type="submit" class="btn btn-primary"
                            style="padding: 14px 30px; font-size: 1rem;">
                        <i class="fas fa-search"></i> Cek Status
                    </button>
                </form>
            </div>

            <!-- Via No. RM -->
            <div class="tab-pane fade" id="tab-nrm" role="tabpanel"
                 aria-labelledby="tab-nrm-tab">
                <form action="<?= base_url('eretribusi/billing/cek-status-nrm') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="no_rm">No. RM Pasien</label>
                        <input type="text" name="no_rm" id="no_rm"
                               placeholder="Masukkan No. RM Pasien, contoh: 2304002648"
                               required autofocus
                               style="font-size: 1.1rem; font-weight: 600; letter-spacing: 1px;">
                    </div>
                    <button type="submit" class="btn btn-success"
                            style="padding: 14px 30px; font-size: 1rem;">
                        <i class="fas fa-search"></i> Cek Status
                    </button>
                </form>
                <div style="margin-top: 15px; font-size: 0.9rem; color: #666;">
                    <i class="fas fa-info-circle"></i> Sistem akan otomatis mencari transaksi pending
                    berdasarkan No. RM ini.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="background: #f8f9fa; border-left: 4px solid #0d6efd;">
    <h4 style="color: #1a237e; margin-bottom: 15px;"><i class="fas fa-info-circle"></i> Informasi</h4>
    <ul style="color: #555; line-height: 2; padding-left: 20px;">
        <li>Pastikan ID Billing atau No. RM yang dimasukkan sudah benar.</li>
        <li>Status akan diperbarui secara otomatis jika pembayaran sudah terkonfirmasi lunas.</li>
        <li>Jika mengalami kendala, hubungi administrator.</li>
    </ul>
</div>

<?= $this->endSection() ?>
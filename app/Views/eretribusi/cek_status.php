<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-search-dollar"></i> Cek Status Billing</h3>
    </div>

    <div style="max-width: 500px;">
        <p style="color: #666; margin-bottom: 25px; line-height: 1.8;">
            Masukkan ID Billing yang telah diterbitkan untuk memeriksa status pembayaran
            secara <i>real-time</i> dari server billing.
        </p>

        <form action="<?= base_url('eretribusi/billing/cek-status') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="id_billing">ID Billing</label>
                <input type="text"
                       name="id_billing"
                       id="id_billing"
                       placeholder="Masukkan ID Billing, contoh: 202506080001"
                       required
                       autofocus
                       style="font-size: 1.1rem; font-weight: 600; letter-spacing: 1px;">
            </div>

            <button type="submit" class="btn btn-primary" style="padding: 14px 30px; font-size: 1rem;">
                <i class="fas fa-search"></i> Cek Status
            </button>
        </form>
    </div>
</div>

<div class="card" style="background: #f8f9fa; border-left: 4px solid #0d6efd;">
    <h4 style="color: #1a237e; margin-bottom: 15px;"><i class="fas fa-info-circle"></i> Informasi</h4>
    <ul style="color: #555; line-height: 2; padding-left: 20px;">
        <li>Pastikan ID Billing yang dimasukkan sudah benar.</li>
        <li>Status akan diperbarui secara otomatis jika pembayaran sudah terkonfirmasi lunas.</li>
        <li>Jika mengalami kendala, hubungi administrator.</li>
    </ul>
</div>

<?= $this->endSection() ?>

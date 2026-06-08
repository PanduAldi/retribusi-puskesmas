<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div style="max-width: 900px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-file-invoice"></i> Ringkasan Konfirmasi Transaksi</h3>
            <div class="role-badge" style="background: var(--warning-color); color: #212529;">Siap Bayar</div>
        </div>

        <?php if (session()->getFlashdata('notif_sukses')) : ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('notif_sukses') ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px; background: #f8f9fa; padding: 25px; border-radius: 12px; border: 1px solid #eee;">
            <div>
                <div style="font-size: 0.8rem; color: #888; text-transform: uppercase; font-weight: 700; margin-bottom: 5px;">Informasi Puskesmas</div>
                <div style="font-size: 1.1rem; font-weight: 700; color: #1a237e;">
                    <i class="fas fa-hospital-alt" style="margin-right: 8px; color: var(--primary-color);"></i>
                    <?= esc($puskesmas['prasarana'] ?? $puskesmas['nama_puskesmas'] ?? '-') ?>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.8rem; color: #888; text-transform: uppercase; font-weight: 700; margin-bottom: 5px;">Nomor Invoice</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #1a237e; letter-spacing: 1px;">
                    <?= esc($invoice) ?>
                </div>
                <div style="font-size: 0.8rem; color: #666;"><?= date('d F Y') ?></div>
            </div>
        </div>

        <h4 style="margin-bottom: 15px; color: #333; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-receipt" style="color: var(--secondary-color);"></i> Rincian Layanan
        </h4>
        <table style="margin-bottom: 40px;">
            <thead>
                <tr>
                    <th>Layanan Retribusi</th>
                    <th style="text-align: center;">Volume</th>
                    <th style="text-align: right;">Subtotal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; foreach ($transaksi as $item) : ?>
                <tr>
                    <td style="font-weight: 600; color: #444;"><?= esc($item['jenis']) ?></td>
                    <td style="text-align: center;">
                        <span style="background: #eee; padding: 2px 10px; border-radius: 4px; font-weight: 600;"><?= esc($item['volume']) ?></span>
                    </td>
                    <td style="text-align: right; font-weight: 700;">
                        <?= number_format($item['amount'], 0, ',', '.') ?>
                    </td>
                </tr>
                <?php $total += $item['amount']; endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: #f8f9ff;">
                    <th colspan="2" style="text-align: right; font-size: 1rem;">Total Pembayaran</th>
                    <th style="text-align: right; color: #1a237e; font-size: 1.5rem; font-weight: 800;">
                        <span style="font-size: 1rem;">Rp</span> <?= number_format($total, 0, ',', '.') ?>
                    </th>
                </tr>
            </tfoot>
        </table>

        <div style="background: #fff9db; border: 1px solid #ffe066; padding: 20px; border-radius: 12px; margin-bottom: 40px; display: flex; gap: 15px; align-items: flex-start;">
            <i class="fas fa-info-circle" style="color: #f08c00; font-size: 1.25rem; margin-top: 3px;"></i>
            <div style="font-size: 0.9rem; color: #856404; line-height: 1.5;">
                <strong>Perhatian:</strong> Pastikan seluruh rincian di atas sudah benar. Setelah ID Billing dibuat, data transaksi ini tidak dapat diubah kembali secara mandiri.
            </div>
        </div>

        <div style="display: flex; gap: 15px; justify-content: center;">
            <form action="<?= base_url('eretribusi/generate') ?>" method="post" style="flex: 1;">
                <?= csrf_field() ?>
                <input type="hidden" name="invoice" value="<?= esc($invoice) ?>">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem; justify-content: center; box-shadow: 0 4px 15px rgba(13, 110, 253, 0.25);">
                    <i class="fas fa-qrcode"></i> Generate ID Billing & QRIS
                </button>
            </form>
            <a href="<?= base_url('eretribusi/transaksi') ?>" class="btn" style="background: #e9ecef; color: #495057; padding: 15px 25px;">
                <i class="fas fa-times"></i> Nanti Saja
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

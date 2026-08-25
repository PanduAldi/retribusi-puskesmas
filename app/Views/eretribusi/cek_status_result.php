<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-search-dollar"></i> Hasil Pengecekan Status Billing</h3>
        <a href="<?= base_url('eretribusi/billing/cek-status') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (!empty($status) && is_array($status)): ?>
        <?php $isLunas = $status['Status'] === 'LUNAS'; ?>
        <div style="text-align: center; padding: 40px 20px;">
            <?php if ($isLunas): ?>
                <div style="font-size: 5rem; color: #28a745; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 style="color: #28a745; margin-bottom: 15px;">LUNAS</h2>
                <p style="color: #666; font-size: 1.1rem; line-height: 1.8; max-width: 500px; margin: 0 auto;">
                    Pembayaran untuk ID Billing <strong><?= esc($id_billing) ?></strong> telah diterima dan
                    transaksi sudah selesai diproses.
                </p>
            <?php else: ?>
                <div style="font-size: 4rem; color: #ffc107; margin-bottom: 20px;">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <h2 style="color: #856404; margin-bottom: 15px;">BELUM LUNAS</h2>
                <p style="color: #666; font-size: 1.1rem; line-height: 1.8; max-width: 500px; margin: 0 auto;">
                    Pembayaran untuk ID Billing <strong><?= esc($id_billing) ?></strong> masih dalam proses atau
                    belum diterima oleh sistem billing.
                </p>
            <?php endif; ?>
        </div>

        <div class="card" style="margin-top: 30px;">
            <div class="card-header">
                <h4><i class="fas fa-file-invoice"></i> Detail Transaksi</h4>
            </div>
            <table class="table">
                <tbody>
                    <tr>
                        <th style="width: 30%;">ID Billing</th>
                        <td><?= esc($status['IdBilling']) ?></td>
                    </tr>
                    <tr>
                        <th>Nomor Dokumen</th>
                        <td><?= esc($status['NoDokumen']) ?></td>
                    </tr>
                    <tr>
                        <th>Nominal Tagihan</th>
                        <td>Rp <?= number_format((int)$status['Nominal'], 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <th>Status Pembayaran</th>
                        <td>
                            <?php if ($isLunas): ?>
                                <span style="display: inline-flex; align-items: center; gap: 5px;
                                             background: #d1e7dd; color: #0f5132; padding: 8px 16px;
                                             border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> LUNAS
                                </span>
                            <?php else: ?>
                                <span style="display: inline-flex; align-items: center; gap: 5px;
                                             background: #fff3cd; color: #856404; padding: 8px 16px;
                                             border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
                                    <i class="fas fa-clock"></i> BELUM LUNAS
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if (!empty($status['TglBayar'])): ?>
                    <tr>
                        <th>Tanggal Pembayaran</th>
                        <td><?= esc($status['TglBayar']) ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($isLunas): ?>
        <div class="alert alert-success" style="margin-top: 25px;">
            <i class="fas fa-info-circle"></i>
            Transaksi telah diperbarui sebagai "LUNAS" di sistem lokal.
            Anda dapat mencetak bukti pembayaran atau melanjutkan transaksi lain.
        </div>
        <?php endif; ?>

        <?php if (!empty($history)): ?>
        <div class="card" style="margin-top: 30px;">
            <div class="card-header bg-light">
                <h4><i class="fas fa-history"></i> Riwayat Transaksi Sebelumnya</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>ID Billing</th>
                                <th>Invoice</th>
                                <th>Puskesmas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $h): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                                <td><?= esc($h['id_billing'] ?: '-') ?></td>
                                <td><?= esc($h['invoice']) ?></td>
                                <td><?= esc($h['prasarana']) ?></td>
                                <td>
                                    <?php if (strtolower($h['status']) === 'paid' || strtolower($h['status']) === 'lunas'): ?>
                                        <span class="badge badge-success">LUNAS</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">BELUM LUNAS</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('eretribusi/billing/konfirmasi/' . $h['invoice']) ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            Maaf, tidak dapat mengambil data status dari server billing.
            Pastikan koneksi internet stabil dan server billing sedang beroperasi.
        </div>
    <?php endif; ?>

<?= $this->endSection() ?>

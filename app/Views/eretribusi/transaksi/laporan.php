<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-chart-line"></i> Laporan Transaksi Retribusi</h3>
        <a href="<?= base_url('eretribusi/transaksi/new') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Transaksi Baru
        </a>
    </div>

    <?php if (!empty($laporan) && is_array($laporan)): ?>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th>Nomor Invoice</th>
                        <th>Tanggal</th>
                        <th>Jenis Layanan</th>
                        <?php if (session()->get('role') === 'admin_kabupaten' && empty($idPuskesmas)): ?>
                            <th>Puskesmas</th>
                        <?php endif; ?>
                        <th style="text-align: center;">Vol</th>
                        <th style="text-align: right;">Total Bayar</th>
                        <th style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($laporan as $i => $trx): ?>
                        <tr>
                            <td style="text-align: center; color: #999; font-size: 0.85rem;"><?= $i + 1 ?></td>
                            <td>
                                <div style="font-weight: 700; color: #1a237e;"><?= esc($trx['invoice']) ?></div>
                                <div style="font-size: 0.75rem; color: #888;">ID: #<?= $trx['id'] ?></div>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem; font-weight: 500;"><?= esc(date('d M Y', strtotime($trx['invoice_date']))) ?></div>
                            </td>
                            <td>
                                <span style="font-size: 0.9rem; font-weight: 500;"><?= esc($trx['jenis']) ?></span>
                            </td>
                            <?php if (session()->get('role') === 'admin_kabupaten' && empty($idPuskesmas)): ?>
                                <td>
                                    <div style="font-size: 0.85rem; font-weight: 600; color: #555;">
                                        <i class="fas fa-hospital-alt" style="margin-right: 5px; color: #ccc;"></i>
                                        <?= esc($trx['prasarana']) ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <td style="text-align: center;">
                                <span style="background: #f0f2f5; padding: 2px 8px; border-radius: 4px; font-weight: 600; font-size: 0.85rem;">
                                    <?= esc($trx['volume']) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="font-weight: 800; color: #2c3e50;">Rp <?= number_format($trx['amount'], 0, ',', '.') ?></div>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($trx['status'] == 'paid'): ?>
                                    <span style="display: inline-flex; align-items: center; gap: 5px; background: #d1e7dd; color: #0f5132; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                        <i class="fas fa-check-circle"></i> Terbayar
                                    </span>
                                <?php else: ?>
                                    <span style="display: inline-flex; align-items: center; gap: 5px; background: #fff3cd; color: #856404; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 3rem; color: #e0e0e0; margin-bottom: 20px;">
                <i class="fas fa-folder-open"></i>
            </div>
            <h4 style="color: #bcbcbc; font-weight: 500;">Belum ada data laporan yang tersedia.</h4>
            <p style="color: #ddd;">Coba sesuaikan filter atau tambahkan transaksi baru.</p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

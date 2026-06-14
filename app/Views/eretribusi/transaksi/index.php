<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<style>
    /* Card Summary Styling */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .summary-card-custom {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .summary-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .summary-info {
        flex: 1;
    }
    .summary-info .label {
        font-size: 0.85rem;
        color: #888;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    .summary-info .val {
        font-size: 1.4rem;
        font-weight: 800;
        color: #2c3e50;
    }

    /* Modal Styling */
    .modal-detail {
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
    .modal-detail-dialog {
        position: relative;
        width: 90%;
        max-width: 600px;
        margin: 50px auto;
    }
    .modal-detail-content {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        overflow: hidden;
    }
    .modal-detail-header {
        padding: 20px 25px;
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-detail-body {
        padding: 25px;
    }
    .modal-detail-footer {
        padding: 15px 25px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .invoice-link {
        color: #1a237e;
        text-decoration: none;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
    }
    .invoice-link:hover {
        color: var(--primary-color);
        text-decoration: underline;
    }
</style>

<!-- Summary Cards -->
<div class="summary-grid">
    <div class="summary-card-custom" style="border-left: 5px solid #0d6efd;">
        <div class="summary-icon" style="background: #e6f0ff; color: #0d6efd;">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="summary-info">
            <div class="label">Total Transaksi</div>
            <div class="val"><?= esc($totalTransaksi ?? 0) ?></div>
        </div>
    </div>
    <div class="summary-card-custom" style="border-left: 5px solid #198754;">
        <div class="summary-icon" style="background: #e8f5e9; color: #198754;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="summary-info">
            <div class="label">Total Terbayar</div>
            <div class="val">Rp <?= number_format($totalTerbayar ?? 0, 0, ',', '.') ?></div>
        </div>
    </div>
    <div class="summary-card-custom" style="border-left: 5px solid #ffc107;">
        <div class="summary-icon" style="background: #fffde7; color: #f57f17;">
            <i class="fas fa-clock"></i>
        </div>
        <div class="summary-info">
            <div class="label">Belum Terbayar</div>
            <div class="val">Rp <?= number_format($totalBelumTerbayar ?? 0, 0, ',', '.') ?></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list-ul"></i> Riwayat Transaksi Retribusi</h3>
        <a href="<?= base_url('eretribusi/transaksi/new') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Transaksi Baru
        </a>
    </div>

    <?php if (!empty($transaksi) && is_array($transaksi)): ?>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th>Nomor Invoice</th>
                        <th>Tanggal</th>
                        <th>Jenis Layanan</th>
                        <?php if (session()->get('role') === 'admin_kabupaten'): ?>
                            <th>Puskesmas</th>
                        <?php endif; ?>
                        <th style="text-align: center;">Vol</th>
                        <th style="text-align: right;">Total Bayar</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksi as $i => $trx): ?>
                        <tr>
                            <td style="text-align: center; color: #999; font-size: 0.85rem;"><?= $i + 1 ?></td>
                            <td>
                                <a class="invoice-link" onclick="openDetailModal(<?= htmlspecialchars(json_encode($trx)) ?>)">
                                    <?= esc($trx['invoice']) ?>
                                </a>
                                <div style="font-size: 0.75rem; color: #888;">ID: #<?= $trx['id'] ?></div>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem; font-weight: 500;"><?= esc(date('d M Y', strtotime($trx['invoice_date']))) ?></div>
                            </td>
                            <td>
                                <span style="font-size: 0.9rem; font-weight: 500;"><?= esc($trx['jenis']) ?></span>
                            </td>
                            <?php if (session()->get('role') === 'admin_kabupaten'): ?>
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
                                <?php if ($trx['status'] == 'paid' || $trx['status'] == 'lunas'): ?>
                                    <span style="display: inline-flex; align-items: center; gap: 5px; background: #d1e7dd; color: #0f5132; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                        <i class="fas fa-check-circle"></i> Terbayar
                                    </span>
                                <?php else: ?>
                                    <span style="display: inline-flex; align-items: center; gap: 5px; background: #fff3cd; color: #856404; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($trx['status'] != 'paid' && $trx['status'] != 'lunas'): ?>
                                    <?php if (!empty($trx['id_billing'])): ?>
                                        <a href="<?= base_url('eretribusi/qris/' . $trx['id_billing']) ?>" class="btn btn-success" style="padding: 5px 10px; font-size: 0.8rem; border-radius: 6px;">
                                            <i class="fas fa-qrcode"></i> Bayar
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('eretribusi/konfirmasi/' . $trx['invoice']) ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem; border-radius: 6px;">
                                            <i class="fas fa-credit-card"></i> Bayar
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 0.85rem;">-</span>
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
            <h4 style="color: #bcbcbc; font-weight: 500;">Belum ada data transaksi yang tercatat.</h4>
            <p style="color: #ddd;">Silakan klik tombol "Transaksi Baru" untuk memulai.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Detail Transaksi -->
<div id="modal-detail-transaksi" class="modal-detail">
    <div class="modal-detail-dialog">
        <div class="modal-detail-content">
            <div class="modal-detail-header">
                <h3 style="margin: 0; color: #1a237e;"><i class="fas fa-file-invoice"></i> Detail Transaksi</h3>
                <button type="button" style="background:none; border:none; font-size: 1.5rem; color:#aaa; cursor:pointer;" onclick="closeDetailModal()">&times;</button>
            </div>
            <div class="modal-detail-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                    <div>
                        <div style="font-size: 0.8rem; color: #888;">NOMOR INVOICE</div>
                        <strong id="det-invoice" style="color: #1a237e; font-size: 1.1rem;">-</strong>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.8rem; color: #888;">TANGGAL</div>
                        <strong id="det-tanggal">-</strong>
                    </div>
                    <div>
                        <div style="font-size: 0.8rem; color: #888;">NOMOR REKAM MEDIS</div>
                        <strong id="det-rm">-</strong>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.8rem; color: #888;">STATUS</div>
                        <span id="det-status">-</span>
                    </div>
                </div>

                <h4 style="margin-bottom: 10px; font-size: 0.95rem; color: #333;">Rincian Item Layanan</h4>
                <div style="background: #f8f9fa; border-radius: 8px; padding: 10px; margin-bottom: 20px;">
                    <table style="margin: 0; background: transparent;">
                        <thead>
                            <tr style="background: transparent;">
                                <th style="padding: 8px; font-size: 0.8rem;">Layanan</th>
                                <th style="padding: 8px; text-align: center; font-size: 0.8rem; width: 60px;">Vol</th>
                                <th style="padding: 8px; text-align: right; font-size: 0.8rem; width: 120px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="det-items-body">
                            <!-- Injected by JS -->
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 2px solid #eef0f7;">
                    <strong style="color: #555;">TOTAL BAYAR</strong>
                    <strong id="det-total" style="color: #1a237e; font-size: 1.4rem;">Rp 0</strong>
                </div>
            </div>
            <div class="modal-detail-footer">
                <div id="det-action-container">
                    <!-- Bayar button will be injected here if pending -->
                </div>
                <button type="button" class="btn" style="background: #e9ecef; color: #495057;" onclick="closeDetailModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openDetailModal(trx) {
        document.getElementById('det-invoice').innerText = trx.invoice;

        // Date formatting
        const dateObj = new Date(trx.invoice_date);
        const formattedDate = dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        document.getElementById('det-tanggal').innerText = formattedDate;

        document.getElementById('det-rm').innerText = trx.no_dokumen;

        // Status badge
        const statusEl = document.getElementById('det-status');
        if (trx.status === 'paid' || trx.status === 'lunas') {
            statusEl.innerHTML = `<span style="background: #d1e7dd; color: #0f5132; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Terbayar</span>`;
        } else {
            statusEl.innerHTML = `<span style="background: #fff3cd; color: #856404; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Pending</span>`;
        }

        // Items mapping
        const itemsBody = document.getElementById('det-items-body');
        itemsBody.innerHTML = '';
        if (trx.items_detail && trx.items_detail.length > 0) {
            trx.items_detail.forEach(item => {
                const tr = document.createElement('tr');
                tr.style.background = 'transparent';
                tr.innerHTML = `
                    <td style="padding: 8px; font-size: 0.85rem; font-weight: 600; color: #444;">${item.jenis}</td>
                    <td style="padding: 8px; text-align: center; font-size: 0.85rem;">${item.volume}</td>
                    <td style="padding: 8px; text-align: right; font-weight: 700; font-size: 0.85rem;">Rp ${new Intl.NumberFormat('id-ID').format(item.amount)}</td>
                `;
                itemsBody.appendChild(tr);
            });
        }

        // Total
        document.getElementById('det-total').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(trx.amount)}`;

        // Action button
        const actionContainer = document.getElementById('det-action-container');
        actionContainer.innerHTML = '';
        if (trx.status !== 'paid' && trx.status !== 'lunas') {
            const payBtn = document.createElement('a');
            if (trx.id_billing) {
                payBtn.href = `<?= base_url('eretribusi/qris/') ?>/${trx.id_billing}`;
                payBtn.className = 'btn btn-success';
                payBtn.innerHTML = `<i class="fas fa-qrcode"></i>Bayar`;
            } else {
                payBtn.href = `<?= base_url('eretribusi/konfirmasi/') ?>/${trx.invoice}`;
                payBtn.className = 'btn btn-primary';
                payBtn.innerHTML = `<i class="fas fa-credit-card"></i> Bayar Sekarang`;
            }
            actionContainer.appendChild(payBtn);
        }

        document.getElementById('modal-detail-transaksi').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeDetailModal() {
        document.getElementById('modal-detail-transaksi').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close detail modal on click outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modal-detail-transaksi');
        if (event.target === modal) {
            modal.style.display = "none";
            document.body.style.overflow = 'auto';
        }
    });
</script>

<?= $this->endSection() ?>

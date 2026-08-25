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

            <?php if (!empty($items_detail)): ?>
            <div style="padding: 15px 20px; border-top: 1px solid #eee;">
                <h5 style="font-weight: 700; font-size: 0.95rem; color: #333; margin-bottom: 12px; margin-top: 10px;">
                    <i class="fas fa-list" style="margin-right: 5px;"></i> Rincian Layanan & Tarif
                </h5>
                <div style="border: 1px solid #eee; border-radius: 8px; overflow: hidden;">
                    <table class="table table-bordered table-striped mb-0" style="font-size: 0.9rem;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th>Jenis Layanan</th>
                                <th style="width: 80px; text-align: center;">Vol</th>
                                <th style="width: 150px; text-align: right;">Tarif</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items_detail as $item): ?>
                            <tr>
                                <td><?= esc($item['jenis']) ?></td>
                                <td style="text-align: center;"><?= esc($item['volume']) ?></td>
                                <td style="text-align: right;">Rp <?= number_format((int)$item['amount'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
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
                                <td><?= !empty($h['created_at']) ? date('d/m/Y H:i', strtotime($h['created_at'])) : '-' ?></td>
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
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showHistoryDetail('<?= esc($h['invoice']) ?>')">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Modal Detail Transaksi -->
        <style>
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
        </style>

        <div id="modal-detail-transaksi" class="modal-detail">
            <div class="modal-detail-dialog">
                <div class="modal-detail-content">
                    <div class="modal-detail-header">
                        <h4 style="margin: 0; font-weight: 700; color: #1a237e; font-size: 1.2rem;">
                            <i class="fas fa-receipt" style="margin-right: 8px;"></i>Detail Transaksi
                        </h4>
                        <button type="button" onclick="closeDetailModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #888;">&times;</button>
                    </div>
                    <div class="modal-detail-body">
                        <div style="background: #f8f9fa; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: #666; font-size: 0.85rem;">Invoice:</span>
                                <strong id="det-invoice" style="color: #1a237e; font-size: 0.9rem;">-</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: #666; font-size: 0.85rem;">No. RM:</span>
                                <span id="det-rm" style="font-weight: 600; font-size: 0.85rem;">-</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: #666; font-size: 0.85rem;">Tanggal:</span>
                                <span id="det-tanggal" style="font-weight: 600; font-size: 0.85rem;">-</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #666; font-size: 0.85rem;">Status:</span>
                                <div id="det-status"></div>
                            </div>
                        </div>

                        <h5 style="font-weight: 700; font-size: 0.95rem; color: #333; margin-bottom: 12px;">Rincian Layanan / Retribusi</h5>
                        <div style="border: 1px solid #eee; border-radius: 8px; overflow: hidden; margin-bottom: 20px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f8f9fa; border-bottom: 1px solid #eee;">
                                        <th style="padding: 8px 12px; text-align: left; font-size: 0.8rem;">Item</th>
                                        <th style="padding: 8px; text-align: center; font-size: 0.8rem; width: 60px;">Vol</th>
                                        <th style="padding: 8px 12px; text-align: right; font-size: 0.8rem; width: 120px;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="det-items-body"></tbody>
                            </table>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 2px solid #eef0f7;">
                            <strong style="color: #555;">TOTAL BAYAR</strong>
                            <strong id="det-total" style="color: #1a237e; font-size: 1.4rem;">Rp 0</strong>
                        </div>
                    </div>
                    <div class="modal-detail-footer">
                        <div id="det-action-container"></div>
                        <button type="button" class="btn" style="background: #e9ecef; color: #495057;" onclick="closeDetailModal()">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showHistoryDetail(invoice) {
                fetch(`<?= base_url('eretribusi/billing/detail/') ?>/${invoice}`)
                    .then(response => response.json())
                    .then(trx => {
                        if (trx.status === 'error') {
                            alert(trx.message);
                            return;
                        }

                        document.getElementById('det-invoice').innerText = trx.invoice;
                        const dateObj = new Date(trx.created_at || trx.invoice_date);
                        const formattedDate = dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                        document.getElementById('det-tanggal').innerText = formattedDate;
                        document.getElementById('det-rm').innerText = trx.no_dokumen;

                        const statusEl = document.getElementById('det-status');
                        if (trx.status === 'paid' || trx.status === 'lunas') {
                            statusEl.innerHTML = `<span style="background: #d1e7dd; color: #0f5132; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Terbayar</span>`;
                        } else {
                            statusEl.innerHTML = `<span style="background: #fff3cd; color: #856404; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Pending</span>`;
                        }

                        const itemsBody = document.getElementById('det-items-body');
                        itemsBody.innerHTML = '';
                        if (trx.items_detail && trx.items_detail.length > 0) {
                            trx.items_detail.forEach(item => {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td style="padding: 8px 12px; font-size: 0.85rem; font-weight: 600; color: #444;">${item.jenis}</td>
                                    <td style="padding: 8px; text-align: center; font-size: 0.85rem;">${item.volume}</td>
                                    <td style="padding: 8px 12px; text-align: right; font-weight: 700; font-size: 0.85rem;">Rp ${new Intl.NumberFormat('id-ID').format(item.amount)}</td>
                                `;
                                itemsBody.appendChild(tr);
                            });
                        }

                        document.getElementById('det-total').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(trx.amount)}`;

                        const actionContainer = document.getElementById('det-action-container');
                        actionContainer.innerHTML = '';
                        if (trx.status !== 'paid' && trx.status !== 'lunas') {
                            const payBtn = document.createElement('a');
                            if (trx.id_billing) {
                                payBtn.href = `<?= base_url('eretribusi/qris/') ?>/${trx.id_billing}`;
                                payBtn.className = 'btn btn-success';
                                payBtn.innerHTML = `<i class="fas fa-qrcode"></i> Bayar`;
                            } else {
                                payBtn.href = `<?= base_url('eretribusi/konfirmasi/') ?>/${trx.invoice}`;
                                payBtn.className = 'btn btn-primary';
                                payBtn.innerHTML = `<i class="fas fa-credit-card"></i> Bayar Sekarang`;
                            }
                            actionContainer.appendChild(payBtn);
                        }

                        document.getElementById('modal-detail-transaksi').style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Gagal memuat detail transaksi.');
                    });
            }

            function closeDetailModal() {
                document.getElementById('modal-detail-transaksi').style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            window.addEventListener('click', function(event) {
                const modal = document.getElementById('modal-detail-transaksi');
                if (event.target === modal) {
                    modal.style.display = "none";
                    document.body.style.overflow = 'auto';
                }
            });
        </script>

    <?php else: ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            Maaf, tidak dapat mengambil data status dari server billing.
            Pastikan koneksi internet stabil dan server billing sedang beroperasi.
        </div>
    <?php endif; ?>

<?= $this->endSection() ?>

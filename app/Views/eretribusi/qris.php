<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="row" style="display: flex; gap: 30px; flex-wrap: wrap;">
    <!-- Bagian Kiri: Info Billing -->
    <div style="flex: 1; min-width: 350px;">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-file-invoice-dollar"></i> Detail Billing</h3>
            </div>

            <div style="text-align: center; padding: 10px 0;">
                <h4 style="color: #6c757d; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">ID Billing</h4>
                <div style="font-size: 1.4rem; font-weight: 800; color: #1a237e; letter-spacing: 1px; background: #f0f4ff; padding: 12px; border-radius: 12px; display: block; border: 2px dashed #3f51b5; word-break: break-all; overflow-wrap: break-word; line-height: 1.2;">
                    <?= esc($id_billing) ?>
                </div>
            </div>

            <table class="table">
                <tbody>
                    <tr>
                        <th style="width: 40%; border: none;">No. Rekam Medik</th>
                        <td style="border: none; font-weight: 800; color: #0d6efd;"><?= esc($transaksi_master['no_dokumen']) ?></td>
                    </tr>
                    <tr>
                        <th style="width: 40%;">Puskesmas</th>
                        <td style="font-weight: 600;"><?= esc($puskesmas['prasarana']) ?></td>
                    </tr>
                    <tr>
                        <th>Total Bayar</th>
                        <td style="font-weight: 800; color: #198754; font-size: 1.1rem;">Rp <?= number_format(array_sum(array_column($items, 'amount')), 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <th>Metode</th>
                        <td>QRIS / Virtual Account</td>
                    </tr>
                    <tr>
                        <th>Berlaku Sampai</th>
                        <td style="color: #dc3545; font-weight: 600;"><?= date('d F Y') ?> (23:59)</td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 30px; display: flex; flex-direction: column; gap: 10px;">
                <button onclick="window.print()" class="btn btn-warning" style="width: 100%; justify-content: center;">
                    <i class="fas fa-print"></i> Cetak Struk (80mm)
                </button>
                <a href="<?= base_url('eretribusi/transaksi') ?>" class="btn btn-success" style="width: 100%; justify-content: center;">
                    <i class="fas fa-check-double"></i> Selesai & Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Bagian Kanan: QRIS -->
    <div style="flex: 1; min-width: 350px;">
        <div class="card" style="text-align: center;">
            <div class="card-header">
                <h3><i class="fas fa-qrcode"></i> QRIS Pembayaran</h3>
            </div>

            <div id="qris-container" style="padding: 20px 0;">
                <div style="width: 280px; min-height: 220px; background: #f8f9fa; border: 3px dashed #dee2e6; border-radius: 15px; margin: 0 auto; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px; color: #6c757d; padding: 25px;">
                    <i class="fas fa-qrcode fa-5x" style="color: #198754;"></i>
                    <p style="font-weight: 700; color: #1a237e; margin: 0;">Link QRIS siap digunakan</p>
                    <p style="font-size: 0.85rem; margin: 0; line-height: 1.5;">
                        Link pembayaran berlaku selama <strong>5 menit</strong> sejak halaman ini dibuka.
                    </p>
                    <div id="qris-countdown" style="font-weight: 800; color: #dc3545; font-size: 1.2rem;">05:00</div>
                </div>

                <a id="btn-bayar-qris" href="<?= esc($paymentLink, 'attr') ?>" class="btn btn-success" style="margin-top: 25px; padding: 15px 40px; font-size: 1rem; box-shadow: 0 4px 15px rgba(25, 135, 84, 0.3); display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-external-link-alt"></i> BAYAR PAKAI QRIS
                </a>

                <div style="margin-top: 15px; display: flex; justify-content: center;">
                    <a href="<?= esc(current_url(), 'attr') ?>" class="btn btn-outline-secondary" style="padding: 8px 18px; font-size: 0.85rem;">
                        <i class="fas fa-sync-alt"></i> Refresh Link QRIS
                    </a>
                </div>

                <p id="qris-expired-note" style="margin-top: 20px; font-size: 0.85rem; color: #6c757d; line-height: 1.6;">
                    Klik tombol di atas untuk membuka halaman pembayaran QRIS Bank Jateng.<br>
                    Jika waktu habis, klik Refresh Link QRIS untuk membuat link baru.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles for Thermal Printer 80mm -->
<style>
@media print {
    /* Hide everything except structural content */
    body * { visibility: hidden; background: #fff !important; }
    .sidebar, .navbar, .btn, .card-header, #btn-bayar-qris { display: none !important; }

    .main-content, .content-body { margin: 0 !important; padding: 0 !important; }

    /* Structural receipt layout (80mm) */
    #print-area {
        visibility: visible;
        position: absolute;
        left: 0;
        top: 0;
        width: 80mm;
        padding: 5mm;
        font-family: 'Courier New', Courier, monospace;
        font-size: 10pt;
        color: #000;
        line-height: 1.2;
    }

    .print-header { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 5mm; margin-bottom: 5mm; }
    .print-header h2 { font-size: 12pt; margin: 0; }
    .print-body { margin-bottom: 5mm; }
    .print-row { display: flex; justify-content: space-between; margin-bottom: 2mm; }
    .print-footer { text-align: center; border-top: 1px dashed #000; padding-top: 5mm; font-size: 8pt; }

    #qris-placeholder-print {
        display: block !important;
        margin: 5mm auto;
        width: 50mm;
        height: 50mm;
        border: 1px solid #000;
        text-align: center;
        line-height: 50mm;
    }
}

/* Hidden by default, only shown in print */
#print-area { display: none; }
</style>

<!-- Area Khusus Print (80mm Thermal) -->
<div id="print-area">
    <div class="print-header">
        <h2>STRUK RETRIBUSI</h2>
        <p><?= esc($puskesmas['prasarana']) ?></p>
        <p><?= date('d/m/Y H:i') ?></p>
    </div>

    <div class="print-body">
        <div class="print-row">
            <span>NO. RM:</span>
            <span style="font-weight: bold;"><?= esc($transaksi_master['no_dokumen']) ?></span>
        </div>
        <div class="print-row">
            <span>ID BILLING:</span>
            <span style="font-weight: bold;"><?= esc($id_billing) ?></span>
        </div>
        <div style="margin: 4mm 0; border-bottom: 1px solid #eee;"></div>
        <?php foreach ($items as $item): ?>
            <div class="print-row">
                <span style="max-width: 60%;"><?= esc($item['jenis']) ?></span>
                <span><?= number_format($item['amount'], 0, ',', '.') ?></span>
            </div>
        <?php endforeach; ?>
        <div style="margin: 4mm 0; border-bottom: 1px solid #000;"></div>
        <div class="print-row" style="font-weight: bold; font-size: 12pt;">
            <span>TOTAL:</span>
            <span>Rp <?= number_format(array_sum(array_column($items, 'amount')), 0, ',', '.') ?></span>
        </div>
    </div>

    <div id="qris-placeholder-print">
        [ SCAN QRIS ]
    </div>

    <div class="print-footer">
        <p>Terima Kasih</p>
        <p>Simpan struk ini sebagai bukti pembayaran sah.</p>
        <p>-- Retribusi Puskesmas --</p>
    </div>
</div>

<script>
(function() {
    let remainingSeconds = 5 * 60;
    const countdown = document.getElementById('qris-countdown');
    const payButton = document.getElementById('btn-bayar-qris');
    const expiredNote = document.getElementById('qris-expired-note');

    const timer = setInterval(function() {
        remainingSeconds--;

        const minutes = String(Math.floor(remainingSeconds / 60)).padStart(2, '0');
        const seconds = String(remainingSeconds % 60).padStart(2, '0');
        countdown.textContent = minutes + ':' + seconds;

        if (remainingSeconds <= 0) {
            clearInterval(timer);
            countdown.textContent = 'KADALUARSA';
            payButton.classList.remove('btn-success');
            payButton.classList.add('btn-secondary');
            payButton.setAttribute('aria-disabled', 'true');
            payButton.addEventListener('click', function(event) {
                event.preventDefault();
            });
            expiredNote.innerHTML = 'Link QRIS sudah kadaluarsa. Klik <strong>Refresh Link QRIS</strong> untuk membuat link baru.';
        }
    }, 1000);
})();
</script>

<?= $this->endSection() ?>

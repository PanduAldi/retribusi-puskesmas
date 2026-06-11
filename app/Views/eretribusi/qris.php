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
                <div id="qris-placeholder" style="width: 280px; height: 280px; background: #f8f9fa; border: 3px dashed #dee2e6; border-radius: 15px; margin: 0 auto; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 15px; color: #adb5bd;">
                    <i class="fas fa-qrcode fa-5x"></i>
                    <p style="font-weight: 500;">QRIS belum di-generate</p>
                </div>

                <button id="btn-generate-qris" class="btn btn-primary" style="margin-top: 25px; padding: 15px 40px; font-size: 1rem; box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);">
                    <i class="fas fa-sync-alt"></i> GENERATE QRIS SEKARANG
                </button>

                <p style="margin-top: 20px; font-size: 0.85rem; color: #6c757d; line-height: 1.6;">
                    Klik tombol di atas untuk mendapatkan kode QRIS terbaru dari Bank Jateng.<br>
                    Pastikan koneksi internet stabil.
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
    .sidebar, .navbar, .btn, .card-header, #btn-generate-qris { display: none !important; }

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
document.getElementById('btn-generate-qris').addEventListener('click', function() {
    const btn = this;
    const placeholder = document.getElementById('qris-placeholder');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> GENERATING...';

    // Simulasi hit ke API Bank Jateng untuk mendapatkan QRIS
    setTimeout(function() {
        placeholder.style.border = 'none';
        placeholder.style.background = '#fff';
        placeholder.innerHTML = '<img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=IDBILLING:<?= $id_billing ?>|TOTAL:<?= array_sum(array_column($items, 'amount')) ?>" alt="QRIS" style="width: 100%; height: 100%; object-fit: contain;">';

        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-secondary');
        btn.innerHTML = '<i class="fas fa-check"></i> QRIS TERGENERASI';

        // Update di print area juga
        document.getElementById('qris-placeholder-print').innerHTML = '<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=IDBILLING:<?= $id_billing ?>|TOTAL:<?= array_sum(array_column($items, 'amount')) ?>" style="width: 100%; height: 100%;">';
    }, 1500);
});
</script>

<?= $this->endSection() ?>

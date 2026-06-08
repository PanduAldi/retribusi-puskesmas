<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran QRIS - Retribusi Puskesmas</title>
    <style>
        body { font-family: sans-serif; margin: 20px; line-height: 1.6; text-align: center; }
        .container { max-width: 600px; margin: auto; border: 1px solid #ccc; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #28a745; margin-bottom: 5px; }
        .id-billing { font-size: 1.5em; font-weight: bold; color: #333; margin: 20px 0; letter-spacing: 2px; }
        .qris-placeholder {
            width: 250px; height: 250px; background-color: #eee; margin: 20px auto;
            display: flex; align-items: center; justify-content: center; border: 2px dashed #bbb;
        }
        .info { text-align: left; margin: 20px 0; background: #f9f9f9; padding: 15px; border-radius: 4px; }
        .btn { padding: 12px 25px; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; display: inline-block; margin-top: 20px; font-weight: bold; }
        .btn-success { background-color: #28a745; }
        .btn-outline { border: 1px solid #007bff; color: #007bff; background: transparent; }
    </style>
</head>
<body>
    <div class="container">
        <h2>ID Billing Berhasil Diterbitkan</h2>
        <p>Silakan lakukan pembayaran menggunakan aplikasi BIMA Bank Jateng atau Scan QRIS di bawah ini.</p>

        <div class="id-billing"><?= esc($id_billing) ?></div>

        <div class="qris-placeholder">
            <!-- Link QRIS biasanya dari Bank Jateng -->
            <p style="color: #666; font-size: 0.9em;">[ QRIS Link/Image dari Bank Jateng ]</p>
        </div>

        <div class="info">
            <p><strong>Puskesmas:</strong> <?= esc($puskesmas['prasarana']) ?></p>
            <p><strong>Total Bayar:</strong> Rp <?= number_format(array_sum(array_column($transaksi, 'amount')), 0, ',', '.') ?></p>
            <p><strong>Berlaku Sampai:</strong> <?= date('d F Y') ?> (23:59)</p>
        </div>

        <button onclick="window.print()" class="btn btn-outline">Cetak / Simpan PDF</button>
        <br>
        <a href="<?= base_url('/') ?>" class="btn btn-success">Selesai</a>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran Billing</title>
    <style>
        body { font-family: sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: auto; border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
        h2 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        table th { background-color: #f4f4f4; }
        .total { font-size: 1.2em; font-weight: bold; text-align: right; }
        .actions { margin-top: 20px; text-align: center; }
        .btn { padding: 10px 20px; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; }
        .btn-primary { background-color: #007bff; }
        .btn-secondary { background-color: #6c757d; }
        .notif { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .notif-gagal { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Konfirmasi Pembayaran</h2>

        <?php if (session()->getFlashdata('notif_gagal')) : ?>
            <div class="notif notif-gagal">
                <?= session()->getFlashdata('notif_gagal') ?>
            </div>
        <?php endif; ?>

        <p><strong>Puskesmas:</strong> <?= esc($puskesmas['prasarana']) ?></p>
        <p><strong>Nomor Invoice:</strong> <?= esc($invoice) ?></p>

        <table>
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th>Volume</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; foreach ($transaksi as $item) : ?>
                <tr>
                    <td><?= esc($item['jenis']) ?></td>
                    <td><?= esc($item['volume']) ?></td>
                    <td>Rp <?= number_format($item['amount'], 0, ',', '.') ?></td>
                </tr>
                <?php $total += $item['amount']; endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="total">Total Bayar</td>
                    <td class="total">Rp <?= number_format($total, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="actions">
            <form action="<?= base_url('eretribusi/generate') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="invoice" value="<?= esc($invoice) ?>">
                <a href="<?= base_url('/') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Generate ID Billing</button>
            </form>
        </div>
    </div>
</body>
</html>

<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-plus-circle"></i> Input Transaksi Baru</h3>
        <a href="<?= base_url('eretribusi/transaksi') ?>" class="btn btn-primary" style="background: var(--secondary-color);">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (session()->get('role') === 'admin_kabupaten' && empty($selectedPuskesmasId)): ?>
        <form action="<?= base_url('eretribusi/transaksi/new') ?>" method="GET">
            <div class="form-group">
                <label for="id_puskesmas">Pilih Puskesmas Pelayanan</label>
                <div style="display: flex; gap: 15px;">
                    <select id="id_puskesmas" name="id_puskesmas" required style="flex: 1;">
                        <option value="">-- Pilih Puskesmas --</option>
                        <?php foreach ($puskesmas as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= $p['nama_puskesmas'] ?? $p['prasarana'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Muat Tarif
                    </button>
                </div>
            </div>
        </form>
    <?php else: ?>

        <form action="<?= base_url('eretribusi/transaksi/store') ?>" method="POST">
            <?= csrf_field() ?>

            <?php if (session()->get('role') === 'admin_kabupaten'): ?>
                <input type="hidden" name="id_puskesmas" value="<?= $selectedPuskesmasId ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Puskesmas</label>
                <div style="padding: 12px 15px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #eee; color: #555; font-weight: 500;">
                    <i class="fas fa-hospital-alt"></i>
                    <?= session()->get('role') === 'admin_kabupaten' ? 'Puskesmas ID: ' . $selectedPuskesmasId : ($currentPuskesmas['nama_puskesmas'] ?? $currentPuskesmas['prasarana'] ?? '') ?>
                </div>
            </div>

            <div class="form-group">
                <label for="id_jenis">Jenis Layanan Retribusi</label>
                <select id="id_jenis" name="id_jenis" required onchange="hitungTotal()" style="height: 50px; font-weight: 500;">
                    <option value="">-- Pilih Jenis Layanan --</option>
                    <?php foreach ($tarif as $t): ?>
                        <option value="<?= $t['id_jenis'] ?>" data-tarif="<?= $t['tarif'] ?>">
                            <?= $t['jenis'] ?> &mdash; Rp <?= number_format($t['tarif'], 0, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: #888; margin-top: 5px; display: block;">*Tarif otomatis muncul berdasarkan jenis layanan yang dipilih.</small>
            </div>

            <div class="form-group">
                <label for="volume">Volume / Jumlah Layanan</label>
                <input type="number" id="volume" name="volume" value="<?= old('volume', 1) ?>" min="1" step="1" required onchange="hitungTotal()" onkeyup="hitungTotal()" style="height: 50px;">
            </div>

            <div style="background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%); padding: 25px; border-radius: 12px; border: 2px dashed #d0d7de; text-align: right; margin-top: 30px;">
                <div style="font-size: 0.9rem; color: #666; margin-bottom: 5px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Estimasi Total Bayar</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: #1a237e;">
                    <span style="font-size: 1.5rem;">Rp</span> <span id="total_amount_display">0</span>
                </div>
            </div>

            <div style="margin-top: 40px; display: flex; gap: 15px; justify-content: flex-end;">
                <a href="<?= base_url('eretribusi/transaksi') ?>" class="btn" style="background: #e9ecef; color: #495057;">
                    <i class="fas fa-times"></i> Batalkan
                </a>
                <button type="submit" class="btn btn-success" style="padding: 12px 35px; font-size: 1rem; box-shadow: 0 4px 15px rgba(25, 135, 84, 0.2);">
                    <i class="fas fa-save"></i> Simpan Transaksi
                </button>
            </div>
        </form>

        <script>
        function hitungTotal() {
            var select = document.getElementById('id_jenis');
            var volume = document.getElementById('volume').value;

            var tarif = 0;
            if (select.selectedIndex > 0) {
                var option = select.options[select.selectedIndex];
                tarif = parseFloat(option.getAttribute('data-tarif')) || 0;
            }

            var total = tarif * (parseFloat(volume) || 0);

            // Format rupiah
            var formattedTotal = new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('total_amount_display').innerText = formattedTotal;
        }

        window.onload = function() {
            hitungTotal();
        };
        </script>

    <?php endif; ?>
</div>

<?= $this->endSection() ?>

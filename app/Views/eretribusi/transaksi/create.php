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
                <label for="no_dokumen">Nomor Rekam Medik (SIMPUS)</label>
                <input type="text" id="no_dokumen" name="no_dokumen" value="<?= old('no_dokumen') ?>" required placeholder="Contoh: 12.34.56" style="height: 50px; font-weight: 600; font-size: 1.1rem; border-color: #0d6efd;">
            </div>

            <div style="margin-top: 30px;">
                <label style="font-weight: 700; color: #1a237e; margin-bottom: 15px; display: block;">Item Retribusi / Layanan</label>
                <div style="overflow-x: auto;">
                    <table class="table" id="table-items">
                        <thead>
                            <tr>
                                <th>Jenis Layanan</th>
                                <th style="width: 150px;">Volume</th>
                                <th style="width: 200px; text-align: right;">Tarif</th>
                                <th style="width: 200px; text-align: right;">Subtotal</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="item-container">
                            <tr class="item-row">
                                <td>
                                    <select name="id_jenis[]" class="id_jenis" required onchange="updateTarif(this)" style="width: 100%;">
                                        <option value="">-- Pilih Layanan --</option>
                                        <?php foreach ($tarif as $t): ?>
                                            <option value="<?= $t['id_jenis'] ?>" data-tarif="<?= $t['tarif'] ?>">
                                                <?= $t['jenis'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="volume[]" class="volume" value="1" min="1" required onchange="hitungSubtotal(this)" onkeyup="hitungSubtotal(this)" style="width: 100%;">
                                </td>
                                <td style="text-align: right;">
                                    <span class="tarif-display">Rp 0</span>
                                </td>
                                <td style="text-align: right;">
                                    <span class="subtotal-display">Rp 0</span>
                                    <input type="hidden" class="subtotal-val" value="0">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-primary btn-sm" onclick="addRow()">
                    <i class="fas fa-plus"></i> Tambah Item
                </button>
            </div>

            <div style="background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%); padding: 25px; border-radius: 12px; border: 2px dashed #d0d7de; text-align: right; margin-top: 30px;">
                <div style="font-size: 0.9rem; color: #666; margin-bottom: 5px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Total Bayar</div>
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
        function addRow() {
            var container = document.getElementById('item-container');
            var firstRow = container.querySelector('.item-row');
            var newRow = firstRow.cloneNode(true);

            // Clear inputs
            newRow.querySelector('.id_jenis').value = '';
            newRow.querySelector('.volume').value = 1;
            newRow.querySelector('.tarif-display').innerText = 'Rp 0';
            newRow.querySelector('.subtotal-display').innerText = 'Rp 0';
            newRow.querySelector('.subtotal-val').value = 0;

            container.appendChild(newRow);
        }

        function removeRow(btn) {
            var rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                btn.closest('.item-row').remove();
                hitungTotal();
            } else {
                alert('Minimal harus ada 1 item retribusi.');
            }
        }

        function updateTarif(select) {
            var row = select.closest('.item-row');
            var tarif = 0;
            if (select.selectedIndex > 0) {
                var option = select.options[select.selectedIndex];
                tarif = parseFloat(option.getAttribute('data-tarif')) || 0;
            }
            row.querySelector('.tarif-display').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(tarif);
            hitungSubtotal(row.querySelector('.volume'));
        }

        function hitungSubtotal(input) {
            var row = input.closest('.item-row');
            var select = row.querySelector('.id_jenis');
            var tarif = 0;
            if (select.selectedIndex > 0) {
                var option = select.options[select.selectedIndex];
                tarif = parseFloat(option.getAttribute('data-tarif')) || 0;
            }
            var volume = parseFloat(input.value) || 0;
            var subtotal = tarif * volume;

            row.querySelector('.subtotal-display').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(subtotal);
            row.querySelector('.subtotal-val').value = subtotal;

            hitungTotal();
        }

        function hitungTotal() {
            var subtotals = document.querySelectorAll('.subtotal-val');
            var total = 0;
            subtotals.forEach(function(el) {
                total += parseFloat(el.value) || 0;
            });

            document.getElementById('total_amount_display').innerText = new Intl.NumberFormat('id-ID').format(total);
        }
        </script>

    <?php endif; ?>
</div>

<?= $this->endSection() ?>

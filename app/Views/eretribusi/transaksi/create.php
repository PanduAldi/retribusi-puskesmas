<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<style>
    .entry-container {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 30px;
        align-items: start;
    }

    @media (max-width: 1100px) {
        .entry-container { grid-template-columns: 1fr; }
        .sticky-summary { position: static !important; width: 100% !important; }
    }

    .sticky-summary {
        position: sticky;
        top: 100px;
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a237e;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eef0f7;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .item-card {
        background: #fff;
        border: 1px solid #e0e6ed;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: 0.3s;
        position: relative;
    }

    .item-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .summary-card {
        background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
        color: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 20px rgba(26, 35, 126, 0.2);
    }

    .total-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.8;
        margin-bottom: 5px;
    }

    .total-value {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 20px;
    }

    .btn-add-item {
        background: #f0f4ff;
        color: var(--primary-color);
        border: 2px dashed var(--primary-color);
        width: 100%;
        padding: 15px;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-add-item:hover {
        background: var(--primary-color);
        color: #fff;
    }

    .remove-item-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        color: #ff6b6b;
        cursor: pointer;
        font-size: 1.2rem;
        opacity: 0.5;
        transition: 0.3s;
    }

    .remove-item-btn:hover { opacity: 1; color: var(--danger-color); }

    .input-group-custom {
        display: grid;
        grid-template-columns: 1fr 120px;
        gap: 15px;
    }

    .price-info {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #f0f2f5;
        font-size: 0.9rem;
    }

    /* Select2 Custom Styling to match existing CSS */
    .select2-container--default .select2-selection--single {
        height: 46px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px 15px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        padding-left: 0;
        color: #1a237e;
        font-weight: 600;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: var(--primary-color);
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border-radius: 6px;
    }
    .select2-dropdown {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 1050;
    }
</style>

<div class="card-header" style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h3 style="margin: 0; color: #1a237e;">Input Transaksi Baru</h3>
        <p style="color: #666; margin: 5px 0 0 0;">Lengkapi data layanan untuk membuat tagihan retribusi.</p>
    </div>
    <a href="<?= base_url('eretribusi/transaksi') ?>" class="btn btn-secondary" style="background: #e9ecef; color: #495057;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<?php if (session()->get('role') === 'admin_kabupaten' && empty($selectedPuskesmasId)): ?>
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <form action="<?= base_url('eretribusi/transaksi/new') ?>" method="GET">
            <div class="form-group">
                <label for="id_puskesmas" style="font-weight: 700;">Pilih Puskesmas Pelayanan</label>
                <p style="color: #666; font-size: 0.85rem; margin-bottom: 15px;">Silakan pilih lokasi Puskesmas untuk memuat daftar layanan yang tersedia.</p>
                <select id="id_puskesmas" name="id_puskesmas" required class="form-control" style="height: 50px;">
                    <option value="">-- Pilih Puskesmas --</option>
                    <?php foreach ($puskesmas as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= esc($p['prasarana']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; height: 50px; font-size: 1rem;">
                <i class="fas fa-sync-alt"></i> Muat Daftar Layanan
            </button>
        </form>
    </div>
<?php else: ?>

    <form action="<?= base_url('eretribusi/transaksi/store') ?>" method="POST" id="transaksiForm">
        <?= csrf_field() ?>
        <?php if (session()->get('role') === 'admin_kabupaten'): ?>
            <input type="hidden" name="id_puskesmas" value="<?= $selectedPuskesmasId ?>">
        <?php endif; ?>

        <div class="entry-container">
            <!-- Left Side: Form Inputs -->
            <div class="form-main">
                <div class="card" style="padding: 25px; margin-bottom: 25px; border-top: 4px solid var(--primary-color);">
                    <div class="form-section-title">
                        <i class="fas fa-user-injured"></i> Informasi Pasien & Lokasi
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label>Puskesmas Melayani</label>
                            <div style="padding: 12px 15px; background: #f8f9ff; border-radius: 8px; border: 1px solid #e0e4f0; color: #1a237e; font-weight: 700;">
                                <i class="fas fa-hospital-alt" style="margin-right: 8px;"></i>
                                <?= session()->get('role') === 'admin_kabupaten' ? 'Puskesmas ID: ' . $selectedPuskesmasId : ($currentPuskesmas['prasarana'] ?? '') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="no_dokumen">Nomor Rekam Medik (SIMPUS) <span style="color:red;">*</span></label>
                            <input type="text" id="no_dokumen" name="no_dokumen" value="<?= old('no_dokumen') ?>" required placeholder="Contoh: 2304002648" class="form-control" style="border-color: var(--primary-color); font-weight: 700;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label for="no_kwitansi">Nomor Kwitansi / Billing <span style="color:gray; font-size:0.8rem;">(Opsional / Auto-Generated)</span></label>
                            <input type="text" id="no_kwitansi" name="no_kwitansi" value="<?= old('no_kwitansi') ?>" placeholder="Kosongkan untuk generate otomatis (S00...)" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="nama_pasien">Nama Lengkap Pasien <span style="color:red;">*</span></label>
                            <input type="text" id="nama_pasien" name="nama_pasien" value="<?= old('nama_pasien') ?>" required placeholder="Sesuai KTP / SIMPUS" class="form-control">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin <span style="color:red;">*</span></label>
                            <select id="jenis_kelamin" name="jenis_kelamin" required class="form-control" style="height: 46px;">
                                <option value="">-- Pilih --</option>
                                <option value="LAKI-LAKI" <?= old('jenis_kelamin') == 'LAKI-LAKI' ? 'selected' : '' ?>>LAKI-LAKI</option>
                                <option value="PEREMPUAN" <?= old('jenis_kelamin') == 'PEREMPUAN' ? 'selected' : '' ?>>PEREMPUAN</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tgl_lahir">Tanggal Lahir <span style="color:red;">*</span></label>
                            <input type="date" id="tgl_lahir" name="tgl_lahir" value="<?= old('tgl_lahir') ?>" required class="form-control" style="height: 46px; padding: 6px 12px; border: 1px solid #dee2e6; border-radius: 8px; width: 100%;">
                        </div>
                        <div class="form-group">
                            <label for="alamat_pasien">Alamat Pasien</label>
                            <input type="text" id="alamat_pasien" name="alamat_pasien" value="<?= old('alamat_pasien') ?>" placeholder="Kelurahan / Kecamatan" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-section-title">
                    <i class="fas fa-concierge-bell"></i> Item Layanan & Retribusi
                </div>

                <div id="item-list-container">
                    <!-- Item Rows will be injected here -->
                    <div class="item-card item-row">
                        <span class="remove-item-btn" onclick="removeRow(this)"><i class="fas fa-times-circle"></i></span>

                        <div class="input-group-custom">
                            <div>
                                <label style="font-size: 0.8rem; color: #666;">Pilih Jenis Layanan</label>
                                <select name="id_jenis[]" class="id_jenis" required onchange="updateTarif(this)" style="width: 100%;">
                                    <option value="">-- Cari Layanan --</option>
                                    <?php
                                    $currentGroup = '';
                                    foreach ($tarif as $t):
                                        if (($t['kategori'] ?? 'Lain-lain') !== $currentGroup):
                                            if ($currentGroup !== '') echo '</optgroup>';
                                            $currentGroup = $t['kategori'] ?? 'Lain-lain';
                                            echo '<optgroup label="' . esc($currentGroup) . '">';
                                        endif;
                                    ?>
                                        <option value="<?= $t['id'] ?>" data-tarif="<?= $t['tarif'] ?>">
                                            <?= esc($t['jenis']) ?>
                                        </option>
                                    <?php endforeach;
                                    if ($currentGroup !== '') echo '</optgroup>';
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label style="font-size: 0.8rem; color: #666;">Volume</label>
                                <input type="number" name="volume[]" class="volume" value="1" min="1" required onchange="hitungSubtotal(this)" onkeyup="hitungSubtotal(this)">
                            </div>
                        </div>

                        <div class="price-info">
                            <div>
                                <span style="color: #888;">Tarif Satuan:</span>
                                <strong class="tarif-display" style="color: #444; margin-left: 5px;">Rp 0</strong>
                            </div>
                            <div style="text-align: right;">
                                <span style="color: #888;">Subtotal:</span>
                                <strong class="subtotal-display" style="color: var(--primary-color); font-size: 1.1rem; margin-left: 8px;">Rp 0</strong>
                                <input type="hidden" class="subtotal-val" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-add-item" onclick="addRow()">
                    <i class="fas fa-plus-circle"></i> Tambah Item Layanan Lainnya
                </button>
            </div>

            <!-- Right Side: Summary & Actions -->
            <div class="form-summary sticky-summary">
                <div class="summary-card">
                    <div class="total-label">Total Pembayaran</div>
                    <div class="total-value">
                        <span style="font-size: 1.2rem; opacity: 0.7;">Rp</span>
                        <span id="total_amount_display">0</span>
                    </div>

                    <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; margin-bottom: 25px; font-size: 0.85rem;">
                        <i class="fas fa-info-circle" style="margin-right: 5px;"></i>
                        Pastikan seluruh item layanan sudah sesuai sebelum menekan tombol simpan.
                    </div>

                    <button type="button" class="btn btn-warning" onclick="showKonfirmasiModal()" style="width: 100%; height: 60px; font-size: 1.1rem; font-weight: 800; border-radius: 12px; box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);">
                        <i class="fas fa-check-double"></i> SIMPAN & PROSES
                    </button>

                    <a href="<?= base_url('eretribusi/transaksi') ?>" style="display: block; text-align: center; margin-top: 20px; color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.9rem;">
                        <i class="fas fa-times"></i> Batalkan Transaksi
                    </a>
                </div>

                <div style="margin-top: 20px; padding: 20px; background: #fff; border-radius: 12px; border: 1px solid #e0e6ed; font-size: 0.85rem; color: #666;">
                    <div style="font-weight: 700; margin-bottom: 10px; color: #333;">💡 Tips Cepat</div>
                    <ul style="padding-left: 20px; margin: 0;">
                        <li>Gunakan <b>Tab</b> untuk berpindah input.</li>
                        <li>Tekan <b>Enter</b> pada tombol simpan.</li>
                        <li>Volume minimal adalah 1.</li>
                    </ul>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Konfirmasi Sebelum Simpan -->
    <style>
        .modal-konfirmasi {
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
        .modal-konfirmasi-dialog {
            position: relative;
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
        }
        .modal-konfirmasi-content {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .modal-konfirmasi-header {
            padding: 20px 25px;
            background: #fff3cd;
            border-bottom: 1px solid #ffc107;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-konfirmasi-body {
            padding: 25px;
            max-height: 60vh;
            overflow-y: auto;
        }
        .modal-konfirmasi-footer {
            padding: 15px 25px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>

    <div id="modal-konfirmasi" class="modal-konfirmasi">
        <div class="modal-konfirmasi-dialog">
            <div class="modal-konfirmasi-content">
                <div class="modal-konfirmasi-header">
                    <h4 style="margin: 0; font-weight: 700; color: #856404; font-size: 1.1rem;">
                        <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>Konfirmasi Transaksi
                    </h4>
                    <button type="button" onclick="closeKonfirmasiModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #888;">&times;</button>
                </div>
                <div class="modal-konfirmasi-body">
                    <div class="alert alert-warning" style="font-size: 0.9rem; margin-bottom: 20px;">
                        <i class="fas fa-info-circle"></i>
                        Periksa kembali data berikut sebelum menyimpan. Data yang sudah diproses <strong>tidak dapat diubah</strong>.
                    </div>

                    <!-- Info Pasien -->
                    <div style="background: #f8f9fa; border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                        <h5 style="font-weight: 700; font-size: 0.9rem; color: #1a237e; margin-bottom: 12px;">
                            <i class="fas fa-user"></i> Data Pasien
                        </h5>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.85rem;">
                            <div>
                                <span style="color: #666;">No. RM:</span>
                                <strong id="konfirm-rm" style="margin-left: 5px;">-</strong>
                            </div>
                            <div>
                                <span style="color: #666;">Nama:</span>
                                <strong id="konfirm-nama" style="margin-left: 5px;">-</strong>
                            </div>
                            <div>
                                <span style="color: #666;">Jenis Kelamin:</span>
                                <span id="konfirm-jk" style="margin-left: 5px;">-</span>
                            </div>
                            <div>
                                <span style="color: #666;">Tgl Lahir:</span>
                                <span id="konfirm-tgl" style="margin-left: 5px;">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Layanan -->
                    <h5 style="font-weight: 700; font-size: 0.9rem; color: #333; margin-bottom: 12px;">
                        <i class="fas fa-list"></i> Rincian Layanan & Tarif
                    </h5>
                    <div style="border: 1px solid #eee; border-radius: 8px; overflow: hidden; margin-bottom: 20px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa; border-bottom: 1px solid #eee;">
                                    <th style="padding: 8px 12px; text-align: left; font-size: 0.8rem;">Jenis Layanan</th>
                                    <th style="padding: 8px; text-align: center; font-size: 0.8rem; width: 60px;">Vol</th>
                                    <th style="padding: 8px 12px; text-align: right; font-size: 0.8rem; width: 120px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="konfirm-items-body"></tbody>
                        </table>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 2px solid #eef0f7;">
                        <strong style="color: #555;">TOTAL BAYAR</strong>
                        <strong id="konfirm-total" style="color: #1a237e; font-size: 1.4rem;">Rp 0</strong>
                    </div>
                </div>
                <div class="modal-konfirmasi-footer">
                    <button type="button" class="btn" style="background: #e9ecef; color: #495057;" onclick="closeKonfirmasiModal()">
                        <i class="fas fa-pencil-alt"></i> Kembali & Edit
                    </button>
                    <button type="button" class="btn btn-warning" style="font-weight: 700;" onclick="submitTransaksi()">
                        <i class="fas fa-check-double"></i> Ya, Simpan & Proses
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showKonfirmasiModal() {
        var form = document.getElementById('transaksiForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Populate pasien info
        document.getElementById('konfirm-rm').innerText = document.getElementById('no_dokumen').value || '-';
        document.getElementById('konfirm-nama').innerText = document.getElementById('nama_pasien').value || '-';
        document.getElementById('konfirm-jk').innerText = document.getElementById('jenis_kelamin').value || '-';

        var tglLahir = document.getElementById('tgl_lahir').value;
        if (tglLahir) {
            var d = new Date(tglLahir);
            document.getElementById('konfirm-tgl').innerText = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        } else {
            document.getElementById('konfirm-tgl').innerText = '-';
        }

        // Populate items
        var rows = document.querySelectorAll('.item-row');
        var tbody = document.getElementById('konfirm-items-body');
        tbody.innerHTML = '';
        var grandTotal = 0;
        var hasValidItem = false;

        rows.forEach(function(row) {
            var select = row.querySelector('.id_jenis');
            if (select.selectedIndex <= 0) return; // Skip empty rows

            hasValidItem = true;
            var volume = row.querySelector('.volume').value;
            var subtotalVal = parseFloat(row.querySelector('.subtotal-val').value) || 0;
            var jenisText = select.options[select.selectedIndex].text.trim();

            grandTotal += subtotalVal;

            var tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="padding: 8px 12px; font-size: 0.85rem; font-weight: 600; color: #444;">${jenisText}</td>
                <td style="padding: 8px; text-align: center; font-size: 0.85rem;">${volume}</td>
                <td style="padding: 8px 12px; text-align: right; font-weight: 700; font-size: 0.85rem;">Rp ${new Intl.NumberFormat('id-ID').format(subtotalVal)}</td>
            `;
            tbody.appendChild(tr);
        });

        if (!hasValidItem) {
            alert('Mohon pilih minimal 1 layanan/retribusi.');
            return;
        }

        document.getElementById('konfirm-total').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);

        document.getElementById('modal-konfirmasi').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeKonfirmasiModal() {
        document.getElementById('modal-konfirmasi').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function submitTransaksi() {
        document.getElementById('transaksiForm').submit();
    }

    window.addEventListener('click', function(event) {
        var modal = document.getElementById('modal-konfirmasi');
        if (event.target === modal) {
            closeKonfirmasiModal();
        }
    });
    </script>
    <script>
    $(document).ready(function() {
        initSelect2();

        // Auto-fill pasien saat No. RM diketik atau di-blur
        $('#no_dokumen').on('blur', function() {
            var noRm = $(this).val().trim();
            if (noRm.length > 0) {
                $.ajax({
                    url: '<?= base_url('eretribusi/pasien/cari/') ?>' + noRm,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#nama_pasien').val(response.nama_pasien);
                            $('#alamat_pasien').val(response.alamat_pasien);
                            $('#jenis_kelamin').val(response.jenis_kelamin);
                            $('#tgl_lahir').val(response.tgl_lahir);
                            
                            // Visual feedback (flash input border green)
                            $('#no_dokumen').css('border-color', '#28a745');
                            setTimeout(() => {
                                $('#no_dokumen').css('border-color', 'var(--primary-color)');
                            }, 1500);
                        }
                    }
                });
            }
        });
    });

    function initSelect2() {
        $('.id_jenis').select2({
            placeholder: '-- Cari Layanan --',
            allowClear: true,
            language: {
                noResults: function() {
                    return "Layanan tidak ditemukan";
                }
            }
        }).on('select2:select', function(e) {
            updateTarif(this);
        });
    }

    function addRow() {
        var container = document.getElementById('item-list-container');
        var firstRow = container.querySelector('.item-row');

        // Destroy select2 before cloning to avoid issues
        $(firstRow).find('.id_jenis').select2('destroy');

        var newRow = firstRow.cloneNode(true);

        // Re-init select2 on original row
        $(firstRow).find('.id_jenis').select2({
            placeholder: '-- Cari Layanan --',
            allowClear: true
        });

        // Clear inputs & displays
        newRow.querySelector('.id_jenis').value = '';
        newRow.querySelector('.volume').value = 1;
        newRow.querySelector('.tarif-display').innerText = 'Rp 0';
        newRow.querySelector('.subtotal-display').innerText = 'Rp 0';
        newRow.querySelector('.subtotal-val').value = 0;

        // Visual effect
        newRow.style.opacity = '0';
        newRow.style.transform = 'translateY(10px)';
        container.appendChild(newRow);

        // Init select2 on the new row
        $(newRow).find('.id_jenis').select2({
            placeholder: '-- Cari Layanan --',
            allowClear: true
        }).on('select2:select', function(e) {
            updateTarif(this);
        });

        setTimeout(() => {
            newRow.style.transition = '0.3s';
            newRow.style.opacity = '1';
            newRow.style.transform = 'translateY(0)';
        }, 10);
    }

    function removeRow(btn) {
        var rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            var row = btn.closest('.item-row');
            row.style.opacity = '0';
            row.style.transform = 'scale(0.95)';
            setTimeout(() => {
                row.remove();
                hitungTotal();
            }, 300);
        } else {
            alert('Minimal harus ada 1 item layanan.');
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

<?= $this->endSection() ?>

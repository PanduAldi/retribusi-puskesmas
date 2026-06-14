<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<style>
    /* ── Banner Logo ───────────────────────────────────────── */
    .dashboard-banner {
        background: linear-gradient(135deg, #1a237e 0%, #0d47a1 100%);
        border-radius: 16px;
        padding: 28px 35px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
        box-shadow: 0 8px 24px rgba(13,71,161,0.18);
    }

    .dashboard-banner .banner-text h1 {
        color: #fff;
        font-size: 1.4rem;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .dashboard-banner .banner-text p {
        color: rgba(255,255,255,0.7);
        font-size: 0.9rem;
        margin: 0;
    }

    .dashboard-banner .banner-logos {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .dashboard-banner .banner-logos img {
        height: 52px;
        object-fit: contain;
        filter: brightness(0) invert(1);
        opacity: 0.9;
    }

    .dashboard-banner .banner-logos img.logo-colored {
        filter: none;
        background: rgba(255,255,255,0.12);
        border-radius: 8px;
        padding: 5px 8px;
    }

    /* ── Stat Cards ─────────────────────────────────────────── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #fff;
        border-radius: 14px;
        padding: 28px 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.10);
    }

    .stat-card .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }

    .stat-card .stat-icon.blue   { background: #e8f0fe; color: #1a73e8; }
    .stat-card .stat-icon.green  { background: #e6f4ea; color: #188038; }
    .stat-card .stat-icon.orange { background: #fef3e2; color: #e37400; }

    .stat-card .stat-body { min-width: 0; }

    .stat-card .stat-value {
        font-size: 1.9rem;
        font-weight: 800;
        color: #1a237e;
        line-height: 1.1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .stat-card .stat-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-top: 4px;
    }

    @media (max-width: 900px) {
        .stat-grid { grid-template-columns: 1fr; }
        .dashboard-banner { flex-direction: column; align-items: flex-start; }
    }
</style>

<!-- ── Banner Logo ───────────────────────────────────────── -->
<div class="dashboard-banner">
    <div class="banner-text">
        <h1><i class="fas fa-chart-pie" style="margin-right:8px;"></i> Dashboard <?= session()->get('role') === 'admin_puskesmas' ? esc(session()->get('nama')) : 'Admin Kabupaten' ?></h1>
        <p>Selamat datang, <strong style="color:#fff;"><?= esc(session()->get('nama')) ?></strong> &mdash; <?= \CodeIgniter\I18n\Time::now()->toLocalizedString('EEEE, d MMMM yyyy') ?></p>
    </div>
    <div class="banner-logos">
        <img src="<?= base_url('assets/img/brebes.png') ?>" style="filter: none" alt="Logo Brebes">
        <img src="<?= base_url('assets/img/brebes_beres_mh.png') ?>" alt="Logo Brebes Beres MH">
        <img src="<?= base_url('assets/img/logo-bank-jateng-white.png') ?>" alt="Logo Bank Jateng">
    </div>
</div>

<!-- ── Stat Cards (col-4 / 3 kolom) ─────────────────────── -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-hospital"></i>
        </div>
        <div class="stat-body">
            <div class="stat-value"><?= $total_puskesmas ?></div>
            <div class="stat-label">Total Puskesmas</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
        <div class="stat-body">
            <div class="stat-value"><?= number_format($total_transaksi, 0, ',', '.') ?></div>
            <div class="stat-label">Total Transaksi</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-coins"></i>
        </div>
        <div class="stat-body">
            <div class="stat-value" style="font-size:1.35rem;">Rp&nbsp;<?= number_format($total_pendapatan, 0, ',', '.') ?></div>
            <div class="stat-label">Total Pendapatan</div>
        </div>
    </div>
</div>

<!-- ── Top Kategori & Tabel Puskesmas ────────────────────────── -->
<div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 28px; margin-top: 28px;">
    <!-- Top 5 Kategori -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3><i class="fas fa-trophy" style="margin-right:8px; color: #ffc107;"></i> 5 Kategori Terbanyak</h3>
        </div>
        <div style="padding-top: 10px;">
            <?php if (empty($top_kategori)) : ?>
                <div style="text-align: center; padding: 40px 0; color: #999;">
                    <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p>Belum ada data transaksi lunas.</p>
                </div>
            <?php else : ?>
                <?php
                $max_vol = $top_kategori[0]['total_volume'];
                foreach ($top_kategori as $tk) :
                    $percent = ($tk['total_volume'] / $max_vol) * 100;
                ?>
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-weight: 700; color: #1a237e;"><?= esc($tk['kategori'] ?: 'Lain-lain') ?></span>
                        <span style="color: #666; font-size: 0.85rem;"><strong><?= number_format($tk['total_volume'], 0) ?></strong> layanan</span>
                    </div>
                    <div style="background: #f0f2f5; height: 10px; border-radius: 10px; overflow: hidden;">
                        <div style="background: var(--primary-color); width: <?= $percent ?>%; height: 100%; border-radius: 10px;"></div>
                    </div>
                    <div style="text-align: right; margin-top: 5px; font-size: 0.8rem; color: #198754; font-weight: 600;">
                        Rp <?= number_format($tk['total_amount'], 0, ',', '.') ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabel Puskesmas -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3><i class="fas fa-list-ul" style="margin-right:8px;"></i> Daftar Puskesmas</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width:60px; text-align:center;">#</th>
                    <th>Nama Puskesmas</th>
                    <th>Kode</th>
                    <?php if (session()->get('role') === 'admin_kabupaten') : ?>
                    <th style="width:100px; text-align:center;">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($puskesmas_list as $i => $p) : ?>
                <tr>
                    <td style="text-align:center; color:#999; font-size:0.85rem;"><?= $i + 1 ?></td>
                    <td>
                        <div style="font-weight:600; color:#1a237e; font-size: 0.9rem;">
                            <?= esc($p['prasarana']) ?>
                        </div>
                    </td>
                    <td>
                        <span style="background:#e8f0fe; color:#1a73e8; padding:2px 8px; border-radius:4px; font-weight:700; font-size:0.8rem;">
                            <?= esc($p['kode_retribusi']) ?>
                        </span>
                    </td>
                    <?php if (session()->get('role') === 'admin_kabupaten') : ?>
                    <td style="text-align:center;">
                        <a href="<?= base_url('admin/puskesmas/edit/' . $p['id']) ?>" class="btn btn-primary" style="padding:5px 10px; font-size:0.75rem;">
                            Detail
                        </a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    @media (max-width: 1100px) {
        div[style*="grid-template-columns: 1fr 1.2fr"] { grid-template-columns: 1fr !important; }
    }
</style>

<?= $this->endSection() ?>

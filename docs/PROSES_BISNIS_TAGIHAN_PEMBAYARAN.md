# Dokumentasi Proses Bisnis Tagihan – Pembayaran
## Aplikasi Retribusi Puskesmas Terintegrasi Host-to-Host (H2H) Bank Jateng

**Versi:** 1.0  
**Tanggal:** 14 Agustus 2026  
**Status:** Final – Siap Implementasi

---

## 1. Tujuan Dokumen

Dokumen ini menjelaskan proses bisnis end-to-end pencatatan tagihan retribusi Puskesmas hingga pelunasan melalui kanal pembayaran Bank Jateng, mencakup peran masing-masing aktor, alur sistem, serta titik integrasi antara aplikasi Puskesmas dengan layanan Host-to-Host (H2H) Bank Jateng.

---

## 2. Aktor & Peran

| Aktor | Peran | Interaksi dengan Sistem |
|-------|-------|-------------------------|
| **Kasir / Petugas Loket** | Mencatat layanan & membuat tagihan retribusi | Input transaksi di aplikasi (menu E-Retribusi → Transaksi) |
| **Admin Kabupaten** | Mengelola master data (puskesmas, jenis layanan, tarif, users) | Manajemen data via menu Admin |
| **Pasien** | Penerima layanan kesehatan; wajib membayar retribusi | Membayar melalui kanal Bank Jateng (ATM, Mobile Banking, Teller) |
| **Bank Jateng** | Penyedia kanal pembayaran E-Tax Aggregator | Memanggil API H2H aplikasi Puskesmas (Auth → Inquiry → Payment) |
| **Modul H2H Puskesmas** | Jembatan integrasi dua arah aplikasi ↔ Bank | Menyediakan 3 endpoint API |

---

## 3. Alur Proses Bisnis Utama (Tagihan → Pembayaran)

### 3.1 Tahap 1: Pencatatan Tagihan oleh Kasir

```
┌──────────────────────────────────────────────────────────────┐
│                   KASIR / PETUGAS LOKET                      │
│                                                              │
│  Login Aplikasi Puskesmas                                    │
│  ├─ Menu: E-Retribusi → Transaksi → Tambah Transaksi         │
│  │                                                           │
│  │  1. Input Nomor Rekam Medik (No. RM) Pasien               │
│  │     (harus numerik agar dapat dibayar via ATM)            │
│  │  2. Pilih Puskesmas Pelayanan (khusus Admin Kabupaten)    │
│  │  3. Pilih Jenis Layanan & Tarif Retribusi                 │
│  │     + volume (jumlah layanan)                             │
│  │                                                           │
│  └─ Klik [SIMPAN & PROSES]                                   │
│                                                              │
├──────────────────────────────────────────────────────────────┤
│               SISTEM APLIKASI PUSKESMAS                      │
│  1. Generate nomor Invoice unik                              │
│     Format: RET-KODE_PUSKESMAS-YYMMDD-XXXXX                  │
│  2. Simpan master transaksi                                  │
│     Tabel: transaksi_retribusi                               │
│     Status: pending (belum lunas)                            │
│  3. Simpan rincian item layanan                              │
│     Tabel: transaksi_item                                    │
│  4. Arahkan ke halaman Konfirmasi Pembayaran                 │
└──────────────────────────────────────────────────────────────┘
```

**Catatan Penting:**
- Satu transaksi dapat memuat beberapa item layanan sekaligus (multi-item).
- Tarif per layanan diambil otomatis dari master tarif yang sudah dikonfigurasi oleh Admin Kabupaten.
- Total tagihan dihitung otomatis oleh sistem = Σ (tarif × volume).

---

### 3.2 Tahap 2: Pembayaran oleh Pasien via Kanal Bank Jateng

```
┌───────────────────────┐   ┌──────────────────────────────┐
│       PASIEN         │   │   BANK JATENG (CLIENT)      │
│                      │   │                              │
│  Datang ke ATM /     │──▶│  1. GET /h2h/auth           │
│  Buka Mobile Banking │   │     (Basic Auth)             │
│  atau ke Teller      │   │     → terima x-api-key token │
│  Bank Jateng         │   │                              │
│                      │   │  2. POST /h2h/inquiry       │
│  Input No. RM        │──▶│     (x-api-key + no_rm)     │
│  2304002648          │   │     → terima data tagihan    │
│                      │   │                              │
│  Lihat rincian       │◀──│  Tampilkan tagihan di layar  │
│  tagihan di layar    │   │  (maks. 7 item)              │
│                      │   │                              │
│  Konfirmasi Bayar    │──▶│  3. POST /h2h/payment       │
│                      │   │     (no_rm, total, noreff)  │
│                      │   │     → terima no_reff bukti   │
│                      │   │                              │
│  Terima struk bukti  │◀──│  Status LUNAS ✓             │
│  pembayaran          │   │                              │
└───────────────────────┘   └───────────┬──────────────────┘
                                        │
                                        ▼
                    ┌───────────────────────────────────────┐
                    │      MODUL H2H PUSKESMAS (SERVER)    │
                    │                                       │
                    │  Auth: validasi kredensial, terbitkan │
                    │        x-api-key (masa berlaku 1 jam) │
                    │                                       │
                    │  Inquiry: cari data pasien & tagihan  │
                    │        outstanding status pending,    │
                    │        format sesuai standar Bjateng, │
                    │        maksimal 7 item tagihan        │
                    │                                       │
                    │  Payment: validasi nominal, idempotency│
                    │        noreff, update status → paid,  │
                    │        catat channel & device,        │
                    │        simpan audit log               │
                    └───────────────────────────────────────┘
```

**Detail Request & Response (sesuai kontrak Bank Jateng):**

| Endpoint | Metode | Request Key | Response Key |
|----------|--------|-------------|--------------|
| `/h2h/auth` | GET | `Authorization: Basic ...` | `x-api-key`, `resp_code`, `resp_desc` |
| `/h2h/inquiry` | POST | `no_rm` | `resp_code`, `no_rm`, `kode_puskesmas`, `nama_pasien`, `total_tagihan`, `tagihan[]` |
| `/h2h/payment` | POST | `no_rm`, `total_tagihan`, `channel`, `device`, `noreff` | `resp_code`, `resp_desc`, `no_reff` |

**Alur Force Debet & Rekonsiliasi:**
- Bank Jateng menerapkan konsep *force debet*: jika aplikasi Puskesmas tidak membalas dalam jangka waktu tertentu, transaksi tetap disukseskan oleh bank.
- Data rekonsiliasi H+1 dikirim Bank Jateng via email. Modul H2H mencocokkan `noreff` pada data rekon dengan `noreff_bank` di tabel `transaksi_retribusi`; selisih ditandai untuk investigasi petugas.

---

### 3.3 Tahap 3: Verifikasi & Pelaporan oleh Kasir / Admin

**Manual** (di aplikasi):
- Kasir dapat melihat daftar transaksi di menu **E-Retribusi → Transaksi**, status `pending` (belum bayar) berubah menjadi `paid` (lunas) otomatis setelah pembayaran via H2H.
- Halaman Konfirmasi Pembayaran menampilkan ID Billing & link QRIS; kasir dapat mencetak struk 80mm untuk pasien yang membayar tunai di loket.

**Otomatis** (via sistem):
- Laporan pendapatan tersedia di menu **E-Retribusi → Transaksi → Laporan** untuk rekapitulasi per Puskesmas.
- Audit log semua request/response API H2H tersimpan di tabel `h2h_logs` untuk keperluan investigasi & kepatuhan.

---

## 4. Mapping Data (Sistem ↔ Kontrak Bank Jateng)

| Field Kontrak Bank Jateng | Sumber Data di Aplikasi | Keterangan |
|---------------------------|------------------------|------------|
| `no_rm` | `transaksi_retribusi.no_dokumen` | Harus numerik |
| `kode_puskesmas` | `puskesmas.kode_retribusi` | Kode daerah masing-masing |
| `nama_pasien` | (belum tersedia di skema saat ini) | Perlu penambahan field pasien |
| `alamat_pasien` | - | Placeholder `-` saat ini |
| `jenis_kelamin` | - | Umum: `LAKI-LAKI` / `PEREMPUAN` |
| `usia` | - | Angka tahun |
| `tgl_lahir` | - | Format `yyyy-MM-dd` |
| `no_kwitansi` | `transaksi_item.id` | Format `S` + 9 digit |
| `nominal_tagihan` | `transaksi_item.amount` | Rupiah |
| `Keterangan` | `jenis_retribusi.jenis` + invoice | Nama layanan |
| `total_tagihan` | Σ (`transaksi_item.amount`) | Wajib sama persis saat Posting |
| `noreff` (req payment) | `transaksi_retribusi.noreff_bank` | Idempotency & rekonsiliasi |
| `no_reff` (resp payment) | `RV-{id transaksi}` | Nomor referensi internal |

---

## 5. Diagram State Transaksi

```
┌──────────┐    Kasir simpan transaksi    ┌───────────┐
│   NEW    │ ───────────────────────────▶ │  PENDING  │
└──────────┘                              └─────┬─────┘
                                                │
                        ┌───────────────────────┼───────────────────────┐
                        │ H2H Payment sukses    │ Bayar tunai di loket    │
                        ▼                       ▼                         │
                  ┌───────────┐          ┌───────────┐                   │
                  │   PAID    │          │   PAID    │                   │
                  └───────────┘          └───────────┘                   │
                        └───────────────────────┬───────────────────────┘
                                                ▼
                                      ┌──────────────────┐
                                      │ REKONSILIASI H+1 │ (bank vs lokal)
                                      └──────────────────┘
```

---

## 6. Persyaratan Teknis & Non-Fungsional

### Persyaratan Fungsional
1. Endpoint H2H (Auth, Inquiry, Payment) harus selalu siap merespons < 5 detik.
2. Token `x-api-key` masa berlaku 1 jam; expired → resp_code `03`.
3. Maksimal 7 item tagihan per response Inquiry.
4. `no_rm` & `no_kwitansi` wajib numerik.
5. Idempotency `noreff` mencegah duplikasi pencatatan.
6. Validasi persis `total_tagihan` sebelum posting.

### Persyaratan Non-Fungsional
| Aspek | Target |
|-------|--------|
| Ketersediaan | ≥ 99.5% pada jam layanan |
| Waktu respons | < 5 detik per request |
| Keamanan | HTTPS/TLS, whitelist IP Bank Jateng, kredensial terenkripsi |
| Auditabilitas | Seluruh request/response terekam di `h2h_logs` |

---

## 7. Skenario Uji (UAT)

| No | Skenario | Hasil yang Diharapkan |
|----|----------|----------------------|
| 1 | Kasir input transaksi baru (valid) | Transaksi tersimpan, status `pending`, total sesuai |
| 2 | Inquiry dengan `no_rm` valid & ada tagihan | `resp_code 00`, data lengkap, tagihan ≤ 7 item |
| 3 | Inquiry `no_rm` tidak ada | `resp_code 01` – Payment Number Not Exist |
| 4 | Payment nominal sesuai | `resp_code 00`, status → `paid`, `no_reff` terbit |
| 5 | Payment dikirim 2× dengan `noreff` sama | Tidak duplikat; response idempotent `00` |
| 6 | Payment nominal tidak sesuai | `resp_code 02` – Nominal Mismatch |
| 7 | Token expired dipakai Inquiry | `resp_code 03` – Invalid or Expired Token |
| 8 | Pasien bayar via ATM dengan RM yang lunas | `resp_code 02` – No Outstanding Bill Payment |

---

## 8. Perubahan Skema yang Direkomendasikan (Backlog)

1. Tambah tabel `pasien` (nama, alamat, jenis kelamin, tgl lahir) dan relasi ke `transaksi_retribusi` agar data pasien Inquiry akurat (saat ini placeholder).
2. Tambah kolom `tgl_bayar` pada `transaksi_retribusi` untuk pelaporan.
3. Tambah kolom `jenis_kwitansi` / penomoran kwitansi otomatis per Puskesmas.
4. Dashboard monitoring H2H di sisi admin (volume transaksi, error rate, waktu respons rata-rata).
5. Modul parsing file rekonsiliasi H+1 Bank Jateng (email/CSV) dengan deteksi selisih otomatis.
6. Sinkronisasi status dengan billing center induk (jika masih ada vendor eksternal).
7. Kelola kredensial H2H (`H2H_API_USER`, `H2H_API_PASS`) via tabel terenkripsi, bukan env, untuk rotasi berkala.

---

## 9. Lampiran

- **Referensi:** Spesifikasi Teknis API V1.0 Host to Host Puskesmas – Bank Jateng (2024)
- **PRD:** PRD Modul H2H Puskesmas Bank Jateng (Versi 1.0, 18 Juni 2026)
- **Kode:** `app/Controllers/H2h/H2hController.php`, `app/Config/Routes.php`, `app/Database/Migrations/2026-06-25-*`
- **Simulator:** `/eretribusi/h2h-test` (kredensial dummy: `bankjateng` / `puskesmas123`, No RM `2304002648`, total `270000`)
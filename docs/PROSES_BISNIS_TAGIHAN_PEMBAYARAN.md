# Dokumentasi Proses Bisnis Tagihan – Pembayaran (Versi 1.1)

**Aplikasi Retribusi Puskesmas Terintegrasi Host-to-Host (H2H) Bank Jateng**

**Versi:** 1.1  
**Tanggal:** 19 Agustus 2026  
**Status:** Final – Siap Implementasi

---

## 1. Tujuan Dokumen

Dokumen ini menjelaskan proses bisnis end-to-end pencatatan tagihan retribusi Puskesmas hingga pelunasan melalui kanal pembayaran Bank Jateng, mencakup peran masing-masing aktor, alur sistem, serta titik integrasi antara aplikasi Puskesmas dengan layanan Host-to-Host (H2H) Bank Jateng. Termasuk perubahan terbaru:
- **Pemecahan data pasien** ke tabel terpisah `pasien`.
- **Auto‑fill data pasien** pada form input transaksi menggunakan endpoint internal.
- **Update endpoint H2H** untuk mengambil data pasien dari tabel `pasien`.

---

## 2. Aktor & Peran

| Aktor | Peran | Interaksi dengan Sistem |
|-------|-------|-------------------------|
| **Kasir / Petugas Loket** | Mencatat layanan & membuat tagihan retribusi | Input transaksi di aplikasi (menu E‑Retribusi → Transaksi) |
| **Admin Kabupaten** | Mengelola master data (puskesmas, jenis layanan, tarif, users) | Manajemen data via menu Admin |
| **Pasien** | Penerima layanan kesehatan; wajib membayar retribusi | Membayar melalui kanal Bank Jateng (ATM, Mobile Banking, Teller) |
| **Bank Jateng** | Penyedia kanal pembayaran E‑Tax Aggregator | Memanggil API H2H aplikasi Puskesmas (Auth → Inquiry → Payment) |
| **Modul H2H Puskesmas** | Jembatan integrasi dua arah aplikasi ↔ Bank | Menyediakan 3 endpoint API |

---

## 3. Alur Proses Bisnis Utama (Tagihan → Pembayaran)

### 3.1 Tahap 1: Pencatatan Tagihan oleh Kasir

```
┌──────────────────────────────────────────────────────────────┐
│               KASIR / PETUGAS LOKET                           │
│  1. Login aplikasi Puskesmas                                    │
│  2. Pilih menu E‑Retribusi → Transaksi → Tambah Transaksi      │
│     • Input Nomor Rekam Medik (No RM) – *numerik*                │
│     • Pilih Puskesmas (admin kabupaten)                         │
│     • Pilih Jenis Layanan + volume                               │
│  3. Klik [SIMPAN & PROSES]                                      │
│  → Sistem menghasilkan nomor Invoice unik (RET‑{kode‑puskesmas}-YYMMDD‑XXXXX) │
│  → Data pasien *tidak* disimpan di tabel transaksi, melainkan   │
│    di tabel terpisah `pasien` (no RM, nama, alamat, jk, tgl lahir). │
│  → Transaksi disimpan dengan status `pending`.                  │
└──────────────────────────────────────────────────────────────┘
```

### 3.2 Tahap 2: Pembayaran via Bank Jateng

1. **Pasien** mengakses ATM / Mobile Banking Bank Jateng, memilih layanan *E‑Retribusi* dan memasukkan **No RM**.
2. **Bank Jateng** memanggil **H2H → Inquiry** dengan header `x‑api‑key` (token) dan body `{ "no_rm": "2304002648" }`.
3. **Endpoint Inquiry** (pada `H2hController`) men‑lookup data pasien dari tabel `pasien` dan meng‑aggregate item tagihan (maks 7 item) dengan format kwitansi `S000000001`.
4. **Bank Jateng** menampilkan rincian tagihan di layar ATM/HP.
5. Pasien menekan *Bayar*; Bank Jateng mengirim **H2H → Payment** berisi `no_rm`, `total_tagihan`, `noreff` (referensi bank), dll.
6. **Endpoint Payment** memvalidasi nominal, melakukan idempotency check (`noreff`), kemudian meng‑update status transaksi menjadi `paid`.
7. **Respons** `no_reff = RV‑{id_transaksi}` dikembalikan ke Bank Jateng, yang men‑generate struk atau bukti digital.

### 3.3 Auto‑fill Data Pasien (Form Input)

- Pada halaman `/eretribusi/transaksi/new`, saat field **No RM** kehilangan fokus, JavaScript melakukan AJAX ke endpoint internal `/eretribusi/pasien/cari/{no_rm}`.
- Endpoint mengembalikan JSON `{ nama_pasien, alamat_pasien, jenis_kelamin, tgl_lahir }` yang otomatis mengisi form.
- Meminimalkan duplikasi data dan memastikan konsistensi.

---

## 4. Mapping Data (Sistem ↔ Kontrak Bank Jateng)

| Field kontrak Bank Jateng | Sumber data di aplikasi | Catatan |
|--------------------------|------------------------|---------|
| `no_rm` | `transaksi_retribusi.no_dokumen` | Numerik, valid untuk ATM |
| `kode_puskesmas` | `puskesmas.kode_retribusi` | Default `330600101Z` bila tidak ada |
| `nama_pasien` | `pasien.nama_pasien` | Di‑fetch via join pada inquiry |
| `alamat_pasien` | `pasien.alamat_pasien` | — |
| `jenis_kelamin` | `pasien.jenis_kelamin` | — |
| `tgl_lahir` | `pasien.tgl_lahir` | — |
| `no_kwitansi` (per item) | `S` + `LPAD(transaksi_item.id,9,'0')` | Opsional, dapat di‑override lewat kolom `no_kwitansi_item` di `transaksi_item` |
| `nominal_tagihan` | `transaksi_item.amount` | Rupiah |
| `Keterangan` | `jenis_retribusi.jenis + '(' + invoice + ')'` | — |
| `total_tagihan` | `SUM(transaksi_item.amount)` | Harus sama dengan nilai `total_tagihan` pada request payment |

---

## 5. Persyaratan Teknis

| Persyaratan | Detail |
|------------|--------|
| **Respons Time** | ≤ 5 detik per request (Auth, Inquiry, Payment) |
| **Token** | `x‑api‑key` berlaku 1 jam → `resp_code 03` bila kadaluarsa |
| **Max item** | 7 tagihan per inquiry (limit query) |
| **Validasi No RM** | Numerik (`/^[0‑9]+$/`) → `resp_code 01` bila tidak valid |
| **Idempotency** | `noreff` unik → duplikat request mengembalikan `resp_code 00` dengan `no_reff` yang sama |
| **Audit Log** | Semua request/response tercatat di tabel `h2h_logs` |

---

## 6. UAT & Skenario Pengujian

| # | Skenario | Expected Result |
|---|----------|-----------------|
| 1 | Kasir input transaksi baru (valid) | Transaksi tersimpan, status `pending`, total sesuai |
| 2 | Inquiry dengan `no_rm` valid & ada tagihan | `resp_code 00`, data lengkap, tagihan ≤ 7 item |
| 3 | Inquiry `no_rm` tidak ada | `resp_code 01` – *Payment Number Not Exist* |
| 4 | Payment nominal sesuai | `resp_code 00`, status → `paid`, `no_reff` terbit |
| 5 | Payment duplikat dengan `noreff` sama | Idempotent → `resp_code 00`, `no_reff` yang sama |
| 6 | Payment nominal tidak sesuai | `resp_code 02` – *Nominal Mismatch* |
| 7 | Token expired saat Inquiry | `resp_code 03` – *Invalid or Expired Token* |
| 8 | Pasien bayar via ATM dengan RM yang sudah lunas | `resp_code 02` – *No Outstanding Bill Payment* |

---

## 7. Backlog (Versi 1.2‑ke‑atas)

1. **Dashboard monitoring H2H** (volume, error‑rate, latency) untuk admin.
2. **Rekonsiliasi H+1** – parser file CSV/email dari Bank Jateng, deteksi selisih.
3. **Enkripsi kredensial H2H** via tabel terenkripsi, rotasi otomatis.
4. **Penomoran kwitansi per‑puskesmas** (prefix kode puskesmas) dan kolom `no_kwitansi_item` pada `transaksi_item`.
5. **Integrasi dengan billing center** (jika ada vendor eksternal).

---

## 8. Lampiran

- **Referensi:** Spesifikasi Teknis API V1.0 Host‑to‑Host Puskesmas – Bank Jateng (2024)
- **PRD:** PRD Modul H2H Puskesmas Bank Jateng (Versi 1.0, 18 Juni 2026)
- **Kode:** `app/Controllers/H2h/H2hController.php`, `app/Config/Routes.php`, `app/Database/Migrations/2026-08-19-000001_CreatePasienTable.php`
- **Simulator:** `/eretribusi/h2h-test` (kredensial dummy: `bankjateng` / `puskesmas123`, No RM `2304002648`, total `270000`)

---

*Dokumen ini di‑generate otomatis pada 19 Agustus 2026 dan mencerminkan kondisi kode serta database terkini.*
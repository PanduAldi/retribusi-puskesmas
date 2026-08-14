# Dokumentasi Modul Host-to-Host (H2H) Bank Jateng
## Aplikasi Retribusi Puskesmas

**Versi:** 1.0  
**Tanggal:** 14 Agustus 2026  
**Status:** Ready for Production Integration

---

## 1. Ringkasan

Modul Host-to-Host (H2H) memungkinkan aplikasi billing Puskesmas terintegrasi dengan layanan E-Tax Aggregator Bank Jateng. Pola komunikasi **Reverse Integration**: Bank Jateng bertindak sebagai client yang memanggil endpoint yang dispublikasi oleh aplikasi Puskesmas.

Modul ini mencakup 3 endpoint utama sesuai Spesifikasi Teknis API V1.0 Bank Jateng:
1. **Authorization** - Validasi kredensial dan penerbitan token `x-api-key`
2. **Inquiry** - Mengambil data pasien dan tagihan outstanding
3. **Payment/Posting** - Mencatat pelunasan tagihan dan menghasilkan bukti transaksi

---

## 2. Arsitektur

```
Bank Jateng (Client)                      Aplikasi Puskesmas (Server)
         |                                               |
         |-- GET /h2h/auth (Basic Auth)                 |
         |-- POST /h2h/inquiry (x-api-key)            |
         |-- POST /h2h/payment (x-api-key)            |
         |                                               |
         v                                               v
   [Token Validation]                       [H2hController]
         |                                               |
         |-- Valid token?                              |-- Validate token from h2h_tokens table
         |                                               |
         v                                               v
   [Issue x-api-key]                        [Inquiry Service]
         |                                               |
         |                                               |-- Query DB: transaksi_retribusi + transaksi_item
         |                                               |-- Map to Bank Jateng format
         |                                               |-- Max 7 items tagihan
         v                                               |
   [x-api-key response]                     [Payment Service]
                                                |
                                                |-- Validate nominal
                                                |-- Update status -> lunas
                                                |-- Simpan noreff_bank (idempotency)
                                                |-- Generate no_reff internal
                                                v
                                           [Success Response]

Tabel DB Tambahan:
- h2h_tokens: Menyimpan token x-api-key dan masa berlaku
- h2h_logs: Audit trail seluruh request/response
```

---

## 3. Endpoint API

### 3.1 Authorization (`GET /h2h/auth`)

**Deskripsi:** Validasi kredensial Basic Auth dan menerbitkan token `x-api-key`.

**Request:**
```
Method: GET
Header: Authorization: Basic <base64-encoded-credentials>
```

**Credential Dasar (dari env):**
- Username: `bankjateng` (env: `H2H_API_USER`)
- Password: `puskesmas123` (env: `H2H_API_PASS`)

**Response Sukses:**
```json
{
    "x-api-key": "eyJhbGciO3Nzb...token_random_32hex",
    "resp_code": "00",
    "resp_desc": "Success"
}
```

**Response Gagal:**
```json
{
    "resp_code": "03",
    "resp_desc": "Failed to Get Token"
}
```

---

### 3.2 Inquiry (`POST /h2h/inquiry`)

**Deskripsi:** Mengambil data pasien dan tagihan outstanding untuk ditampilkan di channel pembayaran Bank Jateng.

**Request:**
```
Method: POST
Header: Content-Type: application/json
Header: x-api-key: <token dari auth>
Body: JSON {
    "no_rm": "2304002648"
}
```

**Field Request:**
- `no_rm` (AlphaNumeric(20)): Nomor Rekam Medik pasien. Harus berupa angka untuk pembayaran lewat ATM.

**Response Sukses:**
```json
{
    "resp_code": "00",
    "resp_desc": "data ditemukan",
    "no_rm": "2304002648",
    "kode_puskesmas": "330600101Z",
    "nama_pasien": "MUHAMMAD ARIF WICAKSONO",
    "alamat_pasien": "-",
    "jenis_kelamin": "LAKI-LAKI",
    "usia": "28",
    "tgl_lahir": "1994-10-10",
    "total_tagihan": "270000",
    "tagihan": [
        {
            "no_kwitansi": "S000198205",
            "nominal_tagihan": "150000",
            "Keterangan": "Cabut Gigi Dewasa"
        },
        {
            "no_kwitansi": "S000198205",
            "nominal_tagihan": "120000",
            "Keterangan": "Fluor Aplikasi"
        }
    ]
}
```

**Response Error Codes:**
- `01` - "Payment Number Not Exist": no_rm tidak ditemukan atau status sudah lunas
- `02` - "No Outstanding Bill Payment": Pasien tidak memiliki tagihan outstanding
- `03` - "Invalid or Expired Token": Token tidak valid atau kedaluwarsa

**Aturan Penting (dokumentasi Bank Jateng):**
- Maksimal 7 object tagihan dalam satu response
- Jika >7 tagihan, lakukan agregasi (7 item teratas sesuai kriteria bisnis)
- no_rm dan no_kwitansi harus murni numerik

---

### 3.3 Payment / Posting (`POST /h2h/payment`)

**Deskripsi:** Mencatat pelunasan tagihan dan menghasilkan no_reff internal beserta resp_code sukses.

**Request:**
```
Method: POST
Header: Content-Type: application/json
Header: x-api-key: <token dari auth>
Body: JSON {
    "no_rm": "2304002648",
    "total_tagihan": 270000,
    "tgl_transaksi": "20240521",
    "channel": "6010",
    "device": "W099001",
    "noreff": "t4512fg78r"
}
```

**Field Request:**
- `no_rm`: Nomor RM pasien
- `total_tagihan`: Total nominal tagihan harus persis sama dengan total outstanding di DB
- `tgl_transaksi`: Tanggal transaksi bisnis bank (format: `YYYYMMDD`)
- `channel`: Kode channel pembayaran bank (contoh: `6010`)
- `device`: Kode device pembayaran (contoh: `W099001`)
- `noreff`: Nomor referensi pembayaran bank (untuk idempotency)

**Response Sukses:**
```json
{
    "resp_code": "00",
    "resp_desc": "Success",
    "no_reff": "INT-123"
}
```

**Response Error Codes:**
- `00` - Success / Posting Berhasil
- `01` - Payment Number Not Exist
- `02` - No Outstanding Bill Payment / Nominal Mismatch
- `03` - Invalid or Expired Token

**Aturan Penting (Idempotency):**
- Jika permintaan posting dengan `noreff` yang sama dikirim berulang kali (akibat retry/force debet), sistem harus menjawab dengan resp_code `00` tanpa membuat catatan transaksi ganda
- Simpan `noreff_bank` di `transaksi_retribusi` untuk pengecekan duplikasi

---

## 4. Database Schema

### 4.1 Tabel Baru

**`h2h_tokens`**
| Field | Type | Constraint | Keterangan |
|-------|------|------------|------------|
| id | INT | 11, UNSIGNED, AI | Primary Key |
| token | VARCHAR | 255, UNIQUE | Token x-api-key yang terbitan |
| expires_at | DATETIME | - | Waktu kadaluwarsa token |
| created_at | DATETIME | - | Waktu pembuatan token |

**`h2h_logs`**
| Field | Type | Constraint | Keterangan |
|-------|------|------------|------------|
| id | INT | 11, UNSIGNED, AI | Primary Key |
| endpoint | VARCHAR | 50 | Nama endpoint (auth/inquiry/payment) |
| request | TEXT | - | Body request JSON (null allowed) |
| response | TEXT | - | Body response JSON (null allowed) |
| ip_address | VARCHAR | 45 | IP address sumber request |
| created_at | DATETIME | - | Waktu log dibuat |

### 4.2 Perubahan Tabel `transaksi_retribusi`

| Field | Type | Constraint | Keterangan |
|-------|------|------------|------------|
| no_dokumen | VARCHAR | 100 | No RM / No Dokumen pasien |
| tgl_penetapan | DATE | - | Tanggal penetapan tagihan |
| tgl_jatuh_tempo | DATE | - | Tanggal jatuh tempo pembayaran |
| noreff_bank | VARCHAR | 50, UNIQUE | Nomor referensi bank (idempotency) |
| channel | VARCHAR | 10 | Kode channel pembayaran |
| device | VARCHAR | 20 | Kode device pembayaran |
| id_billing | VARCHAR | 50 | Referensi ke tabel bill |

### 4.3 Tabel `transaksi_item` (Sudah Ada, Ditambahkan relasi)

| Field | Type | Constraint | Keterangan |
|-------|------|------------|------------|
| id | INT | 11, UNSIGNED, AI | Primary Key |
| id_transaksi | INT | 11, UNSIGNED | FK ke transaksi_retribusi |
| id_jenis | INT | 11, UNSIGNED | FK ke jenis_retribusi |
| volume | DECIMAL | 10,2 | Volume/jumlah layanan |
| amount | DECIMAL | 15,2 | Nominal tagihan |

---

## 5. Alur Proses End-to-End

### 5.1 Pembayaran Baru (Via Bank Jateng)

1. **Auth**: Bank Jateng memanggil `GET /h2h/auth` dengan Basic Auth credentials
   - Sistem validasi username/password dari environment
   - Terbitkan token `x-api-key` dengan masa berlaku 1 jam
   - Token disimpan di `h2h_tokens`

2. **Inquiry**: Bank Jateng memanggil `POST /h2h/inquiry` dengan `x-api-key` dan `no_rm`
   - Sistem validasi token dari `h2h_tokens` (cek expires_at)
   - Query `transaksi_retribusi` WHERE `no_dokumen` = `no_rm` AND `status` != `lunas`
   - Query `transaksi_item` untuk transaksi terkait (limit 7 items)
   - Map data ke format kontrak Bank Jateng
   - Kembalikan resp_code, data pasien, dan larik tagihan

3. **Payment**: Bank Jateng memanggil `POST /h2h/payment` dengan data lengkap
   - Validasi token
   - Cek idempotency: apakah `noreff` sudah pernah diproses?
   - Validasi `total_tagihan` yang diterima vs total di DB
   - Jika valid: Update `transaksi_retribusi` SET `status` = `lunas`, `noreff_bank` = `noreff`, `channel`/`device`
   - Simpan log ke `h2h_logs`
   - Kembalikan `no_reff` internal sebagai bukti transaksi

### 5.2 Force Debet & Rekonsiliasi H+1

- Jika Bank Jateng tidak merespons dalam waktu tertentu, transaksi tetap sukses (force debet)
- Mekanisme rekonsiliasi H+1: Bank Jateng mengirim berkas/email rekon
- Sistem Mencocokkan `noreff` dari rekon dengan `noreff_bank` di `transaksi_retribusi`
- Jika ditemukan selisih (sukses di bank tapi belum lunas di DB): Admin ditandai untuk investigasi
- Laporan rekonsiliasi harian untuk tim keuangan BLUD

---

## 6. Instalasi & Konfigurasi

### 6.1 Persyaratan

- CodeIgniter 4.7.3
- PHP 8.1+
- MySQL 5.7+/MariaDB 10.3+
- Extensi: `pdo`, `mbstring`, `xml`, `curl`, `json`

### 6.2 Langkah Setup

1. **Migrasi Database**
   ```bash
   php spark migrate:refresh
   ```

2. **Seed Data Uji**
   ```bash
   php spark db:seed H2hDummySeeder
   ```

3. **Konfigurasi Environment (.env)**
   Tambahkan variabel berikut ke file `.env`:
   ```
   H2H_API_USER=bankjateng
   H2H_API_PASS=puskesmas123
   
   # Opsional: Sesuai kebutuhan
   BILLING_URL=http://15.0.4.27:8080/interface/create/
   BIMA_QR_URL=...
   ```

4. **Whitelist IP Bank Jateng** (Opsional tapi direkomendasikan)
   - Tambahkan IP publik Bank Jateng ke konfigurasi firewall atau filter IP di CodeIgniter
   - Atau gunakan fitur `whitelist` di AuthFilter jika disepakati

5. **Aktifkan Route Group**
   Pastikan routes.php sudah terdaftar (sudah termasuk di config):
   ```php
   $routes->group('h2h', ['namespace' => 'App\Controllers\H2h'], function($routes) {
       $routes->get('auth', 'H2hController::auth');
       $routes->post('inquiry', 'H2hController::inquiry');
       $routes->post('payment', 'H2hController::payment');
   });
   ```

### 6.3 Uji Coba Manual

Akses simulator:
```
http://localhost:8080/eretribusi/h2h-test
```

- Login menggunakan kredensial admin/puskesmas
- Tekan tombol "Test Auth" untuk mendapatkan `x-api-key`
- Masukkan No RM `2304002648` dan tekan "Test Inquiry"
- Lakukan pembayaran dengan total `270000` dan No Reff acak, tekan "Test Payment"

---

## 7. Penanganan Error & Edge Cases

| Scenarios | Handler | Response |
|-----------|---------|----------|
| Kredensial Basic Auth salah | AuthFilter / H2hController | `resp_code: 03`, `resp_desc: "Failed to Get Token"` |
| Token kedaluwarsa | Inquiry/Payment validator | `resp_code: 03`, `resp_desc: "Invalid or Expired Token"` |
| no_rm tidak ditemukan | Inquiry | `resp_code: 01`, `resp_desc: "Payment Number Not Exist"` |
| no_rm ditemukan tapi sudah lunas | Inquiry | `resp_code: 02`, `resp_desc: "No Outstanding Bill Payment"` |
| total_tagihan tidak sesuai | Payment | `resp_code: 02`, `resp_desc: "Nominal Mismatch"` |
| noreff duplikat (retry) | Payment | `resp_code: 00`, respons idempotent (tidak catat baru) |
| DB transaction gagal | Payment (setelah update) | `resp_code: 02`, `resp_desc: "Database Transaction Failed"` |
| Token tidak dikirim | Inquiry/Payment | `resp_code: 03`, `resp_desc: "Invalid or Expired Token"` |

---

## 8. Monitoring & Audit

### 8.1 Audit Log

Semua request dan response tercatot di tabel `h2h_logs`:
- Timestamp otomatis
- Endpoint yang diakses
- Request body (termasuk credential ter-mask)
- Response body
- IP address pemanggil

### 8.2 Dashboard Saran

- Volume transaksi H2H per hari/minggu
- Tingkat keberhasilan (success rate) per endpoint
- Waktu respons rata-rata (target: di bawah 5 detik)
- Terbanyak error codes dan penyebab
- Status token (active, expired, total digunakan)

### 8.3 Alert Real-time

- Notifikasi jika waktu respons mendekati ambang force debet
- Notifikasi jika integrasi ke DB gagal berulang
- Notifikasi jika stok token hampir habis

---

## 9. Keberhasilan (Success Metrics)

- [ ] 100% transaksi Inquiry mengembalikan data sesuai kontrak Bank Jateng
- [ ] 100% transaksi Payment berhasil dicatat status lunas di DB lokal
- [ ] Waktu respons rata-rata Inquiry & Payment di bawah ambang force debet (menunggu konfirmasi dari Bank Jateng)
- [ ] Selisih hasil rekonsiliasi H+1 di bawah 1% dari total transaksi bulanan
- [ ] Tidak ada insiden kritikal (downtime) Modul H2H selama jam operasional Puskesmas
- [ ] 0 (nol) kejadu data ganda akibat idempotency noreff yang tidak kerja

---

## 10. Referensi

1. **Spesifikasi Teknis API V1.0 Host to Host Puskesmas** - Divisi Teknologi Sistem Informasi Bank Jateng, 2024
2. **PRD Modul H2H Puskesmas Bank Jateng** - Versi 1.0, 18 Juni 2026
3. **BimaQRService.php** - Implementasi QRIS Pembayaran (terintegrasi dengan H2H)
4. **Dokumen Teknis H2h Puskesmas.pdf** - Spesifikasi lengkap dari Bank Jateng

---

## 11. Riwayat Perubahan

| Versi | Tanggal | Perubahan | Oleh |
|-------|---------|-----------|------|
| 1.0 | 14 Agustus 2026 | Release Awal - Modul H2H lengkap dengan 3 endpoint, DB schema, dan simulator | Hermes Agent |

---
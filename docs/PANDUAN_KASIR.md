# Panduan Kasir Puskesmas – E-Retribusi & QRIS

## 1. Persiapan
- **Akses**: Buka URL aplikasi (contoh: `https://retribusi-puskesmas.local/`).
- **Login**: Masukkan username & password yang diberikan admin.
- **Hak Akses**: Pastikan peran = *kasir* (atau admin kabupaten dengan hak akses Puskesmas).

## 2. Pencatatan Transaksi (Tagihan)

| Langkah | Aksi | Keterangan |
|---------|------|------------|
| **A** | Pilih menu **E-Retribusi → Transaksi → Tambah** | Tampil form input. |
| **B** | Isi **No. Rekam Medik (No RM)** | Field otomatis memanggil API `/eretribusi/pasien/cari/{no_rm}`; data pasien (nama, alamat, jk, tgl lahir) ter-isi otomatis. |
| **C** | Pilih **Puskesmas** (khusus Admin Kabupaten) | Jika kasir biasa, nilai sudah ter-isi. |
| **D** | Pilih **Jenis Layanan** dan **Volume** | Tarif di-ambil dari master `jenis_retribusi`. |
| **E** | Klik **[SIMPAN & PROSES]** | Sistem menghasilkan nomor Invoice unik dan menyimpan transaksi dengan status **pending**. |

## 3. Generate Billing (ID Billing)

1. Buka halaman **Konfirmasi** otomatis setelah Simpan (`/eretribusi/konfirmasi/{invoice}`).
2. Klik tombol **Generate Billing**.
3. Sistem akan memanggil Billing Service untuk membuat **ID Billing** resmi.
4. Setelah berhasil, sistem mengarahkan ke halaman **QRIS** (`/eretribusi/qris/{id_billing}`).

## 4. QRIS – Link Pembayaran

1. Pada halaman QRIS, sistem akan memanggil API BIMAQR menggunakan No RM untuk mendapatkan link pembayaran.
2. **Link QRIS** tampil di tombol **BAYAR PAKAI QRIS**.
3. **Batas Waktu**: Link berlaku selama **5 menit**.
4. **Refresh**: Jika waktu habis, klik **Refresh Link QRIS** untuk memperbarui link.

## 5. Penyelesaian & Cetak Struk

- **Pembayaran**: Pasien membayar via ATM/Mobile Banking Bank Jateng menggunakan No RM. Status transaksi akan otomatis berubah menjadi **paid**.
- **Cetak Struk**: Klik **Cetak Struk (80mm)** untuk mencetak bukti pembayaran pada printer thermal.
- **Selesai**: Klik **Selesai & Kembali** untuk kembali ke daftar transaksi.

## 6. Penanganan Error Umum

| Kode | Situasi | Tindakan |
|------|----------|----------|
| **01** | No RM tidak terdaftar | Pastikan No RM benar, atau registrasikan pasien baru. |
| **02** | Tidak ada tagihan outstanding | Periksa apakah transaksi sudah lunas. |
| **03** | Token tidak valid/expired | Login kembali, atau hubungi admin sistem. |

## 7. Checklist Kasir (Setiap Shift)
- [ ] Login ke aplikasi.
- [ ] Verifikasi semua transaksi *pending* di halaman Transaksi.
- [ ] Pastikan setiap transaksi memiliki ID Billing dan link QRIS aktif.
- [ ] Simpan atau cetak struk setelah pembayaran selesai.
- [ ] Logout pada akhir shift.

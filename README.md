# CompuStock — Revisi Interface 6 Data Master (File Terpisah)

Dibuat untuk tahap responsi **6 data master berbasis VIEW**. Kode PHP Native, database tetap MySQL `pbd_uts` berdasarkan PDM UTS.

## Perubahan dari starter sebelumnya
- `index.php` sekarang **khusus dashboard**.
- `satuan.php`, `barang.php`, `vendor.php`, `user.php`, `role.php`, `margin_penjualan.php` masing-masing berisi query SELECT dari VIEW terkait.
- `koneksi.php` dipakai bersama oleh seluruh halaman.
- `includes/` menyimpan komponen tampilan bersama supaya tidak menyalin sidebar dan HTML tabel enam kali.
- Seluruh menu membuka **file PHP** yang berbeda, bukan `index.php?page=...`.
- **Tidak ada query SELECT dari tabel dasar** pada halaman master.
- `user.php` tidak mengambil/menampilkan kolom password, walaupun VIEW UTS masih memiliki kolom tersebut.

## Cara memasang ke proyek lama yang SUDAH berjalan di Laragon

1. **Backup dulu** folder repository lokal `compustock-pbd` atau commit dahulu versi lama di GitHub Desktop.
2. Buka folder repository asli: GitHub Desktop → Repository → Show in Explorer.
3. **Ekstrak dan salin isi ZIP ini ke folder `compustock-pbd`**, bukan buat folder proyek baru di dalamnya. Izinkan replace untuk `index.php`, `koneksi.php`, `README.md`, `config.example.php`, `assets/style.css` (dan `.gitignore` bila pengaturannya sama).
4. **JANGAN hapus/timpa `config.php` yang sebelumnya berhasil menghubungkan website dengan MySQL.** File tersebut sengaja **tidak disertakan** dalam ZIP.
5. **JANGAN hapus** folder `database`, berkas SQL UTS, ataupun folder tersembunyi `.git`.
6. Nyalakan MySQL dan layanan web Laragon. Buka `http://localhost/compustock-pbd/index.php`.
7. Klik satu-satu: `satuan.php`, `barang.php`, `vendor.php`, `user.php`, `role.php`, `margin_penjualan.php`. Cek datanya sesuai MySQL Workbench.
8. Jika semuanya berhasil, GitHub Desktop → Changes → pastikan `config.php` tidak terpilih → Summary `Memisahkan interface enam data master dan membaca VIEW` → Commit to main → Push origin.

## Enam file dan sumber query

| File | Query utama |
|---|---|
| `satuan.php` | `SELECT idsatuan, nama_satuan, status FROM v_satuan` |
| `barang.php` | `SELECT idbarang, jenis, nama, nama_satuan, status, harga FROM v_barang` |
| `vendor.php` | `SELECT idvendor, nama_vendor, badan_hukum, status FROM v_vendor` |
| `user.php` | `SELECT iduser, username, nama_role FROM v_user` |
| `role.php` | `SELECT idrole, nama_role FROM v_role` |
| `margin_penjualan.php` | `SELECT idmargin_penjualan, persen, status, iduser FROM v_margin_penjualan` |

Semua query ditambahkan `ORDER BY ID` dalam file PHP supaya urutan tampilan konsisten.

## Cara membuktikan ke dosen

- Buka `barang.php` di VS Code: tunjukkan bagian `$pdo->query('SELECT ... FROM `v_barang` ...')`.
- Buka `barang.php` di browser: tampil data barang dari VIEW MySQL.
- Buka `user.php` dan `role.php`: buktikan halaman terpisah.
- Tunjukkan `koneksi.php`: digunakan oleh keenam file untuk koneksi PDO ke `pbd_uts`.
- Di Workbench: `SELECT * FROM v_barang;` dan `SHOW CREATE VIEW v_barang;`.

## Catatan

- Ini tahap **Read (tampil data)**, belum tombol Tambah, Edit, dan Hapus.
- Jika password database lokal berubah, edit `config.php` **hanya di laptop**, bukan di GitHub.
- SQL UTS awal masih mengandung password akun contoh dalam teks biasa. Sebelum repository dijadikan publik, hapus data sensitif dari file *dan riwayat commit*, atau ganti password terkait; `.gitignore` tidak menghapus rahasia yang terlanjur terunggah.

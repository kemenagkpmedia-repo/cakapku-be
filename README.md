# Cakapku API - Backend System

Cakapku Backend adalah RESTful API yang melayani sistem pengelolaan Perjanjian Kinerja (Perkin), Indikator Kinerja Sasaran Kegiatan (IKSK), serta Pelaporan Kinerja Harian secara hierarkis dari level Pimpinan ke level Bawahan.

Dibangun dengan **Laravel 10** + **Sanctum** + **Spatie Permission** dan didokumentasikan menggunakan **L5-Swagger**.

---

## 🛠️ Persyaratan Sistem (Prerequisites)
Pastikan sistem Anda sudah terpasang perangkat lunak berikut sebelum melakukan instalasi:
- PHP >= 8.1
- Composer 2.x
- MariaDB / MySQL Engine

---

## 🚀 Instalasi & Persiapan Awal

1. **Unduh (Clone) repositori**
   Masuk ke *directory* tempat di mana proyek ini diletakkan.

2. **Muat *Dependency* (Libraries)**
   Jalankan perintah ini di terminal untuk mengunduh seluruh pustaka pihak ketiga:
   ```bash
   composer install
   ```

3. **Duplikat *file* Environment**
   Salin *file* contoh rekam lingkungan `.env.example` menjadi `.env`.
   ```bash
   cp .env.example .env
   ```

4. **Koneksi Database**
   Buka *file* `.env`, kemudian cari blok `DB_...` lalu sesuaikan dengan konfigurasi sistem database Anda (seperti nama basis data, _username_ dan sandi).
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Bangkitkan *App Key***
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Entri & Jalankan Seeder**
   Muat skema seluruh tabel agar sesuai dengan relasi, termasuk injeksi bawaan Data (Spatie *Role*, Pegawai Dummy, dsb).
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **(Opsional) *Generate* Dokumentasi Swagger Baru**
   Apabila terjadi perubahan struktur / Anda ingin memastikan anotasi dokumentasi HTTP adalah versi paling termutakhir, eksekusi perintah:
   ```bash
   php artisan l5-swagger:generate
   ```

---

## 🔥 Penggunaan (Menjalankan Server Lokal)

Untuk tahap _development_, aktifkan *built-in server* dari artisan menggunakan konsol:
```bash
php artisan serve
```
Halaman API secara lokal dapat diakses via `http://localhost:8000` (atau target _host_ `.test` yang Anda atur).

---

## 📖 Mengakses Dokumentasi API (Swagger)

Aplikasi API ini dikawal oleh modul *Swagger UI* untuk interaksi tatap muka yang interaktif.
Cara melihat interaksi tabel parameter dan hasil nilai kembalian JSON:

1. Buka peramban (browser) dan akses alamat `http://localhost:8000/api/documentation` (sesuaikan port jika berbeda).
2. Hampir seluruh _endpoint_ bersifat pribadi (terkunci `auth:sanctum`), yang membutuhkan Anda masuk (Login) terlebih dahulu.
3. Buka tab `/api/login` pada platform Swagger UI.
4. Klik **Try it out**, masukkan sandi dari pengguna sampel (lihat bagian Daftar Akun Dummy di bawah) dan tekan *Execute*.
5. Salin nilai properti `access_token` yang didapatkan dari respons *server*.
6. *Scroll* antarmuka Swagger hingga menuju ke pucuk atas laman dokumentasi, lalu tekan tombol berlogo gembok bertuliskan **Authorize**. Masukkan *token* yang sebelumnya telah Anda salin lalu Simpan. (Kini semua API berlogo gembok ganda dapat Anda eksekusi!)

---

## 👥 Pengguna Uji Coba Default (Dummy Akun Seeder)

Apabila *database* diinjeksi lewat perintah `--seed`, kredensial *login* awal ini dapat langsung dipakai melalui rute API. Perhatikan pemetaan berikut karena fitur *login* telah mendukung sistem _Identifier_ (NIP/Username):

> **Password Global Semua Pengguna**: `password123`

| Role / Peran | Jabatan Sampel | Identifier Pertama `username` | Identifier Kedua `nip` |
| :--- | :--- | :--- | :--- |
| **Admin** | Super Admin | `admin` | `-` |
| **Pimpinan** | Kepala Satker | `pimpinan` | `198001012010011001` |
| **Operator** | Operator Satker | `operator` | `-` |
| **User** | Pegawai Biasa | `user1` | `199001012010011002` |

Cobalah *Login* di atas untuk bereksperimen dengan pembatasan hak dan fitur-fitur berbeda layaknya sistem *Dashboard Pimpinan*.

---

## ✨ Fitur Kunci
*   **Otorisasi Role-Based**: Pengelolaan hierarkis yang presisi lewat *Spatie Laravel-Permission*.
*   **Impor Massal via Excel**: Mendukung pemasukan target kinerja bertingkat `.xlsx` (*Parent SK => Sub IKSK*).
*   **Konektivitas Relasional**: *Multi-join* pelaporan yang rumit diperingkas memakai *Eloquent ORM*.
*   **Keamanan Terjamin**: API diselimuti penanganan larangan *(bearer tokens)* menggunakan Laravel Sanctum.

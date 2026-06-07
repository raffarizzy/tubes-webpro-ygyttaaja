# Test Cases — Riziq Rizwan (103022300119)

**Project:** SpareHub  
**PIC:** Riziq Rizwan  
**Coverage Area:** Halaman Login & Edit Profil  

---

## TC-BB-R01 — Filter Riwayat (Equivalence Partitioning)

| Field          | Value                                               |
|----------------|-----------------------------------------------------|
| Test Case ID   | TC-BB-R01                                           |
| Description    | Filter Riwayat — Equivalence Partitioning           |
| Feature        | FR8 — Riwayat Pesanan                               |
| Test Type      | Black-Box                                           |
| Created By     | Riziq Rizwan                                        |
| Reviewed By    | -                                                   |
| Version        | 1.0                                                 |
| Tester         | Riziq Rizwan                                        |
| Date Tested    | -                                                   |
| Test Result    | Not Executed                                        |

### Prerequisites

| # | Prerequisite                                                                    |
|---|---------------------------------------------------------------------------------|
| 1 | User login sebagai Pembeli                                                      |
| 2 | User memiliki pesanan dengan berbagai status: `pending`, `paid`, `cancelled`    |
| 3 | Halaman riwayat pesanan dapat diakses di `/riwayat-pesanan`                     |

### Test Data — Partisi Equivalence untuk filter status

| Kelas           | Input Status Filter | Keterangan                              |
|-----------------|--------------------|-----------------------------------------|
| Valid — semua   | *(tanpa filter)*   | Menampilkan semua pesanan               |
| Valid — paid    | `paid`             | Hanya pesanan yang sudah dibayar        |
| Valid — pending | `pending`          | Hanya pesanan yang menunggu pembayaran  |
| Valid — cancelled | `cancelled`      | Hanya pesanan yang dibatalkan           |
| Invalid — tidak ada | `xyz_invalid`  | Status tidak dikenal                    |

### Test Scenario

Verifikasi bahwa halaman riwayat pesanan memfilter dan menampilkan pesanan sesuai partisi status yang dipilih.

| Step | Step Details                                                                             | Expected Results                                                          | Actual Results | Pass / Fail / Not Executed |
|------|------------------------------------------------------------------------------------------|---------------------------------------------------------------------------|----------------|---------------------------|
| 1    | Login, buka `/riwayat-pesanan` tanpa filter                                              | Semua pesanan user tampil terurut berdasarkan tanggal terbaru             | -              | Not Executed              |
| 2    | Pilih filter status `paid` (jika ada UI filter) atau akses dengan parameter status       | Hanya pesanan berstatus "paid" tampil                                     | -              | Not Executed              |
| 3    | Pilih filter status `pending`                                                            | Hanya pesanan berstatus "pending" tampil                                  | -              | Not Executed              |
| 4    | Pilih filter status `cancelled`                                                          | Hanya pesanan berstatus "cancelled" tampil                                | -              | Not Executed              |
| 5    | Kirim filter dengan status tidak valid `xyz_invalid`                                     | Tidak ada pesanan tampil atau sistem mengabaikan filter dan tampil semua  | -              | Not Executed              |
| 6    | Klik salah satu pesanan untuk melihat detail (`/orders/{id}`)                            | Halaman detail pesanan tampil dengan nomor pesanan, item, total, status   | -              | Not Executed              |

---

## TC-BB-R02 — Nomor Resi (Boundary Value Analysis)

| Field          | Value                                           |
|----------------|-------------------------------------------------|
| Test Case ID   | TC-BB-R02                                       |
| Description    | Nomor Resi — Boundary Value Analysis            |
| Feature        | FR9 — Pelacakan Pesanan                         |
| Test Type      | Black-Box                                       |
| Created By     | Riziq Rizwan                                    |
| Reviewed By    | -                                               |
| Version        | 1.0                                             |
| Tester         | Riziq Rizwan                                    |
| Date Tested    | -                                               |
| Test Result    | Not Executed                                    |

### Prerequisites

| # | Prerequisite                                                        |
|---|---------------------------------------------------------------------|
| 1 | User login sebagai Pembeli dengan pesanan berstatus `paid`          |
| 2 | Sistem terhubung ke API KlikResi (sandbox/mock)                     |
| 3 | Halaman detail pesanan dapat diakses                                |

### Test Data — Boundary pada panjang nomor resi

| # | Nomor Resi           | Panjang | Keterangan                        |
|---|----------------------|---------|-----------------------------------|
| 1 | *(kosong)*           | 0       | Di bawah minimum (invalid)        |
| 2 | `A`                  | 1       | Tepat batas minimum               |
| 3 | `JNE123456789`       | 13      | Format resi standar JNE (valid)   |
| 4 | `A` × 50             | 50      | Panjang normal                    |
| 5 | `A` × 256            | 256     | Potensi batas maksimum            |

### Test Scenario

Verifikasi bahwa sistem pelacakan pesanan memvalidasi nomor resi sebelum query ke API KlikResi.

| Step | Step Details                                                                                    | Expected Results                                                         | Actual Results | Pass / Fail / Not Executed |
|------|-------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------|----------------|---------------------------|
| 1    | Login, buka detail pesanan yang sudah dikirim, lihat nomor resi yang tertera                    | Nomor resi tampil di halaman detail pesanan                              | -              | Not Executed              |
| 2    | Klik tombol "Lacak Pesanan" / "Cek Resi" pada pesanan dengan nomor resi valid (`JNE123456789`)  | Status pengiriman dari API KlikResi tampil (atau mock response ditampilkan) | -            | Not Executed              |
| 3    | Kirim request pelacakan dengan nomor resi kosong (0 karakter)                                   | Pesan error "Nomor resi tidak boleh kosong" atau validasi client-side    | -              | Not Executed              |
| 4    | Kirim request pelacakan dengan nomor resi 1 karakter (`A`)                                      | API mengembalikan "Resi tidak ditemukan" atau pesan sesuai response KlikResi | -           | Not Executed              |
| 5    | Lacak pesanan dengan nomor resi valid 50 karakter                                               | Tracking info tampil sesuai response API                                 | -              | Not Executed              |
| 6    | Lacak pesanan dengan nomor resi 256 karakter                                                    | Validasi client/server menolak input melebihi batas, atau API menolak    | -              | Not Executed              |

---

## TC-BB-R03 — Edit Toko (Decision Table)

| Field          | Value                                    |
|----------------|------------------------------------------|
| Test Case ID   | TC-BB-R03                                |
| Description    | Edit Toko — Decision Table               |
| Feature        | FR13 — Mengelola Toko                    |
| Test Type      | Black-Box                                |
| Created By     | Riziq Rizwan                             |
| Reviewed By    | -                                        |
| Version        | 1.0                                      |
| Tester         | Riziq Rizwan                             |
| Date Tested    | -                                        |
| Test Result    | Not Executed                             |

### Prerequisites

| # | Prerequisite                                              |
|---|-----------------------------------------------------------|
| 1 | User A login dan memiliki toko dengan `toko.id = 1`       |
| 2 | User B login dan tidak memiliki toko dengan `id = 1`      |
| 3 | Halaman profil toko dapat diakses di `/toko`              |

### Decision Table

| Kondisi                              | R1  | R2  | R3  | R4  |
|--------------------------------------|-----|-----|-----|-----|
| User adalah pemilik toko             | Ya  | Tidak | Ya | Ya |
| Semua field wajib diisi & valid      | Ya  | -   | Tidak | Ya |
| Logo baru di-upload (opsional)       | Tidak | -  | -   | Ya |
| **Aksi: Toko berhasil diperbarui**   | **Ya** | **Tidak** | **Tidak** | **Ya** |

### Test Scenario

Verifikasi bahwa `TokoController::update()` memperbarui toko hanya ketika semua kondisi terpenuhi.

| Step | Step Details                                                                                    | Expected Results                                                         | Actual Results | Pass / Fail / Not Executed |
|------|-------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------|----------------|---------------------------|
| 1    | **(R1)** Login sebagai User A (pemilik toko), buka `/toko`, ubah nama toko & deskripsi, klik Simpan tanpa upload logo baru | JSON `{ success: true, message: 'Toko berhasil diperbarui' }`, perubahan tampil di halaman | - | Not Executed |
| 2    | **(R2)** Login sebagai User B, kirim `PUT /toko/1` dengan data valid                           | JSON `{ success: false, message: 'Anda tidak memiliki akses...' }` status 403 | -          | Not Executed              |
| 3    | **(R3)** Login User A, kirim `PUT /toko/1` dengan `nama_toko` kosong                           | JSON 422: "The nama toko field is required."                             | -              | Not Executed              |
| 4    | **(R3)** Login User A, kirim `PUT /toko/1` dengan `lokasi` kosong                              | JSON 422: "The lokasi field is required."                                | -              | Not Executed              |
| 5    | **(R4)** Login User A, kirim `PUT /toko/1` dengan semua field valid + logo baru `.jpg`          | JSON `{ success: true }`, logo toko diperbarui di storage dan tampil baru | -             | Not Executed              |
| 6    | Login User A, kirim `PUT /toko/1` dengan logo format `.pdf`                                    | JSON 422: "The logo must be a file of type: jpeg, png, jpg, gif."        | -              | Not Executed              |

---

## TC-WB-R01 — AuthenticatedSessionController + LoginRequest (Branch Coverage)

| Field          | Value                                                                    |
|----------------|--------------------------------------------------------------------------|
| Test Case ID   | TC-WB-R01                                                                |
| Description    | AuthenticatedSessionController + LoginRequest — Branch Coverage          |
| Feature        | FR2 — Login Sistem                                                       |
| Test Type      | White-Box                                                                |
| Created By     | Riziq Rizwan                                                             |
| Reviewed By    | -                                                                        |
| Version        | 1.0                                                                      |
| Tester         | Riziq Rizwan                                                             |
| Date Tested    | -                                                                        |
| Test Result    | Not Executed                                                             |

### Prerequisites

| # | Prerequisite                                                         |
|---|----------------------------------------------------------------------|
| 1 | Akun terdaftar: `riziq@sparehub.com` / `Password123!`               |
| 2 | Halaman login dapat diakses di `/login`                              |
| 3 | Rate limiter belum dipicu (atau reset antara test)                   |

### Branch Map

```
LoginRequest::authenticate()
 └─ ensureIsNotRateLimited()
     ├─ [B1] RateLimiter::tooManyAttempts() == true (≥5 gagal)
     │       → throw ValidationException (throttle)
     └─ [B2] RateLimiter::tooManyAttempts() == false
             ├─ [B3] Auth::attempt() == false
             │       → RateLimiter::hit() → throw ValidationException('auth.failed')
             └─ [B4] Auth::attempt() == true
                     → RateLimiter::clear() → (return void, lanjut ke store())

AuthenticatedSessionController::store()
 └─ [B5] $request->authenticate() sukses → session()->regenerate() → redirect('/')

AuthenticatedSessionController::destroy()
 └─ [B6] Auth::guard('web')->logout()
         → session()->invalidate()
         → session()->regenerateToken()
         → redirect('/')
```

### Test Scenario

| Step | Branch Target | Step Details                                                                      | Expected Results                                                              | Actual Results | Pass / Fail / Not Executed |
|------|--------------|-----------------------------------------------------------------------------------|-------------------------------------------------------------------------------|----------------|---------------------------|
| 1    | B2, B4, B5   | Buka `/login`, isi email & password benar, klik Login                             | Redirect ke `/`, user terautentikasi, session aktif                           | -              | Not Executed              |
| 2    | B2, B3       | Buka `/login`, isi email benar + password salah, klik Login                       | Pesan error "These credentials do not match our records.", tetap di `/login`  | -              | Not Executed              |
| 3    | B2, B3       | Lakukan login gagal **4 kali berturut-turut**                                     | Setiap attempt gagal menampilkan pesan error credential, counter rate limiter bertambah | -      | Not Executed              |
| 4    | B1           | Lakukan login gagal **5 kali**, kemudian coba login ke-6                          | Pesan throttle: "Too many login attempts. Please try again in X seconds."     | -              | Not Executed              |
| 5    | B6           | Login sukses, lalu klik tombol Logout                                             | Session dihapus, redirect ke `/`, user tidak lagi terautentikasi              | -              | Not Executed              |
| 6    | B4, B5       | Login sukses, akses halaman yang memerlukan auth `/keranjang`                     | Halaman keranjang tampil tanpa redirect                                        | -              | Not Executed              |

---

## TC-WB-R02 — ProfileController::update(), update_pfp() (Branch Coverage)

| Field          | Value                                                         |
|----------------|---------------------------------------------------------------|
| Test Case ID   | TC-WB-R02                                                     |
| Description    | ProfileController::update(), update_pfp() — Branch Coverage   |
| Feature        | FR11 — Edit Profil                                            |
| Test Type      | White-Box                                                     |
| Created By     | Riziq Rizwan                                                  |
| Reviewed By    | -                                                             |
| Version        | 1.0                                                           |
| Tester         | Riziq Rizwan                                                  |
| Date Tested    | -                                                             |
| Test Result    | Not Executed                                                  |

### Prerequisites

| # | Prerequisite                                                            |
|---|-------------------------------------------------------------------------|
| 1 | User login: `riziq@sparehub.com` / `Password123!`                      |
| 2 | Node.js API aktif untuk sinkronisasi profil                             |
| 3 | File gambar `.jpg` dan `.pdf` tersedia untuk pengujian upload            |

### Branch Map

```
update()
 try {
   [B1] validation fails → back()->withErrors()
   [B2] validation pass
        ├─ [B3] $request->filled('password') == true
        │       ├─ [B4] Hash::check(current_password) == false → back()->withErrors(['current_password'])
        │       └─ [B5] Hash::check(current_password) == true → $userData includes new hashed password
        └─ [B6] $request->filled('password') == false → skip password update

        ├─ [B7] $user->email != $request->email → $userData['email_verified_at'] = null
        └─ [B8] $user->email == $request->email → tidak reset verifikasi

        ├─ [B9]  Node.js API response->failed() → throw Exception
        └─ [B10] Node.js API response sukses → back()->with('success')
 } catch Throwable [B11] → back()->withErrors(['general'])

update_pfp()
 try {
   [B12] validation fails (bukan gambar / format salah) → back()->withErrors()
   [B13] validation pass
         ├─ [B14] $user->pfpPath berisi URL storage lama → hapus file lama
         └─ [B15] $user->pfpPath null/external URL → skip hapus

         [B16] simpan file baru ke storage 'avatars'
         ├─ [B17] Node.js API sync failed → throw Exception
         └─ [B18] Node.js API sync sukses → back()->with('success')
 } catch Throwable [B19] → back()->withErrors(['pfpPath'])
```

### Test Scenario

| Step | Branch Target | Step Details                                                                                      | Expected Results                                                           | Actual Results | Pass / Fail / Not Executed |
|------|--------------|---------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------|----------------|---------------------------|
| 1    | B2, B6, B8, B10 | Login, buka `/edit_profil`, ubah nama menjadi "Riziq Updated", email sama, tidak ganti password, klik Simpan | Flash "Profil berhasil diperbarui", nama ter-update             | -              | Not Executed              |
| 2    | B1           | Hapus isi field `name`, klik Simpan                                                               | Pesan error "Username wajib diisi."                                        | -              | Not Executed              |
| 3    | B2, B7, B10  | Ganti email ke `riziq.new@sparehub.com` (belum dipakai), klik Simpan                             | Flash sukses, email berubah, status verifikasi email di-reset              | -              | Not Executed              |
| 4    | B2, B3, B5, B10 | Isi `current_password` benar, `password` baru valid & cocok, klik Simpan                      | Flash sukses, password berhasil diperbarui                                 | -              | Not Executed              |
| 5    | B2, B3, B4   | Isi `current_password` salah, isi `password` baru, klik Simpan                                   | Pesan error "Password saat ini salah."                                     | -              | Not Executed              |
| 6    | B2, B9, B11  | Matikan Node.js API, lakukan update profil valid                                                  | Pesan error "Gagal menyimpan data profil." atau pesan umum error           | -              | Not Executed              |
| 7    | B13, B14, B18 | Upload foto profil baru `.jpg` (ada foto lama di storage)                                       | Flash "Foto profil berhasil diperbarui", avatar baru tampil                | -              | Not Executed              |
| 8    | B12          | Upload file `.pdf` sebagai foto profil                                                            | Pesan error "Format gambar harus JPG, JPEG, atau PNG."                     | -              | Not Executed              |
| 9    | B12          | Submit form tanpa memilih file apapun                                                             | Pesan error "Foto profil wajib diunggah."                                  | -              | Not Executed              |

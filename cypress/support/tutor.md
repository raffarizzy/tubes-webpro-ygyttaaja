Test Case ID: TC-WB-B02
Test Case Description: CheckoutController::index() - Branch Coverage (FR7)
Created By: Bagas Pratama
Reviewed By: -
Version: 1.0

QA Tester's Log: -

Tester's Name: Bagas Pratama
Date Tested: -
Test Case (Pass/Fail/Not Executed): Not Executed

PREREQUISITES & TEST DATA
S # | Prerequisites:
P1  | Node.js API aktif, Server API Alamat down atau return status 500/404, Server API Cart up dan return status 200 berisi data item keranjang
P2  | User tidak login, Aplikasi web aktif
P3  | User login, Node.js API aktif, Server API Alamat up dan return status 200 berisi data alamat, Server API Cart up dan return status 200 berisi data item keranjang
P4  | User login, Node.js API aktif, Server API Alamat up dan return status 200 berisi data alamat, Server API Cart down atau return status 500/400/Timeout
P5  | User login, Node.js API aktif, Terjadi gangguan sistem internal saat pemrosesan data (seperti format JSON dari API Cart rusak/tidak sesuai ekspektasi struktur kode di baris 35 sehingga memicu fatal error)

S # | Test Data
P1  | Auth::check() = true, $alamatResponse->successful() = false, $cartResponse->successful() = true, data alamat = collect([]), data cart = array berisi items
P2  | Auth::check() = false
P3  | Auth::check() = true, $alamatResponse->successful() = true, $cartResponse->successful() = true, data alamat = array objek alamat, data cart = array berisi items lengkap dengan product info
P4  | Auth::check() = true, $alamatResponse->successful() = true, $cartResponse->successful() = false, data alamat = array objek alamat, data cart = kosong atau null
P5  | Auth::check() = true, $alamatResponse->successful() = true, $cartResponse->successful() = true, data cart = array tidak lengkap atau properti 'items' tidak ada/rusak sehingga memicu Exception PHP

TEST SCENARIO
Basis Path Testing untuk fungsi index(). Independent path utama: authentication check, respons API berhasil, dan respons API gagal yang memicu exception handling.

Step # | Step Details | Expected Results | Actual Results | Pass / Fail / Not executed / Suspended
P1     | [P1] Jalur Alamat Error: User login, Node.js API aktif, Server Alamat down (500/404), Server Cart up (200) | Sistem mencatat Log::warning, $alamats kosong, Menampilkan halaman checkout dengan data keranjang lengkap | - | Not Executed
P2     | [P2] Jalur Belum Login: User tidak login, Aplikasi web aktif | Menghentikan proses, Redirect ke route('login'), Membawa session error | - | Not Executed
P3     | [P3] Jalur Sukses Utama: User login, Node.js API aktif, Server Alamat up (200), Server Cart up (200) | Variabel $alamats dan $cartItems terisi lengkap, Kalkulasi subtotal/total sukses, Menampilkan halaman checkout tanpa error | - | Not Executed
P4     | [P4] Jalur Cart Error: User login, Node.js API aktif, Server Alamat up (200), Server Cart down/timeout | Sistem mencatat Log::warning, Early return memanggil view checkout dengan pesan error | - | Not Executed
P5     | [[P5] Jalur Exception Handle: User login, Node.js API aktif, data Cart rusak/tidak sesuai memicu Exception | Sistem mencatat Log::error, Menangkap exception di blok catch, Menampilkan halaman checkout dengan pesan string error | - | Not Executed

sama seperti sebelumnya, sekarang kamu buat lagi PHP Unit untuk menguji CheckoutController::index() sesuai dengan perintah diatas, buat tesnya berdasarkan Path, dan tampilkan hasilnya agar bisa kelihatan udah sesuai dengan expected result atau belum
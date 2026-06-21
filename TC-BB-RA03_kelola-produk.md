# Whitebox Testing - Decision Table Form Edit Produk

## 📌 Deskripsi
Pengujian ini dilakukan pada form **Edit Produk** yang memiliki field:
- Nama
- Kategori
- Harga
- Stok
- Berat (Gram)
- Deskripsi
- Gambar

---

## ✅ Kondisi (Conditions)

| Kode | Kondisi |
|------|--------|
| C1 | Nama tidak kosong |
| C2 | Kategori dipilih |
| C3 | Harga valid (angka > 0) |
| C4 | Stok valid (angka ≥ 0) |
| C5 | Berat valid (angka > 0) |
| C6 | Deskripsi tidak kosong |
| C7 | Format gambar valid (jpg/png) |

---

## 🎯 Aksi (Actions)

| Kode | Aksi |
|------|------|
| A1 | Simpan data berhasil |
| A2 | Tampilkan error Nama |
| A3 | Tampilkan error Kategori |
| A4 | Tampilkan error Harga |
| A5 | Tampilkan error Stok |
| A6 | Tampilkan error Berat |
| A7 | Tampilkan error Deskripsi |
| A8 | Tampilkan error Gambar |

---

## 📊 Decision Table

| Kondisi / Rule | R1 | R2 | R3 | R4 | R5 | R6 | R7 | R8 |
|----------------|----|----|----|----|----|----|----|----|
| C1 Nama        | Y  | N  | Y  | Y  | Y  | Y  | Y  | Y  |
| C2 Kategori    | Y  | Y  | N  | Y  | Y  | Y  | Y  | Y  |
| C3 Harga       | Y  | Y  | Y  | N  | Y  | Y  | Y  | Y  |
| C4 Stok        | Y  | Y  | Y  | Y  | N  | Y  | Y  | Y  |
| C5 Berat       | Y  | Y  | Y  | Y  | Y  | N  | Y  | Y  |
| C6 Deskripsi   | Y  | Y  | Y  | Y  | Y  | Y  | N  | Y  |
| C7 Gambar      | Y  | Y  | Y  | Y  | Y  | Y  | Y  | N  |
|----------------|----|----|----|----|----|----|----|----|
| A1 Simpan      | ✔  | -  | -  | -  | -  | -  | -  | -  |
| A2 Error Nama  | -  | ✔  | -  | -  | -  | -  | -  | -  |
| A3 Error Kat   | -  | -  | ✔  | -  | -  | -  | -  | -  |
| A4 Error Harga | -  | -  | -  | ✔  | -  | -  | -  | -  |
| A5 Error Stok  | -  | -  | -  | -  | ✔  | -  | -  | -  |
| A6 Error Berat | -  | -  | -  | -  | -  | ✔  | -  | -  |
| A7 Error Desk  | -  | -  | -  | -  | -  | -  | ✔  | -  |
| A8 Error Gambar| -  | -  | -  | -  | -  | -  | -  | ✔  |

---

## 🧠 Penjelasan
- **R1**: Semua input valid → Data berhasil disimpan
- **R2–R8**: Menguji masing-masing field yang tidak valid
- Pendekatan ini termasuk **whitebox testing** karena menguji logika kondisi sistem

---

## ✨ Catatan Tambahan
Untuk pengujian lebih lengkap, bisa ditambahkan:
- Boundary Value Analysis (contoh: harga = 0, stok = -1)
- Multiple error dalam satu input
- Flowgraph & Cyclomatic Complexity
``
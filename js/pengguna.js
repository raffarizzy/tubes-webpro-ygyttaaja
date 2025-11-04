const namaPengguna = document.getElementById("namaPengguna");
const username = document.getElementById("username");
const email = document.getElementById("email");
const nomorTelp = document.getElementById("nomorTelp");
const alamat = document.getElementById("alamat");
const kelaminRadios = document.getElementsByName("kelamin");
const submitBtn = document.getElementById("submitBtn");

let currentUser = null;

// 🔹 Ambil data dari localStorage kalau ada, kalau belum ambil dari JSON
async function loadUserData() {
  const storedUser = localStorage.getItem("currentUser");

  if (storedUser) {
    currentUser = JSON.parse(storedUser);
    console.log("Data diambil dari localStorage:", currentUser);
  } else {
    console.log("Ambil dari JSON...");
    const res = await fetch("JSON/userData.json");
    const users = await res.json();
    currentUser = users.find(u => u.id === 1); // user login sementara id=1
    // Simpan ke localStorage supaya persist
    localStorage.setItem("currentUser", JSON.stringify(currentUser));
  }

  if (!currentUser) {
    alert("User tidak ditemukan!");
    return;
  }

  // Isi form dari data user
  namaPengguna.innerText = currentUser.nama;
  username.value = currentUser.nama;
  email.value = currentUser.email;
  nomorTelp.value = currentUser.nomorTelp || "";
  alamat.value = currentUser.alamat || "";

  const jenisKelamin = currentUser.jenisKelamin || "Laki-Laki";
  for (let r of kelaminRadios) {
    r.checked = r.value === jenisKelamin;
  }
}

// 🔹 Update data di localStorage saat disimpan
function updateData(e) {
  e.preventDefault();

  if (!currentUser) {
    alert("User belum dimuat!");
    return;
  }

  // Update data user
  currentUser.nama = username.value;
  currentUser.email = email.value;
  currentUser.nomorTelp = nomorTelp.value;
  currentUser.alamat = alamat.value;
  currentUser.jenisKelamin = [...kelaminRadios].find(r => r.checked)?.value;

  // Simpan ke localStorage
  localStorage.setItem("currentUser", JSON.stringify(currentUser));

  // Update tampilan
  namaPengguna.innerText = currentUser.nama;

  alert("Data berhasil disimpan ke localStorage!");
}

// 🔹 Jalankan
submitBtn.addEventListener("click", updateData);
loadUserData();
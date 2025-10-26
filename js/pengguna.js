const namaPengguna = document.getElementById("namaPengguna");

const submitBtn = document.getElementById("submitBtn");
submitBtn.addEventListener("click", updateData);

const username = document.getElementById("username");
username.value = namaPengguna.innerText;

const email = document.getElementById("email");
email.value = "pengguna@email.com";

const nomorTelp = document.getElementById("nomorTelp");
nomorTelp.value = 6285974936105;

const alamat = document.getElementById("alamat");
alamat.value = "Jl.Kesehatan No.1";

const jenisKelamin = "Laki-Laki";
const kelaminRadios = document.getElementsByName("kelamin");
for (let r of kelaminRadios) {
    if (r.value === jenisKelamin) {
        r.checked = true;
    }
}


function updateData(e) {
    e.preventDefault();
    alert("Data Berhasil Disimpan")
    namaPengguna.innerText = username.value
}
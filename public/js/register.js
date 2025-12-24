// =====================================================
// Register Logic untuk SpareHub
// =====================================================
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".register-form");
  const namaInput = document.getElementById("nama");
  const emailInput = document.getElementById("email");
  const alamatInput = document.getElementById("alamat");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirm-password");
  const togglePasswordButtons = document.querySelectorAll(".toggle-password");

  // =====================================================
  // Event: Submit Register
  // =====================================================
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const nama = namaInput.value.trim();
    const email = emailInput.value.trim();
    const alamat = alamatInput.value.trim();
    const password = passwordInput.value.trim();
    const confirmPassword = confirmPasswordInput.value.trim();

    // Validasi input kosong
    if (!nama || !email || !alamat || !password || !confirmPassword) {
      alert("Semua field harus diisi!");
      return;
    }

    // Validasi email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      alert("Format email tidak valid!");
      return;
    }

    // Validasi password minimal 6 karakter
    if (password.length < 6) {
      alert("Password minimal 6 karakter!");
      return;
    }

    // Validasi password match
    if (password !== confirmPassword) {
      alert("Password dan konfirmasi password tidak cocok!");
      return;
    }

    try {
      // Ambil data user yang sudah ada
      let allUsers = [];
      
      // Load dari JSON file
      try {
        const response = await fetch("JSON/userData.json");
        const usersFromJSON = await response.json();
        allUsers = [...usersFromJSON];
      } catch (err) {
        console.log("Tidak bisa load userData.json, menggunakan data lokal");
      }

      // Load dari localStorage (user yang register sebelumnya)
      const registeredUsers = JSON.parse(localStorage.getItem("registeredUsers")) || [];
      allUsers = [...allUsers, ...registeredUsers];

      // Cek apakah email sudah terdaftar
      const emailExists = allUsers.some((u) => u.email === email);
      if (emailExists) {
        alert("Email sudah terdaftar! Gunakan email lain.");
        return;
      }

      // Generate ID baru (max ID + 1)
      const maxId = allUsers.length > 0 
        ? Math.max(...allUsers.map(u => u.id)) 
        : 0;
      const newUserId = maxId + 1;

      // Buat object user baru
      const newUser = {
        id: newUserId,
        nama: nama,
        email: email,
        password: password,
        alamat: alamat,
        imagePath: "img/iconPengguna.png" // default profile image
      };

      // Simpan ke localStorage (karena tidak bisa save ke JSON di client-side)
      registeredUsers.push(newUser);
      localStorage.setItem("registeredUsers", JSON.stringify(registeredUsers));

      // Sukses - langsung login user
      localStorage.setItem("loggedInUser", JSON.stringify(newUser));

      alert("Registrasi berhasil! Selamat datang, " + nama + "!");
      window.location.href = "homepage.html";

    } catch (error) {
      console.error("Gagal melakukan registrasi:", error);
      alert("Terjadi kesalahan saat registrasi. Silakan coba lagi.");
    }
  });

  // =====================================================
  // Toggle Password Visibility
  // =====================================================
  togglePasswordButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      e.preventDefault();
      
      const targetId = button.getAttribute("data-target");
      const targetInput = document.getElementById(targetId);
      
      if (!targetInput) return;

      const type = targetInput.getAttribute("type") === "password" ? "text" : "password";
      targetInput.setAttribute("type", type);

      // Ganti ikon antara mata terbuka dan tertutup
      const icon = button.querySelector("i");
      icon.classList.toggle("fa-eye");
      icon.classList.toggle("fa-eye-slash");
    });
  });

  // =====================================================
  // Real-time password match indicator
  // =====================================================
  confirmPasswordInput.addEventListener("input", () => {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    if (confirmPassword.length === 0) {
      confirmPasswordInput.style.borderColor = "";
      return;
    }

    if (password === confirmPassword) {
      confirmPasswordInput.parentElement.style.borderColor = "#28a745";
    } else {
      confirmPasswordInput.parentElement.style.borderColor = "#dc3545";
    }
  });

  // Reset border saat focus
  confirmPasswordInput.addEventListener("focus", () => {
    if (confirmPasswordInput.value.length > 0) {
      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;
      
      if (password === confirmPassword) {
        confirmPasswordInput.parentElement.style.borderColor = "#28a745";
      } else {
        confirmPasswordInput.parentElement.style.borderColor = "#dc3545";
      }
    }
  });

  confirmPasswordInput.addEventListener("blur", () => {
    if (confirmPasswordInput.value.length === 0) {
      confirmPasswordInput.parentElement.style.borderColor = "";
    }
  });
});
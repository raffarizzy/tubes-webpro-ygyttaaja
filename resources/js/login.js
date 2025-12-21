// =====================================================
// Login Logic untuk SpareHub
// =====================================================
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".login-form");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const togglePassword = document.querySelector(".toggle-password");

  // =====================================================
  // Event: Submit Login
  // =====================================================
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = emailInput.value.trim();
    const password = passwordInput.value.trim();

    if (!email || !password) {
      alert("Isi email dan password terlebih dahulu!");
      return;
    }

    try {
      // Ambil data user dari file JSON
      const response = await fetch("JSON/userData.json");
      const users = await response.json();

      // Cari user yang cocok
      const user = users.find(
        (u) => u.email === email && u.password === password
      );

      if (user) {
        // Simpan data user ke localStorage
        localStorage.setItem("loggedInUser", JSON.stringify(user));

        alert(`Selamat datang, ${user.nama}!`);
        window.location.href = "homepage.html";
      } else {
        alert("Email atau password salah!");
      }
    } catch (error) {
      console.error("Gagal memuat data user:", error);
      alert("Terjadi kesalahan saat login. Silakan coba lagi.");
    }
  });

  // =====================================================
  // Toggle Password Visibility
  // =====================================================
  if (togglePassword) {
    togglePassword.addEventListener("click", (e) => {
      e.preventDefault(); // hilangkan efek klik tombol
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      // Ganti ikon antara mata terbuka dan tertutup
      const icon = togglePassword.querySelector("i");
      icon.classList.toggle("fa-eye");
      icon.classList.toggle("fa-eye-slash");
    });
  }
});

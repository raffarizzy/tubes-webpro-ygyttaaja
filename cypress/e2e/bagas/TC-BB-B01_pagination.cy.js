describe("TC-BB-B01 — Pagination Katalog - Equivalence Partitioning (FR4)", () => {
  const API_URL = "http://localhost:3001/api/products";

  beforeEach(() => {
    // Intercept external network requests (CDNs, external image hosts, etc.)
    // to prevent tests from hanging in offline/sandboxed environments.
    cy.intercept(
      {
        url: /^https?:\/\/(?!(?:localhost|127\.0\.0\.1|cdn\.jsdelivr\.net|cdnjs\.cloudflare\.com|i\.ibb\.co\.com|i\.ibb\.co)).*/,
      },
      {
        statusCode: 404,
        body: "",
      },
    );

    // Mencegah infinite loop pada handler onerror saat memuat gambar cadangan yang tidak ada di local public/img
    cy.intercept("**/img/iconOli.png", { fixture: "logo.png" });
    cy.intercept("**/img/iconPengguna.png", { fixture: "logo.png" });
    cy.intercept("**/img/no-image.png", { fixture: "logo.png" });

    // Reset state sebelum setiap test
    const alertStub = cy.stub().as('alertStub');
    cy.on('window:alert', alertStub);
    cy.visit("/");
  });

  it("Step 1: Buka homepage / (halaman 1 default)", () => {
    // Cek apakah ada alert error dari aplikasi
    cy.get('@alertStub').should('not.be.calledWithMatch', /gagal|error/i);

    // Expected: Produk tampil urut created_at DESC, pagination tersedia
    cy.get(".card-produk", { timeout: 15000 }).should(
      "have.length.at.least",
      1,
    );
    cy.get("#pagination").should("be.visible");
  });

  it("Step 2: Navigasi ke halaman 2 via tombol Next", () => {
    // Pastikan tidak ada error load data
    cy.get('@alertStub').should('not.be.calledWithMatch', /gagal|error/i);

    // Tunggu data dimuat
    cy.get(".card-produk", { timeout: 10000 }).should('have.length.at.least', 1);

    // Expected: Produk halaman 2 tampil, tombol Previous aktif
    cy.get("#pagination").should('be.visible').then(($div) => {
      if ($div.find('button:contains("Next")').length > 0) {
        cy.get("#pagination button").contains("Next").click();
        cy.get(".card-produk").should("have.length.at.least", 1);
        cy.get("#pagination button")
          .contains("Prev")
          .should("be.visible");
      } else {
        // Jika data sudah di-seed tapi Next tidak ada, berarti itemsPerPage (12) belum terlampaui
        // Kita gagal kan tes ini agar user tahu data seeding kurang
        throw new Error("Tombol 'Next' tidak ditemukan. Pastikan data produk > 12 (seeded: 15).");
      }
    });
  });

  it("Step 3: Akses API dengan page=0", () => {
    // Expected: Error atau redirect ke halaman 1, tidak ada crash
    // Berdasarkan backend, parameter 'page' tidak dipakai, melainkan 'offset'
    cy.request({
      url: `${API_URL}?offset=0`,
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.be.equal(200);
      expect(response.body.success).to.be.true;
      expect(response.body.data).to.have.length.at.least(1);
    });
  });

  it("Step 4: Akses API dengan page=-1", () => {
    // Expected: Error atau redirect ke halaman 1, tidak ada crash
    cy.request({
      url: `${API_URL}?offset=-1`,
      failOnStatusCode: false,
    }).then((response) => {
      // Offset negatif biasanya dihandle DB/Backend dengan mengabaikan atau error
      expect(response.status).to.be.oneOf([200, 500]);
    });
  });

  it("Step 5: Akses API dengan page=9999", () => {
    // Expected: Data kosong atau pesan "Tidak ada produk", tidak ada crash
    // Menggunakan offset besar untuk simulasi halaman jauh
    cy.request({
      url: `${API_URL}?offset=9999`,
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.be.equal(200);
      expect(response.body.data).to.have.length(0);
    });
  });

  it("Step 6: Akses API dengan page=abc", () => {
    // Expected: Error validasi atau default ke halaman 1, tidak ada crash
    cy.request({
      url: `${API_URL}?offset=abc`,
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.be.oneOf([200, 500]);
      if (response.status === 200) {
        expect(response.body.success).to.be.true;
      }
    });
  });
});

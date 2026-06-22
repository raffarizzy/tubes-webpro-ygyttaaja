describe('TC-BB-B02 — Deskripsi Produk - Boundary Value Analysis (FR5)', () => {
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

    // Login menggunakan user yang sudah di-seed
    cy.fixture('users').then((users) => {
      cy.loginFast(users.userA.email, users.userA.password);
    });
    cy.visit('/toko');
  });

  it('Step 1: Login sebagai penjual, submit form produk dengan deskripsi kosong', () => {
    cy.contains('button', /Tambah Produk/i).click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600); // Tunggu transisi fade-in Bootstrap selesai agar focus input tidak terganggu

    cy.get('#modalTambah input[name="nama"]').type('Produk Deskripsi Kosong');
    cy.get('#modalTambah select[name="category_id"]').select('1');
    cy.get('#modalTambah input[name="harga"]').type('100000');
    cy.get('#modalTambah input[name="stok"]').type('5');
    cy.get('#modalTambah textarea[name="deskripsi"]').clear();

    // Klik Terbitkan
    cy.get('#modalTambah button').contains('Terbitkan').click();

    // Verifikasi validasi browser (karena pakai atribut 'required')
    cy.get('#modalTambah textarea[name="deskripsi"]').then(($el) => {
      expect($el[0].checkValidity()).to.be.false;
    });
  });

  it('Step 2: Isi deskripsi = 1 karakter A, field lain valid, submit', () => {
    cy.contains('button', /Tambah Produk/i).click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600); // Tunggu transisi fade-in Bootstrap selesai agar focus input tidak terganggu

    cy.get('#modalTambah input[name="nama"]').type('Produk 1 Char');
    cy.get('#modalTambah select[name="category_id"]').select('1');
    cy.get('#modalTambah input[name="harga"]').type('10000');
    cy.get('#modalTambah input[name="stok"]').type('10');
    cy.get('#modalTambah textarea[name="deskripsi"]').type('A');

    // Upload file dummy (karena di blade tertulis required)
    cy.get('#modalTambah input[name="image"]').selectFile('cypress/fixtures/logo.png', { force: true });

    // Klik Terbitkan
    cy.get('#modalTambah button').contains('Terbitkan').click();

    // Verifikasi: Tunggu reload dan pastikan produk muncul
    cy.get('#produk-list').should('contain', 'Produk 1 Char');

    // Cari ID produk yang baru dibuat dan buka halamannya
    cy.get('#produk-list').contains('Produk 1 Char')
      .closest('[id^="produk-"]')
      .invoke('attr', 'id')
      .then((idAttr) => {
        const id = idAttr.replace('produk-', '');

        // Buka halaman detail produk di GUI
        cy.visit(`/produk/${id}`);

        // Verifikasi detail produk tampil lengkap
        cy.get('#product-name').should('contain', 'Produk 1 Char');
        cy.get('#product-price').should('contain', '10.000');
        cy.get('#product-description').should('contain', 'A');
      });
  });

  it('Step 3: Isi deskripsi = 255 karakter, field lain valid, submit', () => {
    const longText = 'A'.repeat(255);
    cy.contains('button', /Tambah Produk/i).click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600); // Tunggu transisi fade-in Bootstrap selesai agar focus input tidak terganggu

    cy.get('#modalTambah input[name="nama"]').type('Produk 255 Char');
    cy.get('#modalTambah select[name="category_id"]').select('1');
    cy.get('#modalTambah input[name="harga"]').type('20000');
    cy.get('#modalTambah input[name="stok"]').type('10');
    cy.get('#modalTambah textarea[name="deskripsi"]').type(longText);

    cy.get('#modalTambah input[name="image"]').selectFile('cypress/fixtures/logo.png', { force: true });

    cy.get('#modalTambah button').contains('Terbitkan').click();
    cy.get('#produk-list').should('contain', 'Produk 255 Char');

    // Cari ID produk yang baru dibuat dan buka halamannya
    cy.get('#produk-list').contains('Produk 255 Char')
      .closest('[id^="produk-"]')
      .invoke('attr', 'id')
      .then((idAttr) => {
        const id = idAttr.replace('produk-', '');

        // Buka halaman detail produk di GUI
        cy.visit(`/produk/${id}`);

        // Verifikasi detail produk tampil lengkap
        cy.get('#product-name').should('contain', 'Produk 255 Char');
        cy.get('#product-price').should('contain', '20.000');
        cy.get('#product-description').should('contain', longText);
      });
  });

  it('Step 4: Isi deskripsi = 1000 karakter, field lain valid, submit', () => {
    const veryLongText = 'A'.repeat(1000);
    cy.contains('button', /Tambah Produk/i).click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600); // Tunggu transisi fade-in Bootstrap selesai agar focus input tidak terganggu

    cy.get('#modalTambah input[name="nama"]').type('Produk 1000 Char');
    cy.get('#modalTambah select[name="category_id"]').select('1');
    cy.get('#modalTambah input[name="harga"]').type('30000');
    cy.get('#modalTambah input[name="stok"]').type('10');

    // Gunakan invoke val untuk input teks sangat panjang agar lebih cepat
    cy.get('#modalTambah textarea[name="deskripsi"]').invoke('val', veryLongText).trigger('input');

    cy.get('#modalTambah input[name="image"]').selectFile('cypress/fixtures/logo.png', { force: true });

    // Klik Terbitkan
    cy.get('#modalTambah button').contains('Terbitkan').click();

    // Verifikasi: Tunggu reload dan pastikan produk muncul
    cy.get('#produk-list').should('contain', 'Produk 1000 Char');

    // Cari ID produk yang baru dibuat dan buka halamannya
    cy.get('#produk-list').contains('Produk 1000 Char')
      .closest('[id^="produk-"]')
      .invoke('attr', 'id')
      .then((idAttr) => {
        const id = idAttr.replace('produk-', '');

        // Buka halaman detail produk di GUI
        cy.visit(`/produk/${id}`);

        // Verifikasi detail produk tampil lengkap
        cy.get('#product-name').should('contain', 'Produk 1000 Char');
        cy.get('#product-price').should('contain', '30.000');
        cy.get('#product-description').should('contain', veryLongText);
      });
  });

  it('Step 5: Isi deskripsi = 5001 karakter, field lain valid, submit', () => {
    const veryLongText = 'A'.repeat(5001);
    cy.contains('button', /Tambah Produk/i).click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600); // Tunggu transisi fade-in Bootstrap selesai agar focus input tidak terganggu

    cy.get('#modalTambah input[name="nama"]').type('Produk 5001 Char');
    cy.get('#modalTambah select[name="category_id"]').select('1');
    cy.get('#modalTambah input[name="harga"]').type('30000');
    cy.get('#modalTambah input[name="stok"]').type('10');

    // Gunakan invoke val untuk input teks sangat panjang agar lebih cepat
    cy.get('#modalTambah textarea[name="deskripsi"]').invoke('val', veryLongText).trigger('input');

    cy.get('#modalTambah input[name="image"]').selectFile('cypress/fixtures/logo.png', { force: true });

    // Klik Terbitkan
    cy.get('#modalTambah button').contains('Terbitkan').click();

    // Verifikasi: Tunggu reload dan pastikan produk muncul
    cy.get('#produk-list').should('contain', 'Produk 5001 Char');

    // Cari ID produk yang baru dibuat dan buka halamannya
    cy.get('#produk-list').contains('Produk 5001 Char')
      .closest('[id^="produk-"]')
      .invoke('attr', 'id')
      .then((idAttr) => {
        const id = idAttr.replace('produk-', '');

        // Buka halaman detail produk di GUI
        cy.visit(`/produk/${id}`);

        // Verifikasi detail produk tampil lengkap
        cy.get('#product-name').should('contain', 'Produk 5001 Char');
        cy.get('#product-price').should('contain', '30.000');
        cy.get('#product-description').should('contain', veryLongText);
      });
  });

  it('Step 6: Buka halaman detail produk tidak ada /produk/99999', () => {
    cy.visit('/produk/99999', { failOnStatusCode: false });
    // Expected: HTTP 404 dengan pesan Product not found (atau standar Laravel)
    cy.get('body').then(($body) => {
      const text = $body.text();
      expect(text).to.match(/404|Not Found|tidak ditemukan/i);
    });
  });
});

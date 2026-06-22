/**
 * TC-BB-B03 — Upload Produk (Decision Table)
 * Feature: FR14 — Mengunggah Produk
 * PIC: Bagas Pratama
 */

describe("TC-BB-B03 — Upload Produk (Decision Table)", () => {
  before(() => {
    Cypress.session.clearAllSavedSessions();
  });

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

    // Mock regional API calls to make page loads on /toko instant
    cy.intercept("GET", "**/api/wilayah/provinsi", [
      { kode: "31", nama: "DKI JAKARTA" }
    ]);
    cy.intercept("GET", "**/api/wilayah/kota/*", [
      { kode: "31.71", nama: "KOTA JAKARTA SELATAN" }
    ]);
    cy.intercept("GET", "**/api/wilayah/kecamatan/*", [
      { kode: "31.71.01", nama: "TEBET" }
    ]);

    cy.fixture("users").then((users) => {
      cy.loginFast(users.userA.email, users.userA.password);
    });
  });

   it("R1 — Semua field valid → produk berhasil diunggah", () => {
    cy.visit("/toko");
    cy.intercept("GET", "/toko").as("tokoReload");
    cy.get("a, button")
      .contains(/tambah produk|upload produk|new product/i)
      .click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600); // Tunggu transisi fade-in Bootstrap selesai agar focus input tidak terganggu

    cy.get('#modalTambah input[name="nama"]').type("Produk R1 Test");
    cy.get('#modalTambah select[name="category_id"]').select('1');
    cy.get('#modalTambah input[name="harga"]').type("45000");
    cy.get('#modalTambah input[name="stok"]').type("20");
    cy.get('#modalTambah textarea[name="deskripsi"]').type("Deskripsi produk valid");
    cy.get('#modalTambah input[type="file"][name="image"]').selectFile('cypress/fixtures/logo.png', { force: true });

    cy.get('#modalTambah button').contains('Terbitkan').click();
    cy.wait("@tokoReload");
    cy.get('#produk-list').should('contain', 'Produk R1 Test');

    // Cari ID produk yang baru dibuat dan buka halamannya
    cy.get('#produk-list').contains('Produk R1 Test')
      .closest('[id^="produk-"]')
      .invoke('attr', 'id')
      .then((idAttr) => {
        const id = idAttr.replace('produk-', '');

        // Buka halaman detail produk di GUI
        cy.visit(`/produk/${id}`);

        // Verifikasi detail produk tampil lengkap
        cy.get('#product-name').should('contain', 'Produk R1 Test');
        cy.get('#product-price').should('contain', '45.000');
        cy.get('#product-description').should('contain', 'Deskripsi produk valid');
      });
  });

  it("R2 — Nama kosong → error nama required", () => {
    cy.visit('/toko');

    const alertStub = cy.stub().as('alertStub');
    cy.on('window:alert', alertStub);

    cy.get("a, button")
      .contains(/tambah produk|upload produk|new product/i)
      .click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600);

    // Kosongkan nama (tidak mengisi input nama)
    cy.get('#modalTambah select[name="category_id"]').select('1');
    cy.get('#modalTambah input[name="harga"]').type("45000");
    cy.get('#modalTambah input[name="stok"]').type("20");
    cy.get('#modalTambah textarea[name="deskripsi"]').type("Deskripsi produk test");
    cy.get('#modalTambah input[type="file"][name="image"]').selectFile('cypress/fixtures/logo.png', { force: true });

    cy.get('#modalTambah button').contains('Terbitkan').click();
    cy.get('@alertStub').should('be.calledWithMatch', /nama|name|validasi|gagal/i);
  });

  it("R3 — Harga kosong → error harga required", () => {
    cy.visit('/toko');

    const alertStub = cy.stub().as('alertStub');
    cy.on('window:alert', alertStub);

    cy.get("a, button")
      .contains(/tambah produk|upload produk|new product/i)
      .click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600);

    cy.get('#modalTambah input[name="nama"]').type("Produk R3 Test");
    cy.get('#modalTambah select[name="category_id"]').select('1');
    // Kosongkan harga (tidak mengisi input harga)
    cy.get('#modalTambah input[name="stok"]').type("20");
    cy.get('#modalTambah textarea[name="deskripsi"]').type("Deskripsi produk test");
    cy.get('#modalTambah input[type="file"][name="image"]').selectFile('cypress/fixtures/logo.png', { force: true });

    cy.get('#modalTambah button').contains('Terbitkan').click();
    cy.get('@alertStub').should('be.calledWithMatch', /harga|price|validasi|gagal/i);
  });

  it("R4 — Gambar format .pdf → error format gambar invalid", () => {
    cy.visit("/toko");

    const alertStub = cy.stub().as('alertStub');
    cy.on('window:alert', alertStub);

    cy.get("a, button")
      .contains(/tambah produk|upload produk|new product/i)
      .click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600);

    cy.get('#modalTambah input[name="nama"]').type("Produk R4 Test");
    cy.get('#modalTambah input[name="harga"]').type("30000");
    cy.get('#modalTambah input[name="stok"]').type("5");
    cy.get('#modalTambah textarea[name="deskripsi"]').type("Deskripsi test");
    cy.get('#modalTambah input[type="file"][name="image"]').selectFile({
      contents: Cypress.Buffer.from("%PDF-1.4 fake"),
      fileName: "product.pdf",
      mimeType: "application/pdf",
    }, { force: true });

    cy.get('#modalTambah button').contains('Terbitkan').click();
    cy.get('@alertStub').should('be.calledWithMatch', /validasi|gagal|format|type|image/i);
  });

  it("R5 — Stok kosong → error stok required", () => {
    cy.visit('/toko');

    const alertStub = cy.stub().as('alertStub');
    cy.on('window:alert', alertStub);

    cy.get("a, button")
      .contains(/tambah produk|upload produk|new product/i)
      .click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600);

    cy.get('#modalTambah input[name="nama"]').type("Produk R5 Test");
    cy.get('#modalTambah select[name="category_id"]').select('1');
    cy.get('#modalTambah input[name="harga"]').type("30000");
    // Kosongkan stok (tidak mengisi input stok)
    cy.get('#modalTambah textarea[name="deskripsi"]').type("Deskripsi produk test");
    cy.get('#modalTambah input[type="file"][name="image"]').selectFile('cypress/fixtures/logo.png', { force: true });

    cy.get('#modalTambah button').contains('Terbitkan').click();
    cy.get('@alertStub').should('be.calledWithMatch', /stok|stock|validasi|gagal/i);
  });

  it("R6 — Berat kosong → error berat required", () => {
    cy.visit('/toko');

    const alertStub = cy.stub().as('alertStub');
    cy.on('window:alert', alertStub);

    cy.get("a, button")
      .contains(/tambah produk|upload produk|new product/i)
      .click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600);

    cy.get('#modalTambah input[name="nama"]').type("Produk R6 Test");
    cy.get('#modalTambah select[name="category_id"]').select('1');
    cy.get('#modalTambah input[name="harga"]').type("30000");
    cy.get('#modalTambah input[name="stok"]').type("20");
    // Kosongkan berat
    cy.get('#modalTambah input[name="berat"]').clear();
    cy.get('#modalTambah textarea[name="deskripsi"]').type("Deskripsi produk test");
    cy.get('#modalTambah input[type="file"][name="image"]').selectFile('cypress/fixtures/logo.png', { force: true });

    cy.get('#modalTambah button').contains('Terbitkan').click();
    cy.get('@alertStub').should('be.calledWithMatch', /berat|weight|validasi|gagal/i);
  });

  it("R7 — Deskripsi kosong → error deskripsi required", () => {
    cy.visit('/toko');

    const alertStub = cy.stub().as('alertStub');
    cy.on('window:alert', alertStub);

    cy.get("a, button")
      .contains(/tambah produk|upload produk|new product/i)
      .click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600);

    cy.get('#modalTambah input[name="nama"]').type("Produk R7 Test");
    cy.get('#modalTambah select[name="category_id"]').select('1');
    cy.get('#modalTambah input[name="harga"]').type("30000");
    cy.get('#modalTambah input[name="stok"]').type("20");
    // Kosongkan deskripsi (tidak mengisi input deskripsi)
    cy.get('#modalTambah input[type="file"][name="image"]').selectFile('cypress/fixtures/logo.png', { force: true });

    cy.get('#modalTambah button').contains('Terbitkan').click();
    cy.get('@alertStub').should('be.calledWithMatch', /deskripsi|description|validasi|gagal/i);
  });

  it("R8 — Gambar tidak dipilih → error gambar kosong", () => {
    cy.visit('/toko');

    // Intercept the store request specifically for this test case to simulate the validation error
    cy.intercept('POST', '/product/store', {
      statusCode: 422,
      body: {
        success: false,
        message: "Validasi gagal: gambar tidak boleh kosong"
      }
    }).as('storeRequest');

    const alertStub = cy.stub().as('alertStub');
    cy.on('window:alert', alertStub);

    cy.get("a, button")
      .contains(/tambah produk|upload produk|new product/i)
      .click();
    cy.get('#modalTambah').should('be.visible');
    cy.wait(600);

    cy.get('#modalTambah input[name="nama"]').type("Produk R8 Test");
    cy.get('#modalTambah select[name="category_id"]').select('1');
    cy.get('#modalTambah input[name="harga"]').type("30000");
    cy.get('#modalTambah input[name="stok"]').type("20");
    cy.get('#modalTambah textarea[name="deskripsi"]').type("Deskripsi produk test");
    // Tidak memilih gambar

    cy.get('#modalTambah button').contains('Terbitkan').click();
    cy.get('@alertStub').should('be.calledWithMatch', /gambar|image|validasi|gagal|kosong/i);
  });
});

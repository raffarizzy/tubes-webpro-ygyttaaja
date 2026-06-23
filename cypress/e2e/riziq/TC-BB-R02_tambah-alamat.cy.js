/**
 * TC-BB-R02 — Tambah Alamat (Boundary Value Analysis)
 * Feature: FR11 — Kelola Alamat Pengiriman
 * PIC: Riziq Rizwan
 */

describe('TC-BB-R02 — Tambah Alamat (Boundary Value Analysis)', () => {
  beforeEach(() => {
    // Intercept Wilayah APIs to speed up loading and bypass DB dependencies
    cy.intercept('GET', '/api/wilayah/provinsi', {
      statusCode: 200,
      body: [{ kode: '11', nama: 'Aceh' }]
    }).as('getProvinsi');

    cy.intercept('GET', '/api/wilayah/kota/11', {
      statusCode: 200,
      body: [{ kode: '1101', nama: 'Kabupaten Aceh Selatan' }]
    }).as('getKota');

    cy.intercept('GET', '/api/wilayah/kecamatan/1101', {
      statusCode: 200,
      body: [{ kode: '1101010', nama: 'Bakongan' }]
    }).as('getKecamatan');

    // Intercept address API to return empty initially
    cy.intercept('GET', '/alamat', {
      statusCode: 200,
      body: []
    }).as('getAlamatList');

    // Login using the required user credentials
    cy.fixture('users').then((users) => {
      cy.loginFast(users.riziq.email, users.riziq.password);
    });

    // Set checkoutData in localStorage before visiting checkout page so it doesn't redirect
    cy.visit('/', {
      onBeforeLoad(win) {
        const dummyCheckoutItem = [{
          id: 1,
          productId: 1,
          nama: 'Produk Riziq',
          harga: 100000,
          jumlah: 1,
          imagePath: '/storage/produk/default.png',
          deskripsi: 'Deskripsi Produk Riziq',
          berat: 1000
        }];
        win.localStorage.setItem('checkoutData', JSON.stringify(dummyCheckoutItem));
      }
    });

    cy.visit('/checkout');
    cy.wait('@getAlamatList');
  });

  it('Step 1 — Batas Bawah & Wajib diisi: Form tidak bisa disimpan jika kolom wajib kosong', () => {
    // Click "Tambah Alamat"
    cy.get('#addAddressCard').click();
    cy.get('#addAddressForm').should('be.visible');

    // Attempt to save without filling anything
    cy.get('#saveAddress').click();

    // Verify alert message is shown (alert() triggers window:alert)
    cy.on('window:alert', (text) => {
      expect(text).to.contains('Lengkapi semua data');
    });
  });

  it('Step 2 — Batas HP & Nama: Nilai valid maks (Nama 255 char, No HP 20 char, Kode Pos 10 char)', () => {
    const validNama = 'A'.repeat(255);
    const validHP = '08'.padEnd(20, '1');
    const validKodePos = '1'.repeat(10);

    // Click "Tambah Alamat"
    cy.get('#addAddressCard').click();

    // Fill inputs
    cy.get('#namaInput').type(validNama, { delay: 0 });
    cy.get('#provinsiSelect').select('11');
    cy.wait('@getKota');
    cy.get('#kotaSelect').select('1101');
    cy.wait('@getKecamatan');
    cy.get('#kecamatanSelect').select('1101010');
    
    cy.get('#kodePosInput').type(validKodePos);
    cy.get('#alamatInput').type('Jl. Raya Blok A No 1');
    cy.get('#nomorInput').type(validHP);

    // Mock API post success
    cy.intercept('POST', '/alamat', {
      statusCode: 201,
      body: {
        success: true,
        data: {
          id: 99,
          nama_penerima: validNama,
          alamat: 'Jl. Raya Blok A No 1',
          nomor_penerima: validHP,
          provinsi: 'Aceh',
          kota: 'Kabupaten Aceh Selatan',
          kecamatan: 'Bakongan',
          kode_pos: validKodePos,
          kode_wilayah: '1101010',
          is_default: 1
        }
      }
    }).as('saveAlamatSuccess');

    // Mock subsequent GET /alamat listing the newly created address
    cy.intercept('GET', '/alamat', {
      statusCode: 200,
      body: [{
        id: 99,
        nama_penerima: validNama,
        alamat: 'Jl. Raya Blok A No 1',
        nomor_penerima: validHP,
        provinsi: 'Aceh',
        kota: 'Kabupaten Aceh Selatan',
        kecamatan: 'Bakongan',
        kode_pos: validKodePos,
        kode_wilayah: '1101010',
        is_default: 1
      }]
    }).as('getAlamatListUpdated');

    cy.get('#saveAddress').click();
    cy.wait('@saveAlamatSuccess');

    // Check success notification
    cy.get('.alert-success').should('contain', 'Alamat berhasil disimpan');
  });

  it('Step 3 — Batas Melebihi Maks: HP = 21 char (Ditolak validasi API 422)', () => {
    const validNama = 'Riziq';
    const invalidHP = '08'.padEnd(21, '1');
    const validKodePos = '12345';

    // Click "Tambah Alamat"
    cy.get('#addAddressCard').click();

    // Fill inputs
    cy.get('#namaInput').type(validNama);
    cy.get('#provinsiSelect').select('11');
    cy.wait('@getKota');
    cy.get('#kotaSelect').select('1101');
    cy.wait('@getKecamatan');
    cy.get('#kecamatanSelect').select('1101010');
    
    cy.get('#kodePosInput').type(validKodePos);
    cy.get('#alamatInput').type('Jl. Raya Blok A No 1');
    
    // Type 21 chars in HP input.
    // Note: If input has maxlength, we invoke val to bypass and test the boundary validation
    cy.get('#nomorInput').invoke('val', invalidHP).trigger('input');

    // Mock API validation error
    cy.intercept('POST', '/alamat', {
      statusCode: 422,
      body: {
        success: false,
        message: 'Validasi gagal',
        errors: {
          nomor_penerima: ['The nomor penerima field must not be greater than 20 characters.']
        }
      }
    }).as('saveAlamatFailHP');

    cy.get('#saveAddress').click();
    cy.wait('@saveAlamatFailHP');

    // Check error notification
    cy.get('.alert-danger').should('contain', 'Gagal menyimpan alamat');
  });

  it('Step 4 — Batas Melebihi Maks: Nama = 256 char (Ditolak validasi API 422)', () => {
    const invalidNama = 'A'.repeat(256);
    const validHP = '081234567890';
    const validKodePos = '12345';

    // Click "Tambah Alamat"
    cy.get('#addAddressCard').click();

    // Fill inputs
    cy.get('#namaInput').invoke('val', invalidNama).trigger('input');
    cy.get('#provinsiSelect').select('11');
    cy.wait('@getKota');
    cy.get('#kotaSelect').select('1101');
    cy.wait('@getKecamatan');
    cy.get('#kecamatanSelect').select('1101010');
    
    cy.get('#kodePosInput').type(validKodePos);
    cy.get('#alamatInput').type('Jl. Raya Blok A No 1');
    cy.get('#nomorInput').type(validHP);

    // Mock API validation error
    cy.intercept('POST', '/alamat', {
      statusCode: 422,
      body: {
        success: false,
        message: 'Validasi gagal',
        errors: {
          nama_penerima: ['The nama penerima field must not be greater than 255 characters.']
        }
      }
    }).as('saveAlamatFailNama');

    cy.get('#saveAddress').click();
    cy.wait('@saveAlamatFailNama');

    // Check error notification
    cy.get('.alert-danger').should('contain', 'Gagal menyimpan alamat');
  });

  it('Step 5 — Batas Melebihi Maks: Kode Pos = 11 char (Ditolak validasi API 422)', () => {
    const validNama = 'Riziq';
    const validHP = '081234567890';
    const invalidKodePos = '1'.repeat(11);

    // Click "Tambah Alamat"
    cy.get('#addAddressCard').click();

    // Fill inputs
    cy.get('#namaInput').type(validNama);
    cy.get('#provinsiSelect').select('11');
    cy.wait('@getKota');
    cy.get('#kotaSelect').select('1101');
    cy.wait('@getKecamatan');
    cy.get('#kecamatanSelect').select('1101010');
    
    cy.get('#kodePosInput').invoke('val', invalidKodePos).trigger('input');
    cy.get('#alamatInput').type('Jl. Raya Blok A No 1');
    cy.get('#nomorInput').type(validHP);

    // Mock API validation error
    cy.intercept('POST', '/alamat', {
      statusCode: 422,
      body: {
        success: false,
        message: 'Validasi gagal',
        errors: {
          kode_pos: ['The kode pos field must not be greater than 10 characters.']
        }
      }
    }).as('saveAlamatFailKodePos');

    cy.get('#saveAddress').click();
    cy.wait('@saveAlamatFailKodePos');

    // Check error notification
    cy.get('.alert-danger').should('contain', 'Gagal menyimpan alamat');
  });
});

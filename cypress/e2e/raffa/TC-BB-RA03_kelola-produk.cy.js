/**
 * TC-BB-RA03 — Kelola Produk (Decision Table)
 * Feature: FR15 — Mengelola Produk
 * PIC: Raffa Rizky Febryan
 */

describe('TC-BB-RA03 — Kelola Produk (Decision Table)', () => {
  beforeEach(() => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.userA.email, users.userA.password)
    })
    cy.visit('/toko');
  })

  it('R1 — Semua input valid: Simpan data berhasil', () => {
    cy.on('window:alert', (str) => {
      expect(str).to.equal('Produk berhasil diupdate')
    })

    cy.get('#produk-3').find('button[name="btnEditProduk"]').click()
    cy.get('#modalEdit').within(() => {
      cy.get('#editNama').clear().type('IC PCF8574T')
      cy.get('#editKategori').select('Integrated Circuit (IC)')
      cy.get('#editHarga').clear().type('25000')
      cy.get('#editStok').clear().type('10')
      cy.get('#editBerat').clear().type('1500')
      cy.get('#editDeskripsi').clear().type('Remote 8-bit I/O expander for I2C-bus SOIC 16 Philps', { force: true })
      cy.get('#editGambar').selectFile('public/storage/produk/IC_MedcomIndonesia.jpeg')
      cy.get('button').contains(/Simpan/i).click()
    })
    cy.get('#produk-3').find('.product-title').should('contain', 'IC PCF8574T')
  })

  it('R2 — Nama kosong (C1 = N): Tampilkan error Nama', () => {
    cy.get('#produk-3').find('button[name="btnEditProduk"]').click()
    cy.get('#modalEdit').within(() => {
      cy.get('#editNama').clear()
      cy.get('button').contains(/Simpan/i).click()
    })
    cy.get('#modalEdit').find('[role="alert"]').should('be.visible')
  })

  it('R3 — Kategori tidak dipilih (C2 = N): Tampilkan error Kategori', () => {
    cy.get('#produk-3').find('button[name="btnEditProduk"]').click()
    cy.get('#modalEdit').within(() => {
      cy.get('#editKategori').select('')
      cy.get('button').contains(/Simpan/i).click()
    })
    cy.get('#modalEdit').find('[role="alert"]').should('be.visible')
  })

  it('R4 — Harga tidak valid (C3 = N): Tampilkan error Harga', () => {
    cy.get('#produk-3').find('button[name="btnEditProduk"]').click()
    cy.get('#modalEdit').within(() => {
      cy.get('#editHarga').clear().type('-10')
      cy.get('button').contains(/Simpan/i).click()
    })
    cy.get('#modalEdit').find('[role="alert"]').should('be.visible')
  })

  it('R5 — Stok tidak valid (C4 = N): Tampilkan error Stok', () => {
    cy.get('#produk-3').find('button[name="btnEditProduk"]').click()
    cy.get('#modalEdit').within(() => {
      cy.get('#editStok').clear().type('-1')
      cy.get('button').contains(/Simpan/i).click()
    })
    cy.get('#modalEdit').find('[role="alert"]').should('be.visible')
  })

  it('R6 — Berat tidak valid (C5 = N): Tampilkan error Berat', () => {
    cy.get('#produk-3').find('button[name="btnEditProduk"]').click()
    cy.get('#modalEdit').within(() => {
      cy.get('#editBerat').clear().type('0')
      cy.get('button').contains(/Simpan/i).click()
    })
    cy.get('#modalEdit').find('[role="alert"]').should('be.visible')
  })

  it('R7 — Deskripsi kosong (C6 = N): Tampilkan error Deskripsi', () => {
    cy.get('#produk-3').find('button[name="btnEditProduk"]').click()
    cy.get('#modalEdit').within(() => {
      cy.get('#editDeskripsi').clear()
      cy.get('button').contains(/Simpan/i).click()
    })
    cy.get('#modalEdit').find('[role="alert"]').should('be.visible')
  })

  it('R8 — Gambar format tidak valid (C7 = N): Tampilkan error Gambar', () => {
    cy.get('#produk-3').find('button[name="btnEditProduk"]').click()
    cy.get('#modalEdit').within(() => {
      cy.get('#editGambar').selectFile({
        contents: Cypress.Buffer.from('fake-file-data'),
        fileName: 'invalid_format.txt',
        mimeType: 'text/plain',
      })
      cy.get('button').contains(/Simpan/i).click()
    })
    cy.get('#modalEdit').find('[role="alert"]').should('be.visible')
  })
})

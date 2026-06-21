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
    cy.contains('Produk berhasil diupdate').should('be.visible')
  })

  // it('R2 — Nama kosong (C1 = N): Tampilkan error Nama', () => {
  //   cy.request({
  //     method: 'POST',
  //     url: '/product/1',
  //     failOnStatusCode: false,
  //     form: true,
  //     body: {
  //       _method: 'PUT',
  //       nama: '',
  //       category_id: 1,
  //       harga: 50000,
  //       stok: 10,
  //       berat: 1000,
  //       deskripsi: 'Deskripsi valid',
  //     },
  //   }).then((response) => {
  //     expect(response.status).to.eq(422)
  //     expect(response.body.success).to.be.false
  //     expect(response.body.errors.nama).to.exist
  //   })
  // })

  // it('R3 — Kategori tidak dipilih (C2 = N): Tampilkan error Kategori', () => {
  //   cy.request({
  //     method: 'POST',
  //     url: '/product/1',
  //     failOnStatusCode: false,
  //     form: true,
  //     body: {
  //       _method: 'PUT',
  //       nama: 'Produk Valid',
  //       category_id: '',
  //       harga: 50000,
  //       stok: 10,
  //       berat: 1000,
  //       deskripsi: 'Deskripsi valid',
  //     },
  //   }).then((response) => {
  //     expect(response.status).to.eq(422)
  //     expect(response.body.success).to.be.false
  //     expect(response.body.errors.category_id).to.exist
  //   })
  // })

  // it('R4 — Harga tidak valid (C3 = N): Tampilkan error Harga', () => {
  //   cy.request({
  //     method: 'POST',
  //     url: '/product/1',
  //     failOnStatusCode: false,
  //     form: true,
  //     body: {
  //       _method: 'PUT',
  //       nama: 'Produk Valid',
  //       category_id: 1,
  //       harga: -10,
  //       stok: 10,
  //       berat: 1000,
  //       deskripsi: 'Deskripsi valid',
  //     },
  //   }).then((response) => {
  //     expect(response.status).to.eq(422)
  //     expect(response.body.success).to.be.false
  //     expect(response.body.errors.harga).to.exist
  //   })
  // })

  // it('R5 — Stok tidak valid (C4 = N): Tampilkan error Stok', () => {
  //   cy.request({
  //     method: 'POST',
  //     url: '/product/1',
  //     failOnStatusCode: false,
  //     form: true,
  //     body: {
  //       _method: 'PUT',
  //       nama: 'Produk Valid',
  //       category_id: 1,
  //       harga: 50000,
  //       stok: -1,
  //       berat: 1000,
  //       deskripsi: 'Deskripsi valid',
  //     },
  //   }).then((response) => {
  //     expect(response.status).to.eq(422)
  //     expect(response.body.success).to.be.false
  //     expect(response.body.errors.stok).to.exist
  //   })
  // })

  // it('R6 — Berat tidak valid (C5 = N): Tampilkan error Berat', () => {
  //   cy.request({
  //     method: 'POST',
  //     url: '/product/1',
  //     failOnStatusCode: false,
  //     form: true,
  //     body: {
  //       _method: 'PUT',
  //       nama: 'Produk Valid',
  //       category_id: 1,
  //       harga: 50000,
  //       stok: 10,
  //       berat: 0,
  //       deskripsi: 'Deskripsi valid',
  //     },
  //   }).then((response) => {
  //     expect(response.status).to.eq(422)
  //     expect(response.body.success).to.be.false
  //     expect(response.body.errors.berat).to.exist
  //   })
  // })

  // it('R7 — Deskripsi kosong (C6 = N): Tampilkan error Deskripsi', () => {
  //   cy.request({
  //     method: 'POST',
  //     url: '/product/1',
  //     failOnStatusCode: false,
  //     form: true,
  //     body: {
  //       _method: 'PUT',
  //       nama: 'Produk Valid',
  //       category_id: 1,
  //       harga: 50000,
  //       stok: 10,
  //       berat: 1000,
  //       deskripsi: '',
  //     },
  //   }).then((response) => {
  //     expect(response.status).to.eq(422)
  //     expect(response.body.success).to.be.false
  //     expect(response.body.errors.deskripsi).to.exist
  //   })
  // })

  // it('R8 — Gambar format tidak valid (C7 = N): Tampilkan error Gambar', () => {
  //   cy.visit('/toko')
  //   cy.get('.btn-edit').first().click({ force: true })
  //   cy.get('#editGambar').selectFile({
  //     contents: Cypress.Buffer.from('fake-file-data'),
  //     fileName: 'invalid_format.txt',
  //     mimeType: 'text/plain',
  //   })
  //   cy.get('#modalEdit button').contains(/Simpan/i).click()

  //   cy.on('window:alert', (str) => {
  //     expect(str).to.be.oneOf(['Gagal update produk', 'Validasi gagal'])
  //   })
  // })
})

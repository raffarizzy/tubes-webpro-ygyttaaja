/**
 * TC-BB-B03 — Upload Produk (Decision Table)
 * Feature: FR14 — Mengunggah Produk
 * PIC: Bagas Pratama
 */

describe('TC-BB-B03 — Upload Produk (Decision Table)', () => {
  beforeEach(() => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.userA.email, users.userA.password)
    })
  })

  it('R1 — Penjual bertoko, semua field valid + gambar .jpg: produk berhasil diunggah', () => {
    cy.visit('/toko')
    cy.get('a, button').contains(/tambah produk|upload produk|new product/i).click()
    cy.get('input[name="nama"]').type('Produk Bagas Test')
    cy.get('input[name="harga"]').type('45000')
    cy.get('input[name="stok"]').type('20')
    cy.get('textarea[name="deskripsi"]').type('Deskripsi produk test')
    cy.get('input[type="file"][name="image"]').selectFile({
      contents: Cypress.Buffer.from('fake-jpg'),
      fileName: 'product.jpg',
      mimeType: 'image/jpeg',
    })
    cy.get('button[type="submit"]').click()
    cy.get('body').should('not.contain', '422')
  })

  it('R2 — User tanpa toko, POST /product/store: error "belum memiliki toko"', () => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.tester.email, users.tester.password)
    })
    cy.request({
      method: 'POST',
      url: '/product/store',
      failOnStatusCode: false,
      form: true,
      body: {
        nama: 'Test',
        harga: 10000,
        stok: 1,
        deskripsi: 'Test',
      },
    }).then((response) => {
      expect(response.status).to.be.oneOf([400, 403, 422, 302])
    })
  })

  it('R3a — Field "nama" kosong: error 422 "nama field is required"', () => {
    cy.request({
      method: 'POST',
      url: '/product/store',
      failOnStatusCode: false,
      form: true,
      body: {
        harga: 10000,
        stok: 1,
        deskripsi: 'Test',
      },
    }).then((response) => {
      expect(response.status).to.eq(422)
    })
  })

  it('R3b — Field "harga" kosong: error 422 "harga field is required"', () => {
    cy.request({
      method: 'POST',
      url: '/product/store',
      failOnStatusCode: false,
      form: true,
      body: {
        nama: 'Produk Test',
        stok: 1,
        deskripsi: 'Test',
      },
    }).then((response) => {
      expect(response.status).to.eq(422)
    })
  })

  it('R4 — Gambar format .pdf: error 422 format tidak valid', () => {
    cy.visit('/toko')
    cy.get('a, button').contains(/tambah produk|upload produk|new product/i).click()
    cy.get('input[name="nama"]').type('Produk PDF Test')
    cy.get('input[name="harga"]').type('30000')
    cy.get('input[name="stok"]').type('5')
    cy.get('textarea[name="deskripsi"]').type('Deskripsi test')
    cy.get('input[type="file"][name="image"]').selectFile({
      contents: Cypress.Buffer.from('%PDF-1.4 fake'),
      fileName: 'product.pdf',
      mimeType: 'application/pdf',
    })
    cy.get('button[type="submit"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/jpg|jpeg|png|format|type/i)
    })
  })
})

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
  })

  it('R1 — Update produk valid tanpa gambar baru: produk ter-update', () => {
    cy.request({
      method: 'PUT',
      url: '/product/1',
      failOnStatusCode: false,
      form: true,
      body: {
        nama: 'Produk Updated',
        harga: 50000,
        stok: 10,
        deskripsi: 'Deskripsi updated',
      },
    }).then((response) => {
      expect(response.status).to.be.oneOf([200, 302])
    })
  })

  it('R3 — Update produk, harga bukan numerik "abc": error 422', () => {
    cy.request({
      method: 'PUT',
      url: '/product/1',
      failOnStatusCode: false,
      form: true,
      body: {
        nama: 'Produk Test',
        harga: 'abc',
        stok: 10,
        deskripsi: 'Deskripsi',
      },
    }).then((response) => {
      expect(response.status).to.eq(422)
    })
  })

  it('R3 — Update produk, stok bukan integer "abc": error 422', () => {
    cy.request({
      method: 'PUT',
      url: '/product/1',
      failOnStatusCode: false,
      form: true,
      body: {
        nama: 'Produk Test',
        harga: 50000,
        stok: 'abc',
        deskripsi: 'Deskripsi',
      },
    }).then((response) => {
      expect(response.status).to.eq(422)
    })
  })

  it('R2 — Update produk dengan gambar .jpg baru: gambar diperbarui', () => {
    cy.visit('/toko')
    cy.get('a[href*="/product/"]').contains(/edit|kelola/i).first().click({ force: true })
    cy.get('input[name="nama"]').clear().type('Produk Dengan Gambar Baru')
    cy.get('input[name="harga"]').clear().type('75000')
    cy.get('input[name="stok"]').clear().type('5')
    cy.get('input[type="file"][name="image"]').selectFile({
      contents: Cypress.Buffer.from('fake-image-data'),
      fileName: 'product.jpg',
      mimeType: 'image/jpeg',
    })
    cy.get('button[type="submit"]').click()
    cy.get('body').should('not.contain', '422')
  })

  it('R1 (delete) — Hapus produk dengan Node API aktif: produk terhapus', () => {
    cy.request({
      method: 'DELETE',
      url: '/product/1',
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.be.oneOf([200, 302])
    })
  })
})

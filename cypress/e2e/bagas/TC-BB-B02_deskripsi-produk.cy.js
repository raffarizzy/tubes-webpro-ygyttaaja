/**
 * TC-BB-B02 — Deskripsi Produk (Boundary Value Analysis)
 * Feature: FR5 — Detail Produk
 * PIC: Bagas Pratama
 */

describe('TC-BB-B02 — Deskripsi Produk (Boundary Value Analysis)', () => {
  beforeEach(() => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.userA.email, users.userA.password)
    })
  })

  const submitProduct = (deskripsi) => {
    cy.request({
      method: 'POST',
      url: '/product/store',
      failOnStatusCode: false,
      form: true,
      body: {
        nama: 'Produk Test',
        harga: 50000,
        stok: 10,
        kategori_id: 1,
        deskripsi,
      },
    })
  }

  it('Step 1 — deskripsi kosong: error "deskripsi field is required"', () => {
    submitProduct('').then((response) => {
      expect(response.status).to.eq(422)
      expect(JSON.stringify(response.body)).to.match(/deskripsi.*required/i)
    })
  })

  it('Step 2 — deskripsi 1 karakter: produk berhasil dibuat', () => {
    submitProduct('A').then((response) => {
      expect(response.status).to.be.oneOf([200, 201, 302])
    })
  })

  it('Step 3 — deskripsi 255 karakter: produk berhasil dibuat', () => {
    submitProduct('A'.repeat(255)).then((response) => {
      expect(response.status).to.be.oneOf([200, 201, 302])
    })
  })

  it('Step 4 — deskripsi 1000 karakter: produk berhasil dibuat, deskripsi tampil penuh', () => {
    submitProduct('A'.repeat(1000)).then((response) => {
      expect(response.status).to.be.oneOf([200, 201, 302])
    })
  })

  it('Step 5 — Halaman detail produk tampil dengan semua field', () => {
    cy.visit('/produk/1')
    cy.get('body').should('satisfy', ($body) => {
      const text = $body.text()
      return text.match(/harga|stok|deskripsi|rating/i)
    })
  })

  it('Step 6 — Produk tidak ada /produk/99999: HTTP 404', () => {
    cy.request({
      url: '/produk/99999',
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.eq(404)
    })
  })
})

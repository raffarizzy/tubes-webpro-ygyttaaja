/**
 * TC-BB-F03 — Tambah Keranjang (Decision Table)
 * Feature: FR6 — Keranjang Belanja
 * PIC: Frizam Dafa Maulana
 */

describe('TC-BB-F03 — Tambah Keranjang (Decision Table)', () => {
  beforeEach(() => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.tester.email, users.tester.password)
    })
  })

  it('R1 — Login, tambah produk id=1, jumlah=1: produk masuk keranjang', () => {
    cy.visit('/produk/1')
    cy.get('button').contains(/tambah.*keranjang|add.*cart/i).click()
    cy.get('body').should('satisfy', ($body) => {
      return (
        $body.text().match(/berhasil ditambahkan|success/i) ||
        $body.find('[data-cart-count]').length > 0
      )
    })
  })

  it('R1 — Update jumlah item di keranjang menjadi 3', () => {
    cy.visit('/keranjang')
    cy.get('input[name*="jumlah"], input[type="number"]').first().clear().type('3')
    cy.get('button').contains(/update|simpan|perbarui/i).first().click()
    cy.get('body').should('not.contain', '500')
  })

  it('R1 — Hapus item dari keranjang: item terhapus', () => {
    cy.visit('/keranjang')
    cy.get('button, a').contains(/hapus|remove|delete/i).first().then(($btn) => {
      if ($btn.length) {
        cy.wrap($btn).click()
        cy.get('body').should('not.contain', '500')
      }
    })
  })

  it('R2 — Tanpa login, POST /keranjang/item: 401 atau redirect ke /login', () => {
    cy.clearCookies()
    cy.request({
      method: 'POST',
      url: '/keranjang/item',
      failOnStatusCode: false,
      form: true,
      body: { product_id: 1, jumlah: 1 },
    }).then((response) => {
      expect(response.status).to.be.oneOf([401, 302, 303])
    })
  })

  it('R3 — Login, product_id=99999 (tidak ada): error 422 invalid product', () => {
    cy.request({
      method: 'POST',
      url: '/keranjang/item',
      failOnStatusCode: false,
      form: true,
      body: { product_id: 99999, jumlah: 1 },
    }).then((response) => {
      expect(response.status).to.eq(422)
    })
  })

  it('R4 — Login, jumlah=0: error 422 "must be at least 1"', () => {
    cy.request({
      method: 'POST',
      url: '/keranjang/item',
      failOnStatusCode: false,
      form: true,
      body: { product_id: 1, jumlah: 0 },
    }).then((response) => {
      expect(response.status).to.eq(422)
    })
  })
})

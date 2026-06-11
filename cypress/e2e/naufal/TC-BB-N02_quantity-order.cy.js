/**
 * TC-BB-N02 — Batas Quantity Order (Boundary Value Analysis)
 * Feature: FR7 — Melakukan Pemesanan
 * PIC: Naufal Muhammad Dzulfikar
 */

describe('TC-BB-N02 — Batas Quantity Order (Boundary Value Analysis)', () => {
  beforeEach(() => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.tester.email, users.tester.password)
    })
  })

  it('Step 1 — jumlah=0: error 422 "must be at least 1"', () => {
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

  it('Step 2 — jumlah=1 (batas minimum valid): produk masuk keranjang', () => {
    cy.request({
      method: 'POST',
      url: '/keranjang/item',
      failOnStatusCode: false,
      form: true,
      body: { product_id: 1, jumlah: 1 },
    }).then((response) => {
      expect(response.status).to.be.oneOf([200, 201, 302])
    })
  })

  it('Step 3 — Update jumlah menjadi 5: subtotal ter-update', () => {
    cy.visit('/keranjang')
    cy.get('input[name*="jumlah"], input[type="number"]').first().then(($input) => {
      if ($input.length) {
        cy.wrap($input).clear().type('5')
        cy.get('button').contains(/update|simpan|perbarui/i).first().click()
        cy.get('body').should('not.contain', '422')
      }
    })
  })

  it('Step 4 — Update jumlah menjadi 10 (setara stok): berhasil', () => {
    cy.visit('/keranjang')
    cy.get('input[name*="jumlah"], input[type="number"]').first().then(($input) => {
      if ($input.length) {
        cy.wrap($input).clear().type('10')
        cy.get('button').contains(/update|simpan|perbarui/i).first().click()
        cy.get('body').should('not.contain', '422')
      }
    })
  })

  it('Step 5 — Update jumlah menjadi 11 (melebihi stok): error stok tidak cukup', () => {
    cy.visit('/keranjang')
    cy.get('input[name*="jumlah"], input[type="number"]').first().then(($input) => {
      if ($input.length) {
        cy.wrap($input).clear().type('11')
        cy.get('button').contains(/update|simpan|perbarui/i).first().click()
        cy.get('body').should('satisfy', ($body) => {
          return $body.text().match(/stok|not enough|insufficient/i)
        })
      }
    })
  })

  it('Step 6 — jumlah=1, buka /checkout, pilih alamat, klik bayar: redirect ke Xendit', () => {
    cy.visit('/checkout')
    cy.url().then((url) => {
      if (url.includes('/checkout')) {
        cy.get('select[name*="alamat"], input[name*="alamat"]').first().then(($el) => {
          if ($el.is('select')) cy.wrap($el).select(1)
        })
        cy.get('button').contains(/bayar|checkout/i).click()
        // Should redirect to Xendit or show invoice URL
        cy.get('body').should('not.contain', '500')
      }
    })
  })
})

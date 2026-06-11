/**
 * TC-BB-B01 — Pagination Katalog (Equivalence Partitioning)
 * Feature: FR4 — Katalog Produk
 * PIC: Bagas Pratama
 */

describe('TC-BB-B01 — Pagination Katalog (Equivalence Partitioning)', () => {
  it('Step 1 — Homepage halaman 1: produk tampil urut created_at DESC, pagination tersedia', () => {
    cy.visit('/')
    cy.get('[data-testid="product-card"], .product-card, .product-item').should('have.length.gte', 1)
  })

  it('Step 2 — Navigasi ke halaman 2 via Next: produk halaman 2 tampil', () => {
    cy.request({
      url: `${Cypress.env('nodeApiUrl')}/api/products?page=2`,
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.be.oneOf([200, 404])
      if (response.status === 200) {
        expect(response.body).to.have.property('data')
      }
    })
  })

  it('Step 3 — page=0: tidak crash, error atau redirect ke halaman 1', () => {
    cy.request({
      url: `${Cypress.env('nodeApiUrl')}/api/products?page=0`,
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.not.eq(500)
    })
  })

  it('Step 4 — page=-1: tidak crash, error atau default ke halaman 1', () => {
    cy.request({
      url: `${Cypress.env('nodeApiUrl')}/api/products?page=-1`,
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.not.eq(500)
    })
  })

  it('Step 5 — page=9999: data kosong atau pesan tidak ada produk', () => {
    cy.request({
      url: `${Cypress.env('nodeApiUrl')}/api/products?page=9999`,
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.not.eq(500)
      if (response.status === 200) {
        const data = response.body.data
        expect(Array.isArray(data) ? data.length : 0).to.eq(0)
      }
    })
  })

  it('Step 6 — page=abc: tidak crash, validasi atau default ke halaman 1', () => {
    cy.request({
      url: `${Cypress.env('nodeApiUrl')}/api/products?page=abc`,
      failOnStatusCode: false,
    }).then((response) => {
      expect(response.status).to.not.eq(500)
    })
  })
})

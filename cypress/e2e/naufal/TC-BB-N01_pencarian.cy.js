/**
 * TC-BB-N01 — Pencarian Keyword (Equivalence Partitioning)
 * Feature: FR3 — Pencarian Produk
 * PIC: Naufal Muhammad Dzulfikar
 */

const products = [
  {
    id: 1,
    nama: 'Oli Mesin 10W-40',
    deskripsi: 'Oli mesin untuk motor harian',
    harga: 75000,
    diskon: 0,
    stok: 20,
    imagePath: 'img/iconOli.png',
    toko_id: 1,
    nama_toko: 'Medcom Official',
    category_id: 1,
    category_nama: 'Oli',
  },
  {
    id: 2,
    nama: 'Filter Udara Racing',
    deskripsi: 'Filter udara performa tinggi',
    harga: 55000,
    diskon: 0,
    stok: 10,
    imagePath: 'img/iconOli.png',
    toko_id: 1,
    nama_toko: 'Medcom Official',
    category_id: 2,
    category_nama: 'Filter',
  },
]

describe('TC-BB-N01 pencarian produk', () => {
  beforeEach(() => {
    cy.on('window:alert', (message) => {
      throw new Error(`Unexpected alert: ${message}`)
    })

    cy.intercept('GET', 'http://localhost:3001/api/products', {
      statusCode: 200,
      body: { success: true, data: products },
    }).as('getProducts')

    cy.intercept('GET', '/keranjang/data', {
      statusCode: 200,
      body: { success: true, data: { total_items: 0 } },
    })

    cy.visit('/')
    cy.wait('@getProducts')
  })

  it('menampilkan produk saat mencari keyword "Oli"', () => {
    cy.get('#search-input').type('Oli')
    cy.get('#produk-container .card-produk').contains('Oli Mesin 10W-40').should('be.visible')
  })

  it('menampilkan produk saat mencari keyword lengkap', () => {
    cy.get('#search-input').type('oli mesin')
    cy.get('#produk-container .card-produk').contains('Oli Mesin 10W-40').should('be.visible')
  })

  it('menampilkan semua produk saat keyword kosong', () => {
    cy.get('#search-input').clear()
    cy.get('#produk-container .card-produk').should('have.length', 2)
  })

  it('menampilkan pesan tidak ditemukan untuk keyword yang tidak cocok', () => {
    cy.get('#search-input').type('zzz999xabc')
    cy.get('#produk-container').should('contain', 'Tidak ada produk ditemukan')
  })

  it('tidak menjalankan script dari input pencarian', () => {
    cy.get('#search-input').type('<script>alert(1)</script>')
    cy.get('#produk-container').should('contain', 'Tidak ada produk ditemukan')
  })
})

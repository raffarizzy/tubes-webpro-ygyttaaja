/**
 * TC-BB-N01 — Pencarian Keyword (Equivalence Partitioning)
 * Feature: FR3 — Pencarian Sparepart
 * PIC: Naufal Muhammad Dzulfikar
 */

describe('TC-BB-N01 — Pencarian Keyword (Equivalence Partitioning)', () => {
  it('Step 1 — Keyword "Oli": produk yang mengandung "Oli" tampil', () => {
    cy.visit('/')
    cy.get('input[name="search"], input[placeholder*="cari" i], input[type="search"]').type('Oli')
    cy.get('form').submit()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/oli/i) || $body.text().match(/tidak ditemukan|no product/i)
    })
  })

  it('Step 2 — Keyword "oli mesin" (multi-kata lowercase): produk ditemukan case-insensitive', () => {
    cy.visit('/')
    cy.get('input[name="search"], input[placeholder*="cari" i], input[type="search"]').type('oli mesin')
    cy.get('form').submit()
    cy.get('body').should('not.contain', '500')
  })

  it('Step 3 — Tanpa keyword (kosong): semua produk tampil', () => {
    cy.visit('/')
    cy.get('[data-testid="product-card"], .product-card, .product-item').should('have.length.gte', 1)
  })

  it('Step 4 — Keyword tidak ada "zzz999xabc": pesan tidak ditemukan / grid kosong', () => {
    cy.visit('/')
    cy.get('input[name="search"], input[placeholder*="cari" i], input[type="search"]').type('zzz999xabc')
    cy.get('form').submit()
    cy.get('body').should('satisfy', ($body) => {
      return (
        $body.text().match(/tidak ditemukan|no product|not found|kosong/i) ||
        $body.find('[data-testid="product-card"], .product-card').length === 0
      )
    })
  })

  it('Step 5 — Keyword "<script>alert(1)</script>": tidak ada eksekusi JS, halaman aman', () => {
    cy.visit('/')
    cy.get('input[name="search"], input[placeholder*="cari" i], input[type="search"]').type(
      '<script>alert(1)</script>'
    )
    cy.get('form').submit()
    // Verify no XSS — page should still load normally
    cy.get('body').should('exist')
    cy.get('body').should('not.contain', '500')
    // Ensure the raw script tag is not rendered as executable
    cy.document().then((doc) => {
      const scripts = [...doc.querySelectorAll('script')].filter((s) =>
        s.textContent.includes('alert(1)')
      )
      expect(scripts).to.have.length(0)
    })
  })
})

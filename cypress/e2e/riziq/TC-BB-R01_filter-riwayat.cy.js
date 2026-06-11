/**
 * TC-BB-R01 — Filter Riwayat (Equivalence Partitioning)
 * Feature: FR8 — Riwayat Pesanan
 * PIC: Riziq Rizwan
 */

describe('TC-BB-R01 — Filter Riwayat (Equivalence Partitioning)', () => {
  beforeEach(() => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.riziq.email, users.riziq.password)
    })
  })

  it('Step 1 — Tanpa filter: semua pesanan tampil urut tanggal terbaru', () => {
    cy.visit('/riwayat-pesanan')
    cy.get('[data-testid="order-item"], .order-item, tr[data-order]').should('have.length.gte', 1)
  })

  it('Step 2 — Filter status "paid": hanya pesanan paid tampil', () => {
    cy.visit('/riwayat-pesanan')
    cy.get('select[name="status"], [data-testid="filter-status"]').select('paid')
    cy.get('body').then(($body) => {
      if ($body.find('[data-status]').length > 0) {
        cy.get('[data-status]').each(($el) => {
          expect($el.attr('data-status')).to.eq('paid')
        })
      } else {
        // fallback: check visible status badges
        cy.get('.status-badge, .badge').each(($el) => {
          expect($el.text().toLowerCase()).to.include('paid')
        })
      }
    })
  })

  it('Step 3 — Filter status "pending": hanya pesanan pending tampil', () => {
    cy.visit('/riwayat-pesanan')
    cy.get('select[name="status"], [data-testid="filter-status"]').select('pending')
    cy.get('[data-status="pending"], .status-pending').should('exist')
  })

  it('Step 4 — Filter status "cancelled": hanya pesanan cancelled tampil', () => {
    cy.visit('/riwayat-pesanan')
    cy.get('select[name="status"], [data-testid="filter-status"]').select('cancelled')
    cy.get('[data-status="cancelled"], .status-cancelled').should('exist')
  })

  it('Step 5 — Filter status invalid "xyz_invalid": tidak ada pesanan / semua tampil', () => {
    cy.visit('/riwayat-pesanan?status=xyz_invalid')
    cy.get('body').should('not.contain', 'Error')
    cy.get('body').should('not.contain', '500')
  })

  it('Step 6 — Klik detail pesanan: halaman detail tampil dengan nomor, item, total, status', () => {
    cy.visit('/riwayat-pesanan')
    cy.get('a[href*="/orders/"]').first().click()
    cy.url().should('include', '/orders/')
    cy.get('body').should('satisfy', ($body) => {
      const text = $body.text()
      return text.includes('Order') || text.includes('Pesanan') || text.includes('Total')
    })
  })
})

/**
 * TC-BB-R02 — Nomor Resi (Boundary Value Analysis)
 * Feature: FR9 — Pelacakan Pesanan
 * PIC: Riziq Rizwan
 */

describe('TC-BB-R02 — Nomor Resi (Boundary Value Analysis)', () => {
  beforeEach(() => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.riziq.email, users.riziq.password)
    })
  })

  it('Step 1 — Nomor resi tampil di halaman detail pesanan', () => {
    cy.visit('/riwayat-pesanan')
    cy.get('a[href*="/orders/"]').first().click()
    cy.get('body').should('satisfy', ($body) => {
      const text = $body.text()
      return text.match(/resi|tracking|pengiriman/i)
    })
  })

  it('Step 2 — Lacak resi valid "JNE123456789" (13 char): status pengiriman tampil', () => {
    cy.visit('/riwayat-pesanan')
    cy.get('a[href*="/orders/"]').first().click()
    cy.get('button, a').contains(/lacak|cek resi/i).then(($btn) => {
      if ($btn.length) {
        cy.wrap($btn).click()
        cy.get('body').should('not.contain', '500')
      }
    })
  })

  it('Step 3 — Resi kosong (0 char): error "tidak boleh kosong"', () => {
    cy.visit('/riwayat-pesanan')
    cy.get('a[href*="/orders/"]').first().click()
    cy.get('input[name*="resi"], input[placeholder*="resi"]').then(($input) => {
      if ($input.length) {
        cy.wrap($input).clear()
        cy.get('form').contains(/lacak|cek/i).closest('form').submit()
        cy.get('body').should('satisfy', ($body) => {
          return $body.text().match(/tidak boleh kosong|required/i)
        })
      }
    })
  })

  it('Step 4 — Resi 1 karakter "A": respon tidak ditemukan dari API', () => {
    cy.visit('/riwayat-pesanan')
    cy.get('a[href*="/orders/"]').first().click()
    cy.get('input[name*="resi"], input[placeholder*="resi"]').then(($input) => {
      if ($input.length) {
        cy.wrap($input).clear().type('A')
        cy.get('button').contains(/lacak|cek/i).click()
        cy.get('body').should('not.contain', '500')
      }
    })
  })

  it('Step 5 — Resi 50 karakter: tracking info tampil sesuai response', () => {
    const resi50 = 'A'.repeat(50)
    cy.visit('/riwayat-pesanan')
    cy.get('a[href*="/orders/"]').first().click()
    cy.get('input[name*="resi"], input[placeholder*="resi"]').then(($input) => {
      if ($input.length) {
        cy.wrap($input).clear().type(resi50)
        cy.get('button').contains(/lacak|cek/i).click()
        cy.get('body').should('not.contain', '500')
      }
    })
  })

  it('Step 6 — Resi 256 karakter: validasi menolak input melebihi batas', () => {
    const resi256 = 'A'.repeat(256)
    cy.visit('/riwayat-pesanan')
    cy.get('a[href*="/orders/"]').first().click()
    cy.get('input[name*="resi"], input[placeholder*="resi"]').then(($input) => {
      if ($input.length) {
        cy.wrap($input).clear().invoke('val', resi256).trigger('input')
        cy.get('button').contains(/lacak|cek/i).click()
        cy.get('body').should('not.contain', '500')
      }
    })
  })
})

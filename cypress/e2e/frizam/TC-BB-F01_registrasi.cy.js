/**
 * TC-BB-F01 — Registrasi Akun (Equivalence Partitioning)
 * Feature: FR1 — Registrasi Akun
 * PIC: Frizam Dafa Maulana
 */

describe('TC-BB-F01 — Registrasi Akun (Equivalence Partitioning)', () => {
  it('Step 1 — Halaman register dapat diakses', () => {
    cy.visit('/register')
    cy.get('input[name="name"]').should('exist')
    cy.get('input[name="email"]').should('exist')
    cy.get('input[name="password"]').should('exist')
  })

  it('Step 2 — Semua field valid: register sukses, redirect ke /', () => {
    cy.visit('/register')
    cy.get('input[name="name"]').type('BudiTest')
    cy.get('input[name="email"]').type(`budi.test.${Date.now()}@gmail.com`)
    cy.get('input[name="phone"]').type('08123456789')
    cy.get('input[name="password"]').type('Password123!')
    cy.get('input[name="password_confirmation"]').type('Password123!')
    cy.get('button[name="daftarBtn"]').click()
    cy.url().should('eq', Cypress.config().baseUrl + '/')
  })

  it('Step 3 — Email kosong: validasi browser muncul', () => {
    cy.visit('/register')
    cy.get('input[name="name"]').type('BudiTest')
    cy.get('input[name="phone"]').type('08123456789')
    cy.get('input[name="password"]').type('Password123!')
    cy.get('input[name="password_confirmation"]').type('Password123!')
    cy.get('button[name="daftarBtn"]').click()
    
    // Mengecek validasi HTML5 "required" pada input email
    cy.get('input[name="email"]').then(($input) => {
      expect($input[0].validationMessage).to.not.be.empty
    })
  })

  it('Step 4 — Email duplikat: error "The email has already been taken"', () => {
    cy.visit('/register')
    cy.get('input[name="name"]').type('BudiTest')
    cy.get('input[name="email"]').type('existing@sparehub.com')
    cy.get('input[name="phone"]').type('08123456789')
    cy.get('input[name="password"]').type('Password123!')
    cy.get('input[name="password_confirmation"]').type('Password123!')
    cy.get('button[name="daftarBtn"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/already been taken|sudah terdaftar/i)
    })
  })

  it('Step 5 — Password tidak cocok: error "confirmation does not match"', () => {
    cy.visit('/register')
    cy.get('input[name="name"]').type('BudiTest')
    cy.get('input[name="email"]').type('budi2@gmail.com')
    cy.get('input[name="phone"]').type('08123456789')
    cy.get('input[name="password"]').type('Password123!')
    cy.get('input[name="password_confirmation"]').type('Password456!')
    cy.get('button[name="daftarBtn"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/confirmation does not match|tidak cocok/i)
    })
  })

  it('Step 6 — Nama kosong: validasi browser muncul', () => {
    cy.visit('/register')
    cy.get('input[name="email"]').type('budi3@gmail.com')
    cy.get('input[name="phone"]').type('08123456789')
    cy.get('input[name="password"]').type('Password123!')
    cy.get('input[name="password_confirmation"]').type('Password123!')
    cy.get('button[name="daftarBtn"]').click()
    
    // Mengecek validasi HTML5 "required" pada input nama
    cy.get('input[name="name"]').then(($input) => {
      expect($input[0].validationMessage).to.not.be.empty
    })
  })
})

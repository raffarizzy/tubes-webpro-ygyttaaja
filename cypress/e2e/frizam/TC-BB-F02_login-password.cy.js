/**
 * TC-BB-F02 — Login Password (Boundary Value Analysis)
 * Feature: FR2 — Login Sistem
 * PIC: Frizam Dafa Maulana
 */

describe('TC-BB-F02 — Login Password (Boundary Value Analysis)', () => {
  it('Step 1 — Halaman login dapat diakses', () => {
    cy.visit('/login')
    cy.get('input[name="email"]').should('exist')
    cy.get('input[name="password"]').should('exist')
  })

  it('Step 2 — Password 7 karakter "Pass123": login gagal', () => {
    cy.visit('/login')
    cy.get('input[name="email"]').type('tester@sparehub.com')
    cy.get('input[name="password"]').type('Pass123')
    cy.get('button[name="loginBtn"]').click()
    cy.url().should('include', '/login')
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/credentials|not match|password/i)
    })
  })

  it('Step 3 — Password 8 karakter "Pass123!" (batas minimum akun terdaftar): login berhasil', () => {
    cy.visit('/login')
    cy.get('input[name="email"]').type('tester@sparehub.com')
    cy.get('input[name="password"]').type('Pass123!')
    cy.get('button[name="loginBtn"]').click()
    cy.url().should('eq', Cypress.config().baseUrl + '/')
  })

  it('Step 4 — Password 12 karakter "Password123!": login berhasil', () => {
    cy.visit('/login')
    cy.get('input[name="email"]').type('tester@sparehub.com')
    cy.get('input[name="password"]').type('Password123!')
    cy.get('button[name="loginBtn"]').click()
    cy.url().should('eq', Cypress.config().baseUrl + '/')
  })

  it('Step 5 — Password kosong, email valid: error "password is required"', () => {
    cy.visit('/login')
    cy.get('input[name="email"]').type('tester@sparehub.com')
    cy.get('button[name="loginBtn"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/password.*required|password.*wajib/i)
    })
  })

  it('Step 6 — Email kosong, password valid: error "email is required"', () => {
    cy.visit('/login')
    cy.get('input[name="password"]').type('Password123!')
    cy.get('button[name="loginBtn"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/email.*required|email.*wajib/i)
    })
  })

  it('Step 7 — Login berhasil lalu logout: session dihapus, redirect ke /', () => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.tester.email, users.tester.password)
    })
    cy.visit('/')
    cy.get('button, a').contains(/logout|keluar/i).click()
    cy.url().should('eq', Cypress.config().baseUrl + '/')
    cy.get('a[href="/login"]').should('exist')
  })
})
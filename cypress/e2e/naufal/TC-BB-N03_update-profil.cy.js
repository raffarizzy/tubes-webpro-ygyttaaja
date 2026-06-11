/**
 * TC-BB-N03 — Update Profil (Decision Table)
 * Feature: FR11 — Edit Profil
 * PIC: Naufal Muhammad Dzulfikar
 */

describe('TC-BB-N03 — Update Profil (Decision Table)', () => {
  beforeEach(() => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.tester.email, users.tester.password)
    })
  })

  it('R1 — Nama valid, email sama, tanpa ganti password: profil berhasil diperbarui', () => {
    cy.visit('/edit_profil')
    cy.get('input[name="name"]').clear().type('Naufal Updated')
    cy.get('button[type="submit"]').first().click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/berhasil diperbarui|success/i)
    })
  })

  it('R2a — Email sudah dipakai akun lain: error "Email sudah digunakan"', () => {
    cy.visit('/edit_profil')
    cy.get('input[name="email"]').clear().type('existing@sparehub.com')
    cy.get('button[type="submit"]').first().click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/sudah digunakan|already been taken/i)
    })
  })

  it('R2b — Nama kosong: error "Username wajib diisi"', () => {
    cy.visit('/edit_profil')
    cy.get('input[name="name"]').clear()
    cy.get('button[type="submit"]').first().click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/wajib diisi|required/i)
    })
  })

  it('R3 — current_password benar, password baru valid: password berhasil diperbarui', () => {
    cy.visit('/edit_profil')
    cy.get('input[name="name"]').clear().type('Naufal Tester')
    cy.get('input[name="current_password"]').type('Password123!')
    cy.get('input[name="password"]').type('NewPass456!')
    cy.get('input[name="password_confirmation"]').type('NewPass456!')
    cy.get('button[type="submit"]').first().click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/berhasil diperbarui|success/i)
    })
  })

  it('R4 — current_password salah: error "Password saat ini salah"', () => {
    cy.visit('/edit_profil')
    cy.get('input[name="current_password"]').type('WrongPass!')
    cy.get('input[name="password"]').type('NewPass456!')
    cy.get('input[name="password_confirmation"]').type('NewPass456!')
    cy.get('button[type="submit"]').first().click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/password.*salah|current.*password/i)
    })
  })

  it('Step 6 — Upload foto profil .jpg valid: avatar baru tampil', () => {
    cy.visit('/edit_profil')
    cy.get('input[type="file"][name*="pfp"], input[type="file"][name*="photo"]').selectFile({
      contents: Cypress.Buffer.from('fake-jpg-data'),
      fileName: 'avatar.jpg',
      mimeType: 'image/jpeg',
    })
    cy.get('form').contains(/upload|simpan foto/i).closest('form').submit()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/berhasil diperbarui|success/i)
    })
  })

  it('Step 7 — Upload foto profil .pdf: error format gambar', () => {
    cy.visit('/edit_profil')
    cy.get('input[type="file"][name*="pfp"], input[type="file"][name*="photo"]').selectFile({
      contents: Cypress.Buffer.from('%PDF-1.4 fake'),
      fileName: 'document.pdf',
      mimeType: 'application/pdf',
    })
    cy.get('form').contains(/upload|simpan foto/i).closest('form').submit()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/JPG|JPEG|PNG|format/i)
    })
  })
})

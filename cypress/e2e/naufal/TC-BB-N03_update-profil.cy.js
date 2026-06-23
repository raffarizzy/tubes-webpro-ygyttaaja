/**
 * TC-BB-N03 — Update Profil (Decision Table)
 * Feature: FR11 — Edit Profil
 *
 * Prasyarat: `php artisan db:seed --class=CypressSeeder`
 *   - tester@medcom.com / Password123!   (akun yang diedit)
 *   - existing@medcom.com                 (untuk uji email sudah dipakai)
 * Server Laravel (8000) & Node API (3001) hidup.
 *
 * Catatan: tombol submit form profil tidak punya atribut type, dan ada 2 form
 * (foto profil + data profil), jadi submit ditarget lewat #profileForm.
 * Input file foto profil tersembunyi (d-none) & auto-submit on change.
 */

describe('TC-BB-N03 — Update Profil (Decision Table)', () => {
  let users

  beforeEach(() => {
    cy.fixture('users').then((u) => {
      users = u
      cy.loginFast(u.tester.email, u.tester.password)
    })
  })

  // submit form langsung (tombol "Simpan Perubahan" tidak punya type=submit &
  // bisa di luar viewport), lebih andal daripada .click()
  const submitProfil = () => cy.get('#profileForm').submit()

  it('R1 — Nama valid, email sama, tanpa ganti password: profil berhasil diperbarui', () => {
    cy.visit('/edit_profil')
    cy.get('#profileForm input[name="name"]').clear().type('Naufal Updated')
    submitProfil()
    cy.get('.alert-success').should('contain', 'berhasil diperbarui')
  })

  it('R2a — Email sudah dipakai akun lain: error "Email sudah digunakan"', () => {
    cy.visit('/edit_profil')
    cy.get('#profileForm input[name="email"]').clear().type(users.existing.email)
    submitProfil()
    cy.get('.alert-danger').should('contain', 'sudah digunakan')
  })

  it('R2b — Nama kosong: error "Username wajib diisi"', () => {
    cy.visit('/edit_profil')
    cy.get('#profileForm input[name="name"]').clear()
    submitProfil()
    cy.get('.alert-danger').should('contain', 'wajib diisi')
  })

  it('R3 — current_password benar, password baru valid: profil berhasil diperbarui', () => {
    cy.visit('/edit_profil')
    cy.get('#profileForm input[name="name"]').clear().type('Naufal Tester')
    cy.get('#profileForm input[name="current_password"]').type('Password123!')
    cy.get('#profileForm input[name="password"]').type('NewPass456!')
    cy.get('#profileForm input[name="password_confirmation"]').type('NewPass456!')
    submitProfil()
    cy.get('.alert-success').should('contain', 'berhasil diperbarui')

    // Kembalikan password ke kondisi awal (Password123!) supaya akun tetap
    // kanonik — mencegah login di test/spec lain (mis. N02) gagal.
    cy.visit('/edit_profil')
    cy.get('#profileForm input[name="current_password"]').type('NewPass456!')
    cy.get('#profileForm input[name="password"]').type('Password123!')
    cy.get('#profileForm input[name="password_confirmation"]').type('Password123!')
    submitProfil()
    cy.get('.alert-success').should('contain', 'berhasil diperbarui')
  })

  it('R4 — current_password salah: error "Password saat ini salah"', () => {
    cy.visit('/edit_profil')
    cy.get('#profileForm input[name="current_password"]').type('WrongPass!')
    cy.get('#profileForm input[name="password"]').type('NewPass456!')
    cy.get('#profileForm input[name="password_confirmation"]').type('NewPass456!')
    submitProfil()
    cy.get('.alert-danger').should('contain', 'Password saat ini salah')
  })

  it('Step 6 — Upload foto profil gambar valid (.png): foto profil berhasil diperbarui', () => {
    cy.visit('/edit_profil')
    // input file tersembunyi + auto-submit on change
    cy.get('input[name="pfpPath"]').selectFile('cypress/fixtures/logo.png', { force: true })
    cy.get('.alert-success').should('contain', 'berhasil diperbarui')
  })

  it('Step 7 — Upload file bukan gambar (.pdf): ditolak dengan error format', () => {
    cy.visit('/edit_profil')
    cy.get('input[name="pfpPath"]').selectFile(
      {
        contents: Cypress.Buffer.from('%PDF-1.4 fake pdf content'),
        fileName: 'document.pdf',
        mimeType: 'application/pdf',
      },
      { force: true }
    )
    cy.get('.alert-danger').should('satisfy', ($el) => /gambar|format|jpg|jpeg|png|webp/i.test($el.text()))
  })
})

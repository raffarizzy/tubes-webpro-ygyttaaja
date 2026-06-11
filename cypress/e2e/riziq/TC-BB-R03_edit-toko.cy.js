/**
 * TC-BB-R03 — Edit Toko (Decision Table)
 * Feature: FR13 — Mengelola Toko
 * PIC: Riziq Rizwan
 */

describe('TC-BB-R03 — Edit Toko (Decision Table)', () => {
  beforeEach(() => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.userA.email, users.userA.password)
    })
  })

  it('R1 — Pemilik toko, field valid tanpa logo baru: toko berhasil diperbarui', () => {
    cy.visit('/toko')
    cy.get('input[name="nama_toko"]').clear().type('Toko Riziq Updated')
    cy.get('textarea[name="deskripsi"], input[name="deskripsi"]').clear().type('Deskripsi baru')
    cy.get('input[name="lokasi"]').clear().type('Jakarta')
    cy.get('button[type="submit"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/berhasil diperbarui|success/i)
    })
  })

  it('R2 — Bukan pemilik toko, kirim PUT /toko/1: ditolak 403', () => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.userB.email, users.userB.password)
    })
    cy.request({
      method: 'PUT',
      url: '/toko/1',
      failOnStatusCode: false,
      body: { nama_toko: 'Hack', lokasi: 'Jakarta' },
    }).then((response) => {
      expect(response.status).to.be.oneOf([403, 302])
    })
  })

  it('R3a — Pemilik toko, nama_toko kosong: error validasi', () => {
    cy.visit('/toko')
    cy.get('input[name="nama_toko"]').clear()
    cy.get('button[type="submit"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/nama toko.*required|wajib diisi/i)
    })
  })

  it('R3b — Pemilik toko, lokasi kosong: error validasi', () => {
    cy.visit('/toko')
    cy.get('input[name="nama_toko"]').clear().type('Toko Valid')
    cy.get('input[name="lokasi"]').clear()
    cy.get('button[type="submit"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/lokasi.*required|wajib diisi/i)
    })
  })

  it('R4 — Pemilik toko, semua field valid + logo .jpg baru: toko & logo diperbarui', () => {
    cy.visit('/toko')
    cy.get('input[name="nama_toko"]').clear().type('Toko Riziq Logo Baru')
    cy.get('input[name="lokasi"]').clear().type('Bandung')
    cy.get('input[type="file"][name="logo"]').selectFile({
      contents: Cypress.Buffer.from('fake-image-content'),
      fileName: 'logo.jpg',
      mimeType: 'image/jpeg',
    })
    cy.get('button[type="submit"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/berhasil diperbarui|success/i)
    })
  })

  it('R4 invalid — Logo format .pdf: error validasi format', () => {
    cy.visit('/toko')
    cy.get('input[name="nama_toko"]').clear().type('Toko Valid')
    cy.get('input[name="lokasi"]').clear().type('Jakarta')
    cy.get('input[type="file"][name="logo"]').selectFile({
      contents: Cypress.Buffer.from('%PDF-1.4 fake'),
      fileName: 'logo.pdf',
      mimeType: 'application/pdf',
    })
    cy.get('button[type="submit"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/jpeg|png|jpg|gif|format/i)
    })
  })
})

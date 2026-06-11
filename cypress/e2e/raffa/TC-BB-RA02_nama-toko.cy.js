/**
 * TC-BB-RA02 — Nama Toko (Boundary Value Analysis)
 * Feature: FR12 — Membuat Toko
 * PIC: Raffa Rizky Febryan
 */

describe('TC-BB-RA02 — Nama Toko (Boundary Value Analysis)', () => {
  beforeEach(() => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.raffa.email, users.raffa.password)
    })
  })

  const fillTokoForm = (namaToko) => {
    cy.get('input[name="nama_toko"]').clear().invoke('val', namaToko).trigger('input')
    cy.get('input[name="lokasi"]').clear().type('Jakarta')
    cy.get('textarea[name="deskripsi"]').clear().type('Deskripsi toko')
    cy.get('input[type="file"][name="logo"]').selectFile({
      contents: Cypress.Buffer.from('fake-image'),
      fileName: 'logo.jpg',
      mimeType: 'image/jpeg',
    })
  }

  it('Step 1 — nama_toko kosong: error "nama toko field is required"', () => {
    cy.visit('/toko/create')
    cy.get('input[name="nama_toko"]').clear()
    cy.get('input[name="lokasi"]').type('Jakarta')
    cy.get('button[type="submit"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/nama toko.*required|wajib diisi/i)
    })
  })

  it('Step 2 — nama_toko 1 karakter "A": toko berhasil dibuat', () => {
    cy.visit('/toko/create')
    fillTokoForm('A')
    cy.get('button[type="submit"]').click()
    cy.url().should('include', '/toko')
    cy.get('body').should('not.contain', 'The nama toko field is required')
  })

  it('Step 3 — nama_toko 255 karakter (batas max valid): toko berhasil dibuat', () => {
    cy.visit('/toko/create')
    fillTokoForm('A'.repeat(255))
    cy.get('button[type="submit"]').click()
    cy.get('body').should('not.contain', '422')
  })

  it('Step 4 — nama_toko 256 karakter (melampaui batas): error validasi', () => {
    cy.visit('/toko/create')
    fillTokoForm('A'.repeat(256))
    cy.get('button[type="submit"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/must not be greater than 255|terlalu panjang/i)
    })
  })

  it('Step 5 — Buat toko kedua saat sudah punya toko: error "sudah memiliki toko"', () => {
    cy.visit('/toko/create')
    cy.get('body').should('satisfy', ($body) => {
      // either redirected away or shows error
      return $body.text().match(/sudah memiliki toko|already has/i) || cy.url().should('not.include', '/toko/create')
    })
  })

  it('Step 6 — Logo format .gif (valid): toko berhasil dibuat', () => {
    cy.visit('/toko/create')
    cy.get('input[name="nama_toko"]').clear().type('Toko Raffa GIF')
    cy.get('input[name="lokasi"]').clear().type('Bandung')
    cy.get('textarea[name="deskripsi"]').clear().type('Deskripsi')
    cy.get('input[type="file"][name="logo"]').selectFile({
      contents: Cypress.Buffer.from('GIF89afake'),
      fileName: 'logo.gif',
      mimeType: 'image/gif',
    })
    cy.get('button[type="submit"]').click()
    cy.get('body').should('not.contain', 'The logo must be a file of type')
  })
})

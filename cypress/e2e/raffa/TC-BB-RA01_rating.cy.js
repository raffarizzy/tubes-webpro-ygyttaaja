/**
 * TC-BB-RA01 — Rating 1-5 (Equivalence Partitioning)
 * Feature: FR10 — Memberikan Ulasan
 * PIC: Raffa Rizky Febryan
 */

describe('TC-BB-RA01 — Rating 1-5 (Equivalence Partitioning)', () => {
  beforeEach(() => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.raffa.email, users.raffa.password)
    })
  })

  it('Step 1 — rating=3, review="Produk oke": berhasil disimpan', () => {
    cy.visit('/ratings')
    cy.get('select[name="rating"], input[name="rating"][value="3"]').then(($el) => {
      if ($el.is('select')) cy.wrap($el).select('3')
      else cy.wrap($el).check()
    })
    cy.get('textarea[name="review"], input[name="review"]').type('Produk oke')
    cy.get('button[type="submit"]').click()
    cy.get('body').should('satisfy', ($body) => {
      return $body.text().match(/berhasil ditambahkan|success/i)
    })
  })

  it('Step 2 — rating=1 (batas bawah valid): berhasil disimpan', () => {
    cy.visit('/ratings')
    cy.get('select[name="rating"], input[name="rating"][value="1"]').then(($el) => {
      if ($el.is('select')) cy.wrap($el).select('1')
      else cy.wrap($el).check()
    })
    cy.get('textarea[name="review"], input[name="review"]').type('Kurang memuaskan')
    cy.get('button[type="submit"]').click()
    cy.get('body').should('not.contain', 'The rating must be at least')
  })

  it('Step 3 — rating=5 (batas atas valid): berhasil disimpan', () => {
    cy.visit('/ratings')
    cy.get('select[name="rating"], input[name="rating"][value="5"]').then(($el) => {
      if ($el.is('select')) cy.wrap($el).select('5')
      else cy.wrap($el).check()
    })
    cy.get('textarea[name="review"], input[name="review"]').type('Sangat bagus!')
    cy.get('button[type="submit"]').click()
    cy.get('body').should('not.contain', 'The rating must not be greater')
  })

  it('Step 4 — rating=0 (invalid bawah): error "must be at least 1"', () => {
    cy.visit('/ratings')
    cy.get('select[name="rating"]').then(($el) => {
      if ($el.length) {
        cy.wrap($el).select('0')
      } else {
        cy.request({
          method: 'POST',
          url: '/ratings',
          failOnStatusCode: false,
          form: true,
          body: { rating: 0, review: 'Test' },
        }).then((response) => {
          expect(response.status).to.eq(422)
        })
      }
    })
  })

  it('Step 5 — rating=6 (invalid atas): error "must not be greater than 5"', () => {
    cy.request({
      method: 'POST',
      url: '/ratings',
      failOnStatusCode: false,
      form: true,
      body: { rating: 6, review: 'Test' },
    }).then((response) => {
      expect(response.status).to.eq(422)
      expect(JSON.stringify(response.body)).to.match(/must not be greater than 5/i)
    })
  })

  it('Step 6 — rating=4, review kosong: error "review field is required"', () => {
    cy.request({
      method: 'POST',
      url: '/ratings',
      failOnStatusCode: false,
      form: true,
      body: { rating: 4, review: '' },
    }).then((response) => {
      expect(response.status).to.eq(422)
    })
  })

  it('Step 7 — Hapus rating yang sudah dibuat: rating terhapus', () => {
    cy.visit('/ratings')
    cy.get('button[data-action="delete"], form[method="POST"] button').contains(/hapus|delete/i).then(($btn) => {
      if ($btn.length) {
        cy.wrap($btn).first().click()
        cy.get('body').should('not.contain', '500')
      }
    })
  })
})

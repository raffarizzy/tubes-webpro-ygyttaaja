describe('TC-BB-RA01 — Rating 1-5 (Equivalence Partitioning)', () => {
    beforeEach(() => {
        // Login as Pembeli
        cy.loginUI('raffa@mail.com', 'qwertyui')
        cy.visit('/ratings')
    })

    const selectFirstProduct = () => {
        cy.get('select[name="product_id"]').then(($select) => {
            if ($select.find('option').length > 1) {
                cy.get('select[name="product_id"]').select($select.find('option').eq(1).val())
            } else {
                throw new Error('No products available to rate. Please ensure there are products in the database.')
            }
        })
    }

    const deleteLastRating = () => {
        cy.get('body').then(($body) => {
            if ($body.find('form button:contains("Hapus")').length > 0) {
                cy.get('form button:contains("Hapus")').first().click()
                cy.get('.alert-success').should('exist')
            }
        })
    }

    it('Scenario 1: Valid - Input 3 (Produk oke)', () => {
        selectFirstProduct()
        cy.get('select[name="rating"]').select('3')
        cy.get('textarea[name="review"]').type('Produk oke')
        cy.get('button[type="submit"]').contains('Kirim Rating').click()
        cy.get('.alert-success').should('contain', 'Rating berhasil ditambahkan')
        
        // Cleanup: Delete the rating we just made
        deleteLastRating()
    })

    it('Scenario 2: Valid min - Input 1 (Kurang memuaskan)', () => {
        selectFirstProduct()
        cy.get('select[name="rating"]').select('1')
        cy.get('textarea[name="review"]').type('Kurang memuaskan')
        cy.get('button[type="submit"]').contains('Kirim Rating').click()
        cy.get('.alert-success').should('contain', 'Rating berhasil ditambahkan')
        
        deleteLastRating()
    })

    it('Scenario 3: Valid max - Input 5 (Sangat bagus!)', () => {
        selectFirstProduct()
        cy.get('select[name="rating"]').select('5')
        cy.get('textarea[name="review"]').type('Sangat bagus!')
        cy.get('button[type="submit"]').contains('Kirim Rating').click()
        cy.get('.alert-success').should('contain', 'Rating berhasil ditambahkan')
        
        deleteLastRating()
    })

    it('Scenario 4: Invalid bawah - Input 0 (Test)', () => {
        selectFirstProduct()
        // Force value 0 into select to bypass UI restriction and test backend validation
        cy.get('select[name="rating"]').invoke('val', '0').trigger('change')
        cy.get('textarea[name="review"]').type('Test')
        cy.get('button[type="submit"]').contains('Kirim Rating').click()
        
        // If HTML5 validation blocks it, URL won't change and no success message
        // If it reaches backend, we expect error message
        cy.get('body').should('not.contain', 'Rating berhasil ditambahkan')
    })

    it('Scenario 5: Invalid atas - Input 6 (Test)', () => {
        selectFirstProduct()
        // Force value 6 into select
        cy.get('select[name="rating"]').invoke('val', '6').trigger('change')
        cy.get('textarea[name="review"]').type('Test')
        cy.get('button[type="submit"]').contains('Kirim Rating').click()
        
        cy.get('body').should('not.contain', 'Rating berhasil ditambahkan')
    })

    it('Scenario 6: Invalid review kosong - Input 4 (Review Kosong)', () => {
        selectFirstProduct()
        cy.get('select[name="rating"]').invoke('val', '4').trigger('change')
        // Review left empty
        cy.get('button[type="submit"]').contains('Kirim Rating').click()
        
        // Form should not submit
        cy.url().should('include', '/ratings')
    })

    it('Scenario 7: Delete Rating', () => {
        cy.get('body').then(($body) => {
            if ($body.find('form button:contains("Hapus")').length > 0) {
                cy.get('form button:contains("Hapus")').first().click()
                // Browser confirm dialog is auto-accepted by Cypress
                cy.get('.alert-success').should('exist')
            } else {
                cy.log('No ratings available to delete')
            }
        })
    })
})

describe('TC-BB-RA01 — Rating 1-5 (Equivalence Partitioning)', () => {
    const productId = 1; 
    const productName = "Spakbor depan vario"; 

    beforeEach(() => {
        // Login as Pembeli
        cy.loginUI('raffa@mail.com', 'qwertyui')
        
        // Ensure no existing rating for product before testing creation
        cy.visit('/ratings')
        cy.get('body').then(($body) => {
            // Check if there's a card containing our product name
            const productCard = $body.find('.card').filter((i, el) => el.innerText.includes(productName));
            
            if (productCard.length > 0) {
                cy.wrap(productCard).find('button:contains("Hapus Ulasan")').click()
                cy.get('.alert-success', { timeout: 10000 }).should('exist')
            }
        })
    })

    const deleteRating = (name) => {
        cy.visit('/ratings')
        cy.get('body').then(($body) => {
            const productCard = $body.find('.card').filter((i, el) => el.innerText.includes(name))
            if (productCard.length > 0) {
                cy.wrap(productCard).find('button:contains("Hapus Ulasan")').click()
                cy.get('.alert-success', { timeout: 10000 }).should('contain', 'Rating berhasil dihapus')
            }
        })
    }

    it('Scenario 1: Valid - Input 3 Bintang (Produk oke)', () => {
        cy.visit(`/ratings/create/${productId}`)
        
        // Select 3 stars
        // We target the input and use force: true because the label might be overlapping
        cy.get('input[name="rating"][value="3"]').click({ force: true })
        
        cy.get('textarea[name="review"]').type('Produk oke')
        cy.get('button[type="submit"]').contains('Kirim Ulasan').click()
        
        cy.url().should('include', '/ratings')
        cy.get('.alert-success').should('contain', 'Rating berhasil ditambahkan')
        
        // Cleanup
        deleteRating(productName)
    })

    it('Scenario 2: Valid - Input 1 Bintang (Kurang memuaskan)', () => {
        cy.visit(`/ratings/create/${productId}`)
        
        // Select 3 stars
        // We target the input and use force: true because the label might be overlapping
        cy.get('input[name="rating"][value="1"]').click({ force: true })
        
        cy.get('textarea[name="review"]').type('Kurang memuaskan')
        cy.get('button[type="submit"]').contains('Kirim Ulasan').click()
        
        cy.url().should('include', '/ratings')
        cy.get('.alert-success').should('contain', 'Rating berhasil ditambahkan')
        
        // Cleanup
        deleteRating(productName)
    })

    it('Scenario 3: Valid max - Input 5 Bintang (Sangat bagus!)', () => {
        cy.visit(`/ratings/create/${productId}`)
        
        cy.get('input[name="rating"][value="5"]').click({ force: true })
        cy.get('textarea[name="review"]').type('Sangat bagus!')
        cy.get('button[type="submit"]').contains('Kirim Ulasan').click()
        
        cy.url().should('include', '/ratings')
        cy.get('.alert-success').should('contain', 'Rating berhasil ditambahkan')
        
        // Cleanup
        deleteRating(productName)
    })

    it('Scenario 4: Invalid review kosong', () => {
        cy.visit(`/ratings/create/${productId}`)
        
        cy.get('input[name="rating"][value="4"]').click({ force: true })
        // Review left empty
        
        cy.get('button[type="submit"]').contains('Kirim Ulasan').click()
        
        // Form has 'required' attribute, so it should stay on page (HTML5 validation)
        cy.url().should('include', `/ratings/create/${productId}`)
    })

    it('Scenario 5: Invalid rating kosong', () => {
        cy.visit(`/ratings/create/${productId}`)
        
        // Rating not selected
        cy.get('textarea[name="review"]').type('Test review tanpa bintang')
        
        cy.get('button[type="submit"]').contains('Kirim Ulasan').click()
        
        // Form has 'required' attribute on rating inputs, should stay on page
        cy.url().should('include', `/ratings/create/${productId}`)
    })
})

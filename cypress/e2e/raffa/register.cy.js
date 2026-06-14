describe('TC-WB-RA01 — RegisteredUserController::store() UI Coverage', () => {
    beforeEach(() => {
        cy.visit('/register')
    })

    it('B2: Registration Pass', () => {
        const email = `raffa.${Date.now()}@sparehub.com`
        cy.get('input[name="name"]').type('Raffa Test')
        cy.get('input[name="email"]').type(email)
        cy.get('input[name="phone"]').type('081234567890')
        cy.get('input[name="password"]').type('Password123!')
        cy.get('input[name="password_confirmation"]').type('Password123!')
        cy.get('button[type="submit"]').click()
        
        cy.url().should('eq', Cypress.config().baseUrl + '/')
        cy.get('nav').should('contain', 'Raffa Test')
    })

    it('B1a: Name empty', () => {
        cy.get('input[name="email"]').type('test@example.com')
        cy.get('button[type="submit"]').click()
        // If HTML5 required is present:
        cy.url().should('include', '/register')
    })

    it('B1b: Invalid email format', () => {
        cy.get('input[name="name"]').type('Raffa')
        cy.get('input[name="email"]').type('bukan-email')
        cy.get('button[type="submit"]').click()
        // Check for browser validation or error message
    })

    it('B1f: Password confirmation does not match', () => {
        cy.get('input[name="name"]').type('Raffa')
        cy.get('input[name="email"]').type('raffa.test@sparehub.com')
        cy.get('input[name="phone"]').type('081234567890')
        cy.get('input[name="password"]').type('Password123!')
        cy.get('input[name="password_confirmation"]').type('Password456!')
        cy.get('button[type="submit"]').click()
        
        cy.get('body').should('contain', 'password field confirmation does not match')
    })
})

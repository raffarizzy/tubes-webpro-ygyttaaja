/**
 * Login via UI form (slow path, for tests that need to verify login flow itself)
 */
Cypress.Commands.add('loginUI', (email, password) => {
  cy.visit('/login')
  cy.get('input[name="email"]').type(email)
  cy.get('input[name="password"]').type(password)
  cy.get('button[type="submit"]').click()
})

/**
 * Login via POST (fast path, bypasses UI for tests that assume authenticated state)
 */
Cypress.Commands.add('loginFast', (email, password) => {
  cy.session([email, password], () => {
    cy.request({
      method: 'POST',
      url: '/login',
      form: true,
      body: { email, password, _token: '' },
      followRedirect: false,
    }).then(() => {
      // session cookie is stored automatically
    })
    // fallback: just use UI login inside session
    cy.visit('/login')
    cy.get('input[name="email"]').type(email)
    cy.get('input[name="password"]').type(password)
    cy.get('button[type="submit"]').click()
    cy.url().should('not.include', '/login')
  })
})

/**
 * Get CSRF token from meta tag
 */
Cypress.Commands.add('csrfToken', () => {
  return cy.request('/login').then((response) => {
    const html = document.createElement('div')
    html.innerHTML = response.body
    const meta = html.querySelector('meta[name="csrf-token"]')
    return meta ? meta.getAttribute('content') : ''
  })
})

/**
 * Fill and submit a repeat-string (e.g. 'A'.repeat(255))
 */
Cypress.Commands.add('typeRepeat', { prevSubject: 'element' }, (subject, char, count) => {
  return cy.wrap(subject).invoke('val', char.repeat(count)).trigger('input')
})

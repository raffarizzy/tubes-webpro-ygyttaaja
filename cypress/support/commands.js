/**
 * Login via UI form (slow path, for tests that need to verify login flow itself)
 */
Cypress.Commands.add('loginUI', (email, password) => {
  cy.visit('/login')
  cy.get('input[name="email"]').type(email)
  cy.get('input[name="password"]').type(password)
  cy.get('button[name="loginBtn"]').click()
})

/**
 * Login via POST (fast path, bypasses UI for tests that assume authenticated state)
 *
 * Pakai HTTP request langsung (bukan klik form UI) supaya andal & cepat: tidak
 * terpengaruh page-load CDN maupun throttle yang mudah ter-trigger saat iterasi
 * test berulang. Alur: ambil token CSRF dari /login lalu POST /login.
 */
Cypress.Commands.add('loginFast', (email, password) => {
  cy.session([email, password], () => {
    cy.request('/login').then((res) => {
      const match = res.body.match(/name="_token"\s+value="([^"]+)"/)
      expect(match, 'CSRF _token ditemukan di halaman login').to.not.be.null
      cy.request({
        method: 'POST',
        url: '/login',
        form: true,
        body: { _token: match[1], email, password },
      })
    })
    // pastikan sesi autentikasi terbentuk (nama cookie dari config session.cookie)
    cy.getCookie('laravel-session').should('exist')
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

/**
 * Authenticated request that automatically attaches the Laravel CSRF token
 * (X-XSRF-TOKEN header, decoded from the XSRF-TOKEN cookie).
 *
 * Web routes are CSRF-protected, so a plain cy.request POST/PUT/DELETE returns
 * 419. Use this for non-GET calls to web routes after logging in.
 * Defaults: Accept JSON + failOnStatusCode false (caller asserts status).
 */
Cypress.Commands.add('apiRequest', (options) => {
  return cy.getCookie('XSRF-TOKEN').then((cookie) => {
    const headers = { Accept: 'application/json', ...(options.headers || {}) }
    if (cookie) headers['X-XSRF-TOKEN'] = decodeURIComponent(cookie.value)
    return cy.request({ failOnStatusCode: false, ...options, headers })
  })
})
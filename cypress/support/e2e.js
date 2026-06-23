import './commands'
import 'cypress-file-upload';

// PNG transparan 1x1 untuk menggantikan gambar eksternal yang di-stub.
const TRANSPARENT_PNG = Cypress.Buffer.from(
  'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
  'base64'
)

beforeEach(() => {
  // Logo & avatar memakai host eksternal (i.ibb.co) yang sering lambat/menggantung
  // sehingga 'load' event halaman tidak selesai → cy.visit timeout 60s.
  // Stub agar request gambar tsb resolve instan tanpa menunggu jaringan.
  cy.intercept({ hostname: 'i.ibb.co' }, (req) => {
    req.reply({ statusCode: 200, headers: { 'content-type': 'image/png' }, body: TRANSPARENT_PNG })
  })
})

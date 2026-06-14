describe('TC-BB-RA03 — Kelola Produk', () => {
    beforeEach(() => {
        // Login as Penjual who has a store
        cy.loginUI('penjual@sparehub.com', 'Password123!')
        cy.visit('/toko')
    })

    it('R1 & R3 — Update produk (tanpa & dengan gambar)', () => {
        // Klik tombol edit pada produk pertama
        cy.get('#produk-list .btn-warning').first().click()
        
        // Update field
        cy.get('#editNama').clear().type('Produk Terupdate')
        cy.get('#editHarga').clear().type('150000')
        cy.get('#editStok').clear().type('20')
        
        // R3: Upload gambar baru
        cy.get('#editGambar').selectFile('cypress/fixtures/produk.jpg')
        
        // Klik Update
        cy.get('#modalEdit button').contains('Update').click()
        
        // Verifikasi sukses (window.alert dihandle Cypress otomatis)
        cy.on('window:alert', (str) => {
            expect(str).to.equal('Produk berhasil diupdate')
        })
    })

    it('R2 — Update dengan data invalid (Harga bukan numerik)', () => {
        cy.get('#produk-list .btn-warning').first().click()
        
        // Force invalid value into numeric input
        cy.get('#editHarga').invoke('val', 'abc').trigger('input')
        
        cy.get('#modalEdit button').contains('Update').click()
        
        // Browser validation might catch this, or backend error
        // If backend catches it, expect an alert or message
    })

    it('R1 — Delete produk', () => {
        cy.get('#produk-list .btn-danger').first().click()
        
        // Klik hapus di modal
        cy.get('#modalHapus button').contains('Hapus').click()
        
        cy.on('window:alert', (str) => {
            expect(str).to.equal('Produk berhasil dihapus')
        })
    })
})

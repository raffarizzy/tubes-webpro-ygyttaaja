describe('TC-BB-RA02 — Nama Toko', () => {
    beforeEach(() => {
        // Login menggunakan user tetap sesuai permintaan
        cy.loginUI('frizam@mail.com', 'qwertyui')
        
        // Pastikan kita berada di halaman create. 
        // Jika user sudah punya toko, Laravel mungkin me-redirect ke /toko
        cy.visit('/toko/create')
    })

    it('Step 1: Nama Toko Kosong (Invalid)', () => {
        cy.get('textarea[name="deskripsi_toko"]').type('Deskripsi valid')
        cy.get('input[name="lokasi"]').type('Bandung')
        cy.get('button').contains('Buat Toko').click()
        cy.url().should('include', '/toko/create')
    })

    it('Step 2: Nama Toko 1 Karakter (Valid - Min Boundary)', () => {
        // Log URL saat ini
        cy.url().then(url => cy.log('Current URL before test:', url))

        cy.get('input[name="nama_toko"]').type('A')
        cy.get('textarea[name="deskripsi_toko"]').type('Deskripsi Toko A')
        cy.get('input[name="lokasi"]').type('Bandung')

        // Gunakan logo.png yang baru dibuat (valid image)
        cy.get('input[name="logo"]').attachFile('logo.png');

        cy.get('button').contains('Buat Toko').click()

        // Debug: Tunggu dan cek apakah ada alert/error yang muncul di layar
        cy.get('body').then(($body) => {
            if ($body.find('.alert-danger').length > 0 || $body.find('.alert-error').length > 0) {
                const errorText = $body.find('.alert-danger, .alert-error').text().trim();
                assert.fail('GAGAL DARI LARAVEL: ' + errorText);
            }
            
            // Cek juga pesan error validasi input (Bootstrap is-invalid)
            if ($body.find('.invalid-feedback').length > 0) {
                const validationMsg = $body.find('.invalid-feedback').first().text().trim();
                assert.fail('GAGAL VALIDASI: ' + validationMsg);
            }
        });

        cy.url().should('not.include', '/toko/create')
    })

    it('Step 4: Nama Toko 256 Karakter (Invalid - Above Boundary)', () => {
        // We use invoke to bypass maxlength if it exists, or just type a long string
        const longName = 'A'.repeat(256)
        cy.get('input[name="nama_toko"]').invoke('val', longName).trigger('input')
        cy.get('textarea[name="deskripsi_toko"]').type('Deskripsi valid')
        cy.get('input[name="lokasi"]').type('Bandung')
        cy.get('input[name="logo"]').selectFile('cypress/fixtures/logo.jpg')
        cy.get('button').contains('Buat Toko').click()
        
        // Depending on backend validation, it might show an error message
        cy.get('body').should('contain', 'nama toko must not be greater than 255')
    })

    it('Step 6: Logo format .gif (Valid per TC)', () => {
        cy.get('input[name="nama_toko"]').type('Toko GIF')
        cy.get('textarea[name="deskripsi_toko"]').type('Deskripsi Toko GIF')
        cy.get('input[name="lokasi"]').type('Jakarta')
        // Create a dummy gif
        cy.get('input[name="logo"]').selectFile({
            contents: Cypress.Buffer.from('G1F89a...'),
            fileName: 'logo.gif',
            mimeType: 'image/gif',
        })
        cy.get('button').contains('Buat Toko').click()
        cy.url().should('not.include', '/toko/create')
    })
})

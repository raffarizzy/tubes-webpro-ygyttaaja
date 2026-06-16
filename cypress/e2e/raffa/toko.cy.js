describe('TC-BB-RA02 — Nama Toko (Boundary Value Analysis)', () => {
    const userEmail = 'frizam@mail.com';
    const userPassword = 'qwertyui';

    beforeEach(() => {
        // Login as verified seller
        cy.loginUI(userEmail, userPassword);

        // Reset state: Hapus toko jika sudah ada agar bisa tes /toko/create
        cy.visit('/toko');
        cy.get('body').then(($body) => {
            // Jika masuk ke halaman profil toko (berarti sudah punya toko)
            if ($body.find('button:contains("Hapus Toko")').length > 0 || $body.find('.shop-details').length > 0) {
                // Gunakan request delete ke route yang sudah kita siapkan
                // Kita perlu ID toko, atau kita bisa coba cari tombol hapus di UI jika ada
                // Karena di UI mungkin belum ada tombol hapus, kita gunakan script pembersih via terminal/tinker
                // Tapi untuk Cypress, kita bisa gunakan cy.request jika route sudah ada
                
                cy.window().then((win) => {
                    const tokoId = $body.find('[data-toko-id]').data('toko-id') || 1; // Fallback
                    cy.request({
                        method: 'DELETE',
                        url: `/toko/${tokoId}`,
                        headers: {
                            'X-CSRF-TOKEN': Cypress.$('meta[name="csrf-token"]').attr('content')
                        },
                        failOnStatusCode: false
                    });
                });
            }
        });

        cy.visit('/toko/create');
    });

    it('Step 1: Nama Toko 0 Karakter (Invalid)', () => {
        // Form has 'required' attribute, so empty submit should be blocked by browser
        cy.get('textarea[name="deskripsi_toko"]').type('Deskripsi valid');
        cy.get('select[name="provinsi"]').select('32'); // Jawa Barat
        cy.get('select[name="kota"]').select('3273'); // Bandung
        cy.get('select[name="kecamatan"]').select('3273100'); // Coblong
        cy.get('input[name="lokasi"]').type('Jl. Ganesha No. 10');
        cy.get('input[name="kode_pos"]').type('40132');
        cy.get('input[name="logo"]').selectFile('cypress/fixtures/logo.png', { force: true });

        cy.get('button[name="buatTokoBtn"]').click();
        
        // Browser validation: URL should not change
        cy.url().should('include', '/toko/create');
    });

    it('Step 2: Nama Toko 1 Karakter (Valid - Min Boundary)', () => {
        cy.get('input[name="nama_toko"]').type('A');
        cy.get('textarea[name="deskripsi_toko"]').type('Deskripsi valid');
        cy.get('select[name="provinsi"]').select('32');
        cy.get('select[name="kota"]').select('3273');
        cy.get('select[name="kecamatan"]').select('3273100');
        cy.get('input[name="lokasi"]').type('Jl. Ganesha No. 10');
        cy.get('input[name="kode_pos"]').type('40132');
        cy.get('input[name="logo"]').selectFile('cypress/fixtures/logo.png', { force: true });

        cy.get('button[name="buatTokoBtn"]').click();

        // Should succeed and redirect to shop profile
        cy.url().should('include', '/toko');
        cy.get('body').should('contain', 'A');
    });

    it('Step 3: Nama Toko 11 Karakter (Valid - Normal)', () => {
        cy.get('input[name="nama_toko"]').type('Toko Raffa');
        cy.get('textarea[name="deskripsi_toko"]').type('Deskripsi valid');
        cy.get('select[name="provinsi"]').select('32');
        cy.get('select[name="kota"]').select('3273');
        cy.get('select[name="kecamatan"]').select('3273100');
        cy.get('input[name="lokasi"]').type('Jl. Ganesha No. 10');
        cy.get('input[name="kode_pos"]').type('40132');
        cy.get('input[name="logo"]').selectFile('cypress/fixtures/logo.png', { force: true });

        cy.get('button[name="buatTokoBtn"]').click();

        cy.url().should('include', '/toko');
        cy.get('body').should('contain', 'Toko Raffa');
    });

    it('Step 4: Nama Toko 255 Karakter (Valid - Max Boundary)', () => {
        const longName = 'A'.repeat(255);
        cy.get('input[name="nama_toko"]').invoke('val', longName).trigger('input');
        cy.get('textarea[name="deskripsi_toko"]').type('Deskripsi valid');
        cy.get('select[name="provinsi"]').select('32');
        cy.get('select[name="kota"]').select('3273');
        cy.get('select[name="kecamatan"]').select('3273100');
        cy.get('input[name="lokasi"]').type('Jl. Ganesha No. 10');
        cy.get('input[name="kode_pos"]').type('40132');
        cy.get('input[name="logo"]').selectFile('cypress/fixtures/logo.png', { force: true });

        cy.get('button[name="buatTokoBtn"]').click();

        cy.url().should('include', '/toko');
    });

    it('Step 5: Nama Toko 256 Karakter (Invalid - Over Boundary)', () => {
        const overName = 'A'.repeat(256);
        cy.get('input[name="nama_toko"]').invoke('val', overName).trigger('input');
        cy.get('textarea[name="deskripsi_toko"]').type('Deskripsi valid');
        cy.get('select[name="provinsi"]').select('32');
        cy.get('select[name="kota"]').select('3273');
        cy.get('select[name="kecamatan"]').select('3273100');
        cy.get('input[name="lokasi"]').type('Jl. Ganesha No. 10');
        cy.get('input[name="kode_pos"]').type('40132');
        cy.get('input[name="logo"]').selectFile('cypress/fixtures/logo.png', { force: true });

        cy.get('button[name="buatTokoBtn"]').click();
        
        // Backend should catch this and show error
        cy.get('.alert-danger').should('exist');
    });
});

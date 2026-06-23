/**
 * TC-BB-R01 — Filter Kategori (Equivalence Partitioning)
 * Feature: FR1 — Filter Kategori Produk
 * PIC: Riziq Rizwan
 */

describe('TC-BB-R01 — Filter Kategori (Equivalence Partitioning)', () => {
  beforeEach(() => {
    // Login fast with the requested credentials
    cy.fixture('users').then((users) => {
      // Use the email & password from the user request
      cy.loginFast(users.riziq.email, users.riziq.password);
    });
  });

  it('Partition 1 — Semua Kategori (Semua produk harus tampil)', () => {
    cy.visit('/');
    
    // Select "Semua Kategori" (value "")
    cy.get('#category-filter').select('');
    
    // Verify both items are visible
    cy.get('#produk-container').should('contain', 'Resistor');
    cy.get('#produk-container').should('contain', 'Kapasitor');
    cy.get('#results-info').should('contain', 'Menampilkan semua produk');
  });

  it('Partition 2 — Kategori Valid "Resistor" (Hanya produk Resistor yang tampil)', () => {
    cy.visit('/');
    
    // Select "Resistor"
    cy.get('#category-filter').select('Resistor');
    
    // Verify Resistor is visible and Kapasitor is NOT visible
    cy.get('#produk-container').should('contain', 'Resistor');
    cy.get('#produk-container').should('not.contain', 'Kapasitor');
    cy.get('#results-info').should('contain', 'Ditemukan 1 produk');
  });

  it('Partition 3 — Kategori Valid "Capacitor" (Hanya produk Kapasitor yang tampil)', () => {
    cy.visit('/');
    
    // Select "Capacitor"
    cy.get('#category-filter').select('Capacitor');
    
    // Verify Kapasitor is visible and Resistor is NOT visible
    cy.get('#produk-container').should('contain', 'Kapasitor');
    cy.get('#produk-container').should('not.contain', 'Resistor');
    cy.get('#results-info').should('contain', 'Ditemukan 1 produk');
  });

  it('Partition 4 — Kategori Tanpa Produk (Pesan tidak ada produk ditemukan harus tampil)', () => {
    cy.visit('/');
    
    // Select a category that exists in database but has no products, e.g. "Bearing"
    cy.get('#category-filter').select('Bearing');
    
    // Verify no product card, instead show the "tidak ada produk" placeholder
    cy.get('#produk-container').should('contain', 'Tidak ada produk ditemukan');
    cy.get('#produk-container').should('not.contain', 'Resistor');
    cy.get('#produk-container').should('not.contain', 'Kapasitor');
    cy.get('#results-info').should('contain', 'Ditemukan 0 produk');
  });

  it('Partition 5 — Kategori Invalid / Tidak Terdaftar (Pesan error kategori tidak valid harus tampil)', () => {
    cy.visit('/');
    
    // Set invalid category directly on filterState and apply filters
    cy.window().then((win) => {
      win.filterState.category = 'KategoriNgaco123';
      win.applyFilters();
    });
    
    // Verify "Kategori Tidak Valid" error is displayed
    cy.get('#produk-container').should('contain', 'Kategori Tidak Valid');
    cy.get('#category-error').should('be.visible');
    
    // Verify results info displays the error
    cy.get('#results-info').should('contain', 'Error: Kategori "KategoriNgaco123" tidak valid');
    
    // Verify products are not shown
    cy.get('#produk-container').should('not.contain', 'Resistor');
    cy.get('#produk-container').should('not.contain', 'Kapasitor');
  });
});

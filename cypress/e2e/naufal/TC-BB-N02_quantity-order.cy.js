/**
 * TC-BB-N02 — Batas Quantity Order (Boundary Value Analysis)
 * Feature: FR7 — Melakukan Pemesanan
 * PIC: Naufal Muhammad Dzulfikar
 *
 * Boundary pada field `jumlah` (produk id=1 stok=10, validasi min:1):
 *   0  -> invalid (di bawah minimum)
 *   1  -> valid   (batas minimum)
 *   5  -> valid   (tengah rentang)
 *   10 -> valid   (tepat batas stok)
 *   11 -> invalid (melebihi stok)
 *
 * Pendekatan HYBRID: validasi inti dilakukan di level API (cy.request) supaya
 * rigorous & sesuai TC doc (jumlah=0 ditolak server, jumlah=11 error stok dari
 * Node), LALU setiap langkah membuka halaman terkait (cy.visit) agar UI tampil
 * untuk bukti screenshot.
 *
 * Prasyarat: `php artisan db:seed --class=CypressSeeder` (user tester + produk id=1 stok=10),
 * server Laravel (8000) & Node API (3001) hidup.
 */

describe('TC-BB-N02 — Batas Quantity Order (Boundary Value Analysis)', () => {
  beforeEach(() => {
    cy.fixture('users').then((users) => {
      cy.loginFast(users.tester.email, users.tester.password)
    })
    cy.request('/') // refresh cookie XSRF-TOKEN untuk apiRequest
    cy.apiRequest({ method: 'DELETE', url: '/keranjang/clear' }) // mulai dari keranjang kosong
  })

  const addToCart = (jumlah) =>
    cy.apiRequest({
      method: 'POST',
      url: '/keranjang/item',
      form: true,
      body: { product_id: 1, jumlah },
    })

  const updateQty = (itemId, jumlah) =>
    cy.apiRequest({
      method: 'PUT',
      url: `/keranjang/item/${itemId}`,
      form: true,
      body: { jumlah },
    })

  it('Step 1 — jumlah=0 (di bawah minimum): ditolak', () => {
    // Validasi SERVER: walau UI di-bypass, API menolak jumlah=0
    addToCart(0).then((res) => {
      expect(res.body.success, 'request ditolak').to.eq(false)
      expect(res.status, 'status error (>=400)').to.be.gte(400)
    })
    // Bukti: keranjang tetap KOSONG karena jumlah=0 ditolak (tidak ada item masuk)
    cy.visit('/keranjang')
    cy.location('pathname').should('eq', '/keranjang')
    cy.get('#keranjang-container').should('contain', 'kosong')
    cy.get('#total-item').should('have.text', '0')
  })

  it('Step 2 — jumlah=1 (batas minimum valid): masuk keranjang', () => {
    addToCart(1).then((res) => {
      expect(res.status).to.be.oneOf([200, 201])
      expect(res.body.success).to.eq(true)
      expect(res.body.data.id, 'id item keranjang').to.exist
    })
    // tampilkan keranjang berisi item
    cy.visit('/keranjang')
    cy.location('pathname').should('eq', '/keranjang')
  })

  it('Step 3 — update jumlah ke 5 (tengah rentang valid): berhasil', () => {
    addToCart(1).then((res) => {
      updateQty(res.body.data.id, 5).then((upd) => {
        expect(upd.status).to.eq(200)
        expect(upd.body.success).to.eq(true)
        expect(Number(upd.body.data.jumlah)).to.eq(5)
      })
    })
    cy.visit('/keranjang')
    cy.location('pathname').should('eq', '/keranjang')
  })

  it('Step 4 — update jumlah ke 10 (tepat batas stok): berhasil', () => {
    addToCart(1).then((res) => {
      updateQty(res.body.data.id, 10).then((upd) => {
        expect(upd.status).to.eq(200)
        expect(upd.body.success).to.eq(true)
        expect(Number(upd.body.data.jumlah)).to.eq(10)
      })
    })
    cy.visit('/keranjang')
    cy.location('pathname').should('eq', '/keranjang')
  })

  it('Step 5 — update jumlah ke 11 (melebihi stok 10): ditolak dengan pesan stok', () => {
    addToCart(1).then((res) => {
      const id = res.body.data.id
      updateQty(id, 10).then((u10) => {
        expect(u10.body.success, 'qty=10 (batas stok) diterima').to.eq(true)
      })
      // (a) SERVER: paksa update ke 11 via API → ditolak 400 + pesan stok
      updateQty(id, 11).then((upd) => {
        expect(upd.body.success, 'request ditolak').to.eq(false)
        expect(upd.status, 'status error (>=400)').to.be.gte(400)
        expect(upd.body.message, 'pesan stok').to.match(/stok/i)
      })
    })
    // (b) UI: di keranjang qty=10, klik "+" → notifikasi stok tampil ke user
    cy.visit('/keranjang')
    cy.get('.btn-increase').first().click()
    cy.get('.notification-toast').should('contain', 'Stok')
  })

  it('Step 6 — jumlah valid (1): siap lanjut ke checkout/pembayaran (Duitku)', () => {
    // Catatan: redirect pembayaran Duitku asli butuh order dibuat dulu + key
    // DUITKU_* di .env + call eksternal ke api-sandbox.duitku.com, jadi di sini
    // cukup diverifikasi halaman checkout dapat diakses dengan qty valid.
    addToCart(1).then((res) => {
      expect(res.body.success).to.eq(true)
    })
    cy.visit('/checkout')
    cy.location('pathname').should('eq', '/checkout')
    cy.get('body').invoke('text').should('match', /checkout|bayar|pembayaran|alamat/i)
  })
})

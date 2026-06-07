const request = require('supertest');
const app = require('../src/app');
const db = require('../src/config/db');

jest.mock('../src/config/db', () => ({
  query: jest.fn(),
  getConnection: jest.fn().mockResolvedValue({
    release: jest.fn(),
  }),
  on: jest.fn(),
}));

describe('SpareHub API Full Integration', () => {
  afterEach(() => {
    jest.clearAllMocks();
  });

  // 1. Profile Tests
  test('GET /api/profile/:id should return user profile', async () => {
    // The controller returns { success: true, data: user } or just user?
    // Let's check profile.controller.js again.
    // It returns user directly.
    db.query.mockResolvedValue([[{ id: 1, name: 'Test User', email: 'test@test.com' }]]);
    const response = await request(app).get('/api/profile/1');
    expect(response.statusCode).toBe(200);
    // If it returns directly:
    expect(response.body.name).toBe('Test User');
  });

  // 2. Product Search
  test('GET /api/products with search should return filtered results', async () => {
    db.query.mockResolvedValue([[{ id: 1, nama: 'Busi Racing' }]]);
    const response = await request(app).get('/api/products?search=Busi');
    expect(response.statusCode).toBe(200);
    expect(response.body.data[0].nama).toBe('Busi Racing');
  });

  // 3. Cart Management
  test('POST /api/cart/item should add item to cart', async () => {
    // addItem makes multiple queries:
    // 1. SELECT stok, harga FROM products
    db.query.mockResolvedValueOnce([[{ stok: 10, harga: 50000 }]]);
    // 2. SELECT id FROM keranjangs
    db.query.mockResolvedValueOnce([[{ id: 1 }]]);
    // 3. SELECT id, jumlah FROM barang_keranjangs
    db.query.mockResolvedValueOnce([[]]);
    // 4. INSERT INTO barang_keranjangs
    db.query.mockResolvedValueOnce([{ insertId: 100 }]);

    const response = await request(app)
      .post('/api/cart/item')
      .send({ user_id: 1, product_id: 1, jumlah: 2 });
    
    expect(response.statusCode).toBe(201);
    expect(response.body.message).toBe('Produk berhasil ditambahkan ke keranjang');
  });

  // 4. Order History
  test('GET /api/history/:userId should return list of orders', async () => {
    db.query.mockResolvedValue([[{ id: 50, total_harga: 100000, status: 'paid' }]]);
    const response = await request(app).get('/api/history/1');
    expect(response.statusCode).toBe(200);
    expect(response.body.data[0].id).toBe(50);
  });
});

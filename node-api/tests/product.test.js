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

describe('Product API Endpoints', () => {
  afterEach(() => {
    jest.clearAllMocks();
  });

  test('GET /api/products should return list of products', async () => {
    const mockProducts = [
      { id: 1, nama: 'Product 1', harga: 1000 },
      { id: 2, nama: 'Product 2', harga: 2000 }
    ];
    db.query.mockResolvedValue([mockProducts]);

    const response = await request(app).get('/api/products');

    expect(response.statusCode).toBe(200);
    expect(response.body.success).toBe(true);
    expect(response.body.data).toHaveLength(2);
    expect(response.body.data[0].nama).toBe('Product 1');
  });

  test('GET /api/products/:id should return detail product', async () => {
    const mockProduct = { 
      id: 1, 
      nama: 'Product 1', 
      harga: 1000,
      nama_toko: 'Toko A',
      category_nama: 'Cat A'
    };
    db.query.mockResolvedValue([[mockProduct]]);

    const response = await request(app).get('/api/products/1');

    expect(response.statusCode).toBe(200);
    expect(response.body.data.nama).toBe('Product 1');
  });

  test('GET /api/products/:id should return 404 if not found', async () => {
    db.query.mockResolvedValue([[]]);

    const response = await request(app).get('/api/products/999');

    expect(response.statusCode).toBe(404);
    expect(response.body.success).toBe(false);
  });

  test('POST /api/products should create a new product', async () => {
    db.query.mockResolvedValue([{ insertId: 10 }]);

    const newProduct = {
      toko_id: 1,
      category_id: 1,
      nama: 'New Product',
      deskripsi: 'Desc',
      harga: 5000,
      stok: 10,
      imagePath: 'path/to/img.jpg'
    };

    const response = await request(app)
      .post('/api/products')
      .send(newProduct);

    expect(response.statusCode).toBe(201);
    expect(response.body.data.id).toBe(10);
    expect(response.body.message).toBe('Produk berhasil dibuat');
  });

  test('POST /api/products should return validation error if fields missing', async () => {
    const response = await request(app)
      .post('/api/products')
      .send({ nama: 'Incomplete' });

    expect(response.statusCode).toBe(400);
    expect(response.body.success).toBe(false);
  });
});

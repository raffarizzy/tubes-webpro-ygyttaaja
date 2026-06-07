const request = require('supertest');
const app = require('../src/app');

// Mock the DB connection to avoid needing a real MySQL instance
jest.mock('../src/config/db', () => ({
  getConnection: jest.fn().mockResolvedValue({
    release: jest.fn(),
  }),
  query: jest.fn(),
  on: jest.fn(),
}));

describe('Node.js API Basic Tests', () => {
  test('GET / should return health check info', async () => {
    const response = await request(app).get('/');
    expect(response.statusCode).toBe(200);
    expect(response.body).toHaveProperty('message', 'SpareHub API is running!');
    expect(response.body).toHaveProperty('endpoints');
  });

  test('GET /api/test should return legacy test message', async () => {
    const response = await request(app).get('/api/test');
    expect(response.statusCode).toBe(200);
    expect(response.body.message).toBe('Node.js API jalan');
  });

  test('404 on non-existent endpoint', async () => {
    const response = await request(app).get('/api/does-not-exist');
    expect(response.statusCode).toBe(404);
    expect(response.body.success).toBe(false);
  });
});

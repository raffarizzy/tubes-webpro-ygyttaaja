// =====================================================
// SpareHub Node.js API Server
// =====================================================
const express = require('express');
const mysql = require('mysql2/promise');
const cors = require('cors');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

// =====================================================
// Middleware
// =====================================================
app.use(cors()); // Enable CORS untuk akses dari Laravel frontend
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// =====================================================
// Database Connection Pool
// =====================================================
const pool = mysql.createPool({
  host: process.env.DB_HOST || '127.0.0.1',
  port: process.env.DB_PORT || 3306,
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_DATABASE || 'tubes',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
});

// =====================================================
// Test Database Connection
// =====================================================
pool.getConnection()
  .then(connection => {
    console.log('✅ Database connected successfully!');
    connection.release();
  })
  .catch(err => {
    console.error('❌ Database connection failed:', err.message);
  });

// =====================================================
// API ROUTES
// =====================================================

// Health Check
app.get('/', (req, res) => {
  res.json({
    message: 'SpareHub API is running!',
    version: '1.0.0',
    endpoints: {
      products: {
        all: 'GET /api/products',
        detail: 'GET /api/products/:id',
        ratings: 'GET /api/products/:id/ratings'
      },
      tokos: {
        detail: 'GET /api/tokos/:id'
      }
    }
  });
});

// =====================================================
// PRODUCTS ENDPOINTS
// =====================================================

// GET all products
app.get('/api/products', async (req, res) => {
  try {
    const [products] = await pool.query(`
      SELECT
        p.*,
        p.toko_id as tokoId,
        p.category_id as categoryId
      FROM products p
    `);
    res.json({
      success: true,
      data: products
    });
  } catch (error) {
    console.error('Error fetching products:', error);
    res.status(500).json({
      success: false,
      message: 'Failed to fetch products',
      error: error.message
    });
  }
});

// GET product by ID
app.get('/api/products/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const [products] = await pool.query(`
      SELECT
        p.*,
        p.toko_id as tokoId,
        p.category_id as categoryId
      FROM products p
      WHERE p.id = ?
    `, [id]);

    if (products.length === 0) {
      return res.status(404).json({
        success: false,
        message: 'Product not found'
      });
    }

    res.json({
      success: true,
      data: products[0]
    });
  } catch (error) {
    console.error('Error fetching product:', error);
    res.status(500).json({
      success: false,
      message: 'Failed to fetch product',
      error: error.message
    });
  }
});

// GET product ratings
app.get('/api/products/:id/ratings', async (req, res) => {
  try {
    const { id } = req.params;
    const [ratings] = await pool.query(
      'SELECT * FROM ratings WHERE produkId = ? ORDER BY tanggal DESC',
      [id]
    );

    res.json({
      success: true,
      data: ratings
    });
  } catch (error) {
    console.error('Error fetching ratings:', error);
    res.status(500).json({
      success: false,
      message: 'Failed to fetch ratings',
      error: error.message
    });
  }
});

// =====================================================
// TOKOS ENDPOINTS
// =====================================================

// GET all tokos
app.get('/api/tokos', async (req, res) => {
  try {
    const [tokos] = await pool.query('SELECT * FROM tokos');
    res.json({
      success: true,
      data: tokos
    });
  } catch (error) {
    console.error('Error fetching tokos:', error);
    res.status(500).json({
      success: false,
      message: 'Failed to fetch tokos',
      error: error.message
    });
  }
});

// GET toko by ID
app.get('/api/tokos/:id', async (req, res) => {
  try {
    const { id } = req.params;
    const [tokos] = await pool.query('SELECT * FROM tokos WHERE id = ?', [id]);

    if (tokos.length === 0) {
      return res.status(404).json({
        success: false,
        message: 'Toko not found'
      });
    }

    res.json({
      success: true,
      data: tokos[0]
    });
  } catch (error) {
    console.error('Error fetching toko:', error);
    res.status(500).json({
      success: false,
      message: 'Failed to fetch toko',
      error: error.message
    });
  }
});

// =====================================================
// 404 Handler
// =====================================================
app.use((req, res) => {
  res.status(404).json({
    success: false,
    message: 'Endpoint not found'
  });
});

// =====================================================
// Start Server
// =====================================================
app.listen(PORT, () => {
  console.log(`🚀 SpareHub API running on http://localhost:${PORT}`);
  console.log(`📚 API Documentation: http://localhost:${PORT}`);
});
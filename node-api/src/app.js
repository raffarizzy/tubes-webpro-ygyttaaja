const express = require('express');
const cors = require('cors');
const db = require('./config/db');
const cookieParser = require('cookie-parser');

// Import Routes
const profileRoutes = require('./routes/profile.routes');
const productRoutes = require('./routes/product.routes');
const authRoutes = require('./routes/auth.routes');
const tokoRoutes = require('./routes/toko.routes');

const app = express();

// =====================================================
// Middleware
// =====================================================
app.use(cookieParser());
app.use(cors({
  origin: 'http://127.0.0.1:8000',
  credentials: true
}));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// =====================================================
// Test Database Connection
// =====================================================
db.getConnection()
  .then(connection => {
    console.log('✅ Database connected successfully!');
    connection.release();
  })
  .catch(err => {
    console.error('❌ Database connection failed:', err.message);
  });

// =====================================================
// API Routes
// =====================================================

// Health Check
app.get('/', (req, res) => {
  res.json({
    message: 'SpareHub API is running!',
    version: '1.0.0',
    endpoints: {
      products: '/api/products',
      profile: '/api/profile'
    }
  });
});

// Legacy test endpoint
app.get('/api/test', (req, res) => {
  res.json({ message: 'Node.js API jalan' });
});

// Mount Routes
app.use('/api/profile', profileRoutes);
app.use('/api/products', productRoutes);
app.use('/api/auth', authRoutes);
app.use('/api/toko', tokoRoutes);

// =====================================================
// 404 Handler
// =====================================================
app.use((req, res) => {
  res.status(404).json({
    success: false,
    message: 'Endpoint not found'
  });
});

module.exports = app;
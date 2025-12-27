const express = require('express');
const cors = require('cors');
const db = require('./config/db');

// Import Routes
const profileRoutes = require('./routes/profile.routes');
const productRoutes = require('./routes/product.routes');
const cartRoutes = require('./routes/cart.routes');
const tokoRoutes = require('./routes/toko.routes');
const checkoutRoutes = require('./routes/checkout.routes');
const historyRoutes = require('./routes/history.routes');
const ratingRoutes = require('./routes/rating.routes');

const app = express();

// =====================================================
// Middleware
// =====================================================
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// =====================================================
// Test Database Connection
// =====================================================
db.getConnection()
  .then(connection => {
    console.log('Database connected successfully!');
    connection.release();
  })
  .catch(err => {
    console.error('Database connection failed:', err.message);
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
      profile: '/api/profile',
      cart: '/api/cart',
      toko: '/api/toko',
      orders: '/api/orders',
      history: '/api/history'
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
app.use('/api/cart', cartRoutes);
app.use('/api/toko', tokoRoutes);
app.use('/api/orders', checkoutRoutes);
app.use('/api/history', historyRoutes);
app.use('/api/ratings', ratingRoutes);

// =====================================================
// 404 Handler
// =====================================================
app.use((req, res) => {
  res.status(404).json({
    success: false,
    message: 'Endpoint not found',
    requested_url: req.originalUrl
  });
});

module.exports = app;
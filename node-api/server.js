// =====================================================
// SpareHub Node.js API Server
// =====================================================
require('dotenv').config();
const express = require('express');
const cors = require('cors');
const { pool } = require('./src/config/db');

// Import Routes
const productsRoutes = require('./src/routes/product.routes');
const tokosRoutes = require('./src/routes/toko.routes');
const usersRoutes = require('./src/routes/user.routes');
// const cartRoutes = require('./src/routes/cart'); // TODO: Uncomment when cart feature is ready

const app = express();
const PORT = process.env.PORT || 3000;

// =====================================================
// Middleware
// =====================================================
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

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
// API Routes
// =====================================================

// Health Check
app.get('/', (req, res) => {
  res.json({
    message: 'SpareHub API is running!',
    version: '1.0.0',
    endpoints: {
      products: '/api/products',
      tokos: '/api/tokos',
      users: '/api/users'
    }
  });
});

// Mount Routes
app.use('/api/products', productsRoutes);
app.use('/api/tokos', tokosRoutes);
app.use('/api/users', usersRoutes);
// app.use('/api/cart', cartRoutes); // TODO: Uncomment when cart feature is ready

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
  console.log(`🚀 Server running on http://localhost:${PORT}`);
});
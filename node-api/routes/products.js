// Products API Routes
const express = require('express');
const router = express.Router();

module.exports = (pool) => {
  // GET all products
  router.get('/', async (req, res) => {
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
  router.get('/:id', async (req, res) => {
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
  router.get('/:id/ratings', async (req, res) => {
    try {
      const { id } = req.params;
      const [ratings] = await pool.query(
        'SELECT * FROM ratings WHERE product_id = ? ORDER BY created_at DESC',
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

  return router;
};
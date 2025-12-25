// Tokos API Routes
const express = require('express');
const router = express.Router();

module.exports = (pool) => {
  // GET all tokos
  router.get('/', async (req, res) => {
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
  router.get('/:id', async (req, res) => {
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

  return router;
};
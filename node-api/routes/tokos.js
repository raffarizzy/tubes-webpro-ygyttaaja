// =====================================================
// Tokos API Routes
// =====================================================
const express = require('express');
const router = express.Router();
const TokoController = require('../controllers/tokoController');

module.exports = (pool) => {
  const controller = new TokoController(pool);

  // GET all tokos
  router.get('/', (req, res) => controller.index(req, res));

  // GET toko by ID
  router.get('/:id', (req, res) => controller.show(req, res));

  // POST create new toko
  router.post('/', (req, res) => controller.store(req, res));

  // PUT update toko
  router.put('/:id', (req, res) => controller.update(req, res));

  // DELETE toko
  router.delete('/:id', (req, res) => controller.destroy(req, res));

  return router;
};
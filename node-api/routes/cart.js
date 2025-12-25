// =====================================================
// Cart API Routes
// =====================================================
const express = require('express');
const router = express.Router();
const CartController = require('../controllers/cartController');

module.exports = (pool) => {
  const controller = new CartController(pool);

  // GET user cart with items
  router.get('/:userId', (req, res) => controller.getCart(req, res));

  // POST add item to cart
  router.post('/:userId/items', (req, res) => controller.addItem(req, res));

  // PUT update cart item quantity
  router.put('/items/:itemId', (req, res) => controller.updateItem(req, res));

  // DELETE remove item from cart
  router.delete('/items/:itemId', (req, res) => controller.removeItem(req, res));

  // DELETE clear all cart items
  router.delete('/:userId/clear', (req, res) => controller.clearCart(req, res));

  return router;
};
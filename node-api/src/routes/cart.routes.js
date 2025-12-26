const express = require('express');
const router = express.Router();
const controller = require('../controllers/cart.controller');

// GET cart items for user
router.get('/:userId', controller.getCartItems);

// POST add item to cart
router.post('/item', controller.addItem);

// PUT update item quantity
router.put('/item/:id', controller.updateItem);

// DELETE remove item from cart
router.delete('/item/:id', controller.removeItem);

// DELETE clear all cart items for user
router.delete('/:userId/clear', controller.clearCart);

module.exports = router;
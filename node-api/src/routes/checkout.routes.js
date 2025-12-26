const express = require('express');
const router = express.Router();
const controller = require('../controllers/checkout.controller');

// POST create order
router.post('/', controller.createOrder);

// GET order by ID
router.get('/:orderId', controller.getOrderById);

// GET orders by user ID
router.get('/user/:userId', controller.getOrdersByUserId);

// PUT update order status
router.put('/:orderId/status', controller.updateOrderStatus);

// POST cancel order
router.post('/:orderId/cancel', controller.cancelOrder);

module.exports = router;
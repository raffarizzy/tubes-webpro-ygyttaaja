const express = require("express");
const router = express.Router();
const controller = require("../controllers/history.controller");

// GET all orders for a user
router.get("/:userId", controller.getUserOrders);

// GET order detail by ID
router.get("/order/:orderId", controller.getOrderById);

// POST cancel order
router.post("/cancel/:orderId", controller.cancelOrder);

router.post("/", controller.createOrder);

module.exports = router;

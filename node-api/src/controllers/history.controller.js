const service = require('../services/history.service');
const response = require('../utils/response');

/**
 * GET /api/history/:userId
 * Get all orders for a user
 */
exports.getUserOrders = async (req, res) => {
  try {
    const { userId } = req.params;

    const orders = await service.getUserOrders(userId);

    return response.success(res, orders, 'Berhasil mengambil riwayat pesanan');
  } catch (error) {
    console.error('Error get user orders:', error);
    return response.error(res, 'Gagal mengambil riwayat pesanan', 500, error.message);
  }
};

/**
 * GET /api/history/order/:orderId
 * Get order detail by ID
 */
exports.getOrderById = async (req, res) => {
  try {
    const { orderId } = req.params;

    const order = await service.getOrderById(orderId);

    return response.success(res, order, 'Berhasil mengambil detail order');
  } catch (error) {
    console.error('Error get order detail:', error);

    if (error.message.includes('tidak ditemukan')) {
      return response.notFound(res, error.message);
    }

    return response.error(res, 'Gagal mengambil detail order', 500, error.message);
  }
};

/**
 * POST /api/history/cancel/:orderId
 * Cancel order and restore stock
 */
exports.cancelOrder = async (req, res) => {
  try {
    const { orderId } = req.params;

    const result = await service.cancelOrder(orderId);

    return response.success(res, result, 'Order berhasil dibatalkan');
  } catch (error) {
    console.error('Error cancel order:', error);

    if (error.message.includes('tidak ditemukan')) {
      return response.notFound(res, error.message);
    }

    return response.error(res, 'Gagal membatalkan order', 500, error.message);
  }
};
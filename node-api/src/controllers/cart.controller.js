const service = require('../services/cart.service');
const response = require('../utils/response');

/**
 * GET /api/cart/:userId
 * Get all cart items for a user
 */
exports.getCartItems = async (req, res) => {
  try {
    const { userId } = req.params;
    const items = await service.getCartItems(userId);

    return response.success(res, items, 'Berhasil mengambil data keranjang');
  } catch (error) {
    console.error('Error get cart items:', error);
    return response.error(res, 'Gagal mengambil data keranjang', 500, error.message);
  }
};

/**
 * POST /api/cart/item
 * Add item to cart
 */
exports.addItem = async (req, res) => {
  try {
    const { user_id, product_id, jumlah } = req.body;

    if (!user_id || !product_id || !jumlah) {
      return response.validationError(res, 'Field user_id, product_id, dan jumlah wajib diisi');
    }

    const result = await service.addItem(user_id, product_id, jumlah);

    return response.success(res, result, 'Produk berhasil ditambahkan ke keranjang', 201);
  } catch (error) {
    console.error('Error add to cart:', error);

    if (error.message.includes('Stok')) {
      return response.error(res, error.message, 400);
    }

    return response.error(res, 'Gagal menambahkan ke keranjang', 500, error.message);
  }
};

/**
 * PUT /api/cart/item/:id
 * Update cart item quantity
 */
exports.updateItem = async (req, res) => {
  try {
    const { id } = req.params;
    const { jumlah } = req.body;

    if (!jumlah || jumlah < 1) {
      return response.validationError(res, 'Jumlah minimal 1');
    }

    const result = await service.updateItem(id, jumlah);

    return response.success(res, result, 'Jumlah berhasil diperbarui');
  } catch (error) {
    console.error('Error update cart item:', error);

    if (error.message.includes('tidak ditemukan')) {
      return response.notFound(res, error.message);
    }

    if (error.message.includes('Stok')) {
      return response.error(res, error.message, 400);
    }

    return response.error(res, 'Gagal memperbarui jumlah', 500, error.message);
  }
};

/**
 * DELETE /api/cart/item/:id
 * Remove item from cart
 */
exports.removeItem = async (req, res) => {
  try {
    const { id } = req.params;
    const result = await service.removeItem(id);

    return response.success(res, result, 'Item berhasil dihapus dari keranjang');
  } catch (error) {
    console.error('Error remove cart item:', error);

    if (error.message.includes('tidak ditemukan')) {
      return response.notFound(res, error.message);
    }

    return response.error(res, 'Gagal menghapus item', 500, error.message);
  }
};

/**
 * DELETE /api/cart/:userId/clear
 * Clear all cart items for user
 */
exports.clearCart = async (req, res) => {
  try {
    const { userId } = req.params;
    const result = await service.clearCart(userId);

    return response.success(res, result, 'Keranjang berhasil dikosongkan');
  } catch (error) {
    console.error('Error clear cart:', error);
    return response.error(res, 'Gagal mengosongkan keranjang', 500, error.message);
  }
};
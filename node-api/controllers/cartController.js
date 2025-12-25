// =====================================================
// Cart Controller - Business Logic
// =====================================================
const CartRepository = require('../repositories/cartRepository');
const { success, error, notFound } = require('../utils/response');

class CartController {
  constructor(pool) {
    this.repository = new CartRepository(pool);
  }

  /**
   * GET user cart with items
   */
  async getCart(req, res) {
    try {
      const { userId } = req.params;

      // Get or create cart
      const cart = await this.repository.getUserCart(userId);

      // Get cart items
      const items = await this.repository.getCartItems(cart.id);

      // Get summary
      const summary = await this.repository.getCartSummary(cart.id);

      return success(res, {
        cart: {
          id: cart.id,
          user_id: cart.user_id,
          status: cart.status
        },
        items,
        summary
      }, 'Cart retrieved successfully');
    } catch (err) {
      console.error('Error fetching cart:', err);
      return error(res, 'Failed to fetch cart', 500, err.message);
    }
  }

  /**
   * POST add item to cart
   */
  async addItem(req, res) {
    try {
      const { userId } = req.params;
      const { product_id, jumlah, harga } = req.body;

      // Validation
      if (!product_id || !jumlah || !harga) {
        return error(res, 'Missing required fields: product_id, jumlah, harga', 400);
      }

      // Get or create cart
      const cart = await this.repository.getUserCart(userId);

      // Add item
      const itemId = await this.repository.addItem(cart.id, product_id, jumlah, harga);

      // Get updated summary
      const summary = await this.repository.getCartSummary(cart.id);

      return success(res, {
        item_id: itemId,
        cart_id: cart.id,
        summary
      }, 'Item added to cart successfully', 201);
    } catch (err) {
      console.error('Error adding item to cart:', err);
      return error(res, 'Failed to add item to cart', 500, err.message);
    }
  }

  /**
   * PUT update cart item quantity
   */
  async updateItem(req, res) {
    try {
      const { itemId } = req.params;
      const { jumlah } = req.body;

      if (!jumlah || jumlah < 1) {
        return error(res, 'Invalid quantity', 400);
      }

      const updated = await this.repository.updateItemQuantity(itemId, jumlah);

      if (!updated) {
        return notFound(res, 'Cart item not found');
      }

      return success(res, { id: itemId, jumlah }, 'Cart item updated successfully');
    } catch (err) {
      console.error('Error updating cart item:', err);
      return error(res, 'Failed to update cart item', 500, err.message);
    }
  }

  /**
   * DELETE remove item from cart
   */
  async removeItem(req, res) {
    try {
      const { itemId } = req.params;

      const deleted = await this.repository.removeItem(itemId);

      if (!deleted) {
        return notFound(res, 'Cart item not found');
      }

      return success(res, null, 'Item removed from cart successfully');
    } catch (err) {
      console.error('Error removing cart item:', err);
      return error(res, 'Failed to remove cart item', 500, err.message);
    }
  }

  /**
   * DELETE clear all cart items
   */
  async clearCart(req, res) {
    try {
      const { userId } = req.params;

      // Get cart
      const cart = await this.repository.getUserCart(userId);

      // Clear items
      const deletedCount = await this.repository.clearCart(cart.id);

      return success(res, {
        deleted_items: deletedCount
      }, 'Cart cleared successfully');
    } catch (err) {
      console.error('Error clearing cart:', err);
      return error(res, 'Failed to clear cart', 500, err.message);
    }
  }
}

module.exports = CartController;
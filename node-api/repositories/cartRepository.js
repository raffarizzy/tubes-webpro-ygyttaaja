// =====================================================
// Cart Repository - Database Layer
// =====================================================

class CartRepository {
  constructor(pool) {
    this.pool = pool;
  }

  /**
   * Get or create user cart
   */
  async getUserCart(userId) {
    // Check if user already has active cart
    const [carts] = await this.pool.query(
      'SELECT * FROM keranjangs WHERE user_id = ? AND status = ?',
      [userId, 'active']
    );

    if (carts.length > 0) {
      return carts[0];
    }

    // Create new cart if doesn't exist
    const [result] = await this.pool.query(
      'INSERT INTO keranjangs (user_id, status, created_at, updated_at) VALUES (?, ?, NOW(), NOW())',
      [userId, 'active']
    );

    return {
      id: result.insertId,
      user_id: userId,
      status: 'active'
    };
  }

  /**
   * Get cart items with product details
   */
  async getCartItems(cartId) {
    const [items] = await this.pool.query(`
      SELECT
        bk.*,
        p.nama as product_nama,
        p.deskripsi,
        p.imagePath,
        p.image_path,
        p.toko_id
      FROM barang_keranjangs bk
      JOIN products p ON bk.product_id = p.id
      WHERE bk.keranjang_id = ?
      ORDER BY bk.created_at DESC
    `, [cartId]);

    return items;
  }

  /**
   * Add item to cart
   */
  async addItem(cartId, productId, jumlah, harga) {
    try {
      // Check if item already exists
      const [existing] = await this.pool.query(
        'SELECT * FROM barang_keranjangs WHERE keranjang_id = ? AND product_id = ?',
        [cartId, productId]
      );

      if (existing.length > 0) {
        // Update quantity
        const newJumlah = existing[0].jumlah + jumlah;
        await this.pool.query(
          'UPDATE barang_keranjangs SET jumlah = ?, updated_at = NOW() WHERE id = ?',
          [newJumlah, existing[0].id]
        );
        return existing[0].id;
      } else {
        // Insert new item
        const [result] = await this.pool.query(
          `INSERT INTO barang_keranjangs (keranjang_id, product_id, jumlah, harga, created_at, updated_at)
           VALUES (?, ?, ?, ?, NOW(), NOW())`,
          [cartId, productId, jumlah, harga]
        );
        return result.insertId;
      }
    } catch (err) {
      throw err;
    }
  }

  /**
   * Update cart item quantity
   */
  async updateItemQuantity(itemId, jumlah) {
    const [result] = await this.pool.query(
      'UPDATE barang_keranjangs SET jumlah = ?, updated_at = NOW() WHERE id = ?',
      [jumlah, itemId]
    );
    return result.affectedRows > 0;
  }

  /**
   * Remove item from cart
   */
  async removeItem(itemId) {
    const [result] = await this.pool.query(
      'DELETE FROM barang_keranjangs WHERE id = ?',
      [itemId]
    );
    return result.affectedRows > 0;
  }

  /**
   * Clear all cart items
   */
  async clearCart(cartId) {
    const [result] = await this.pool.query(
      'DELETE FROM barang_keranjangs WHERE keranjang_id = ?',
      [cartId]
    );
    return result.affectedRows;
  }

  /**
   * Get cart summary
   */
  async getCartSummary(cartId) {
    const [summary] = await this.pool.query(`
      SELECT
        COUNT(*) as total_items,
        SUM(jumlah) as total_quantity,
        SUM(jumlah * harga) as total_price
      FROM barang_keranjangs
      WHERE keranjang_id = ?
    `, [cartId]);

    return summary[0];
  }
}

module.exports = CartRepository;
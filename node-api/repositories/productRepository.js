// =====================================================
// Product Repository - Database Layer
// =====================================================

class ProductRepository {
  constructor(pool) {
    this.pool = pool;
  }

  /**
   * Get all products
   */
  async getAll() {
    const [products] = await this.pool.query(`
      SELECT
        p.*,
        p.toko_id as tokoId,
        p.category_id as categoryId
      FROM products p
      ORDER BY p.created_at DESC
    `);
    return products;
  }

  /**
   * Get product by ID
   */
  async getById(id) {
    const [products] = await this.pool.query(`
      SELECT
        p.*,
        p.toko_id as tokoId,
        p.category_id as categoryId
      FROM products p
      WHERE p.id = ?
    `, [id]);

    return products[0] || null;
  }

  /**
   * Get product ratings
   */
  async getRatings(productId) {
    const [ratings] = await this.pool.query(
      'SELECT * FROM ratings WHERE product_id = ? ORDER BY created_at DESC',
      [productId]
    );
    return ratings;
  }

  /**
   * Create new product
   */
  async create(productData) {
    const [result] = await this.pool.query(
      `INSERT INTO products (nama, harga, deskripsi, toko_id, category_id, stok, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())`,
      [
        productData.nama,
        productData.harga,
        productData.deskripsi,
        productData.toko_id,
        productData.category_id,
        productData.stok || 0
      ]
    );
    return result.insertId;
  }

  /**
   * Update product
   */
  async update(id, productData) {
    const [result] = await this.pool.query(
      `UPDATE products
       SET nama = ?, harga = ?, deskripsi = ?, toko_id = ?, category_id = ?, stok = ?, updated_at = NOW()
       WHERE id = ?`,
      [
        productData.nama,
        productData.harga,
        productData.deskripsi,
        productData.toko_id,
        productData.category_id,
        productData.stok,
        id
      ]
    );
    return result.affectedRows > 0;
  }

  /**
   * Delete product
   */
  async delete(id) {
    const [result] = await this.pool.query('DELETE FROM products WHERE id = ?', [id]);
    return result.affectedRows > 0;
  }
}

module.exports = ProductRepository;
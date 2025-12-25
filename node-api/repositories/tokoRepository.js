// =====================================================
// Toko Repository - Database Layer
// =====================================================

class TokoRepository {
  constructor(pool) {
    this.pool = pool;
  }

  /**
   * Get all tokos
   */
  async getAll() {
    const [tokos] = await this.pool.query('SELECT * FROM tokos ORDER BY created_at DESC');
    return tokos;
  }

  /**
   * Get toko by ID
   */
  async getById(id) {
    const [tokos] = await this.pool.query('SELECT * FROM tokos WHERE id = ?', [id]);
    return tokos[0] || null;
  }

  /**
   * Create new toko
   */
  async create(tokoData) {
    const [result] = await this.pool.query(
      `INSERT INTO tokos (nama_toko, user_id, deskripsi, lokasi, created_at, updated_at)
       VALUES (?, ?, ?, ?, NOW(), NOW())`,
      [tokoData.nama_toko, tokoData.user_id, tokoData.deskripsi, tokoData.lokasi]
    );
    return result.insertId;
  }

  /**
   * Update toko
   */
  async update(id, tokoData) {
    const [result] = await this.pool.query(
      `UPDATE tokos
       SET nama_toko = ?, deskripsi = ?, lokasi = ?, updated_at = NOW()
       WHERE id = ?`,
      [tokoData.nama_toko, tokoData.deskripsi, tokoData.lokasi, id]
    );
    return result.affectedRows > 0;
  }

  /**
   * Delete toko
   */
  async delete(id) {
    const [result] = await this.pool.query('DELETE FROM tokos WHERE id = ?', [id]);
    return result.affectedRows > 0;
  }
}

module.exports = TokoRepository;
// =====================================================
// User Repository - Database Layer
// =====================================================

class UserRepository {
  constructor(pool) {
    this.pool = pool;
  }

  /**
   * Get all users
   */
  async getAll() {
    const [users] = await this.pool.query(
      'SELECT id, name, email, phone, birthDate, gender, created_at FROM users ORDER BY created_at DESC'
    );
    return users;
  }

  /**
   * Get user by ID
   */
  async getById(id) {
    const [users] = await this.pool.query(
      'SELECT id, name, email, phone, birthDate, gender, created_at FROM users WHERE id = ?',
      [id]
    );
    return users[0] || null;
  }

  /**
   * Get user by email
   */
  async getByEmail(email) {
    const [users] = await this.pool.query('SELECT * FROM users WHERE email = ?', [email]);
    return users[0] || null;
  }

  /**
   * Create new user
   */
  async create(userData) {
    const [result] = await this.pool.query(
      `INSERT INTO users (name, email, password, phone, birthDate, gender, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())`,
      [
        userData.name,
        userData.email,
        userData.password,
        userData.phone || null,
        userData.birthDate || null,
        userData.gender || null
      ]
    );
    return result.insertId;
  }

  /**
   * Update user
   */
  async update(id, userData) {
    const fields = [];
    const values = [];

    if (userData.name) {
      fields.push('name = ?');
      values.push(userData.name);
    }
    if (userData.email) {
      fields.push('email = ?');
      values.push(userData.email);
    }
    if (userData.phone !== undefined) {
      fields.push('phone = ?');
      values.push(userData.phone);
    }

    fields.push('updated_at = NOW()');
    values.push(id);

    const [result] = await this.pool.query(
      `UPDATE users SET ${fields.join(', ')} WHERE id = ?`,
      values
    );
    return result.affectedRows > 0;
  }

  /**
   * Delete user
   */
  async delete(id) {
    const [result] = await this.pool.query('DELETE FROM users WHERE id = ?', [id]);
    return result.affectedRows > 0;
  }
}

module.exports = UserRepository;
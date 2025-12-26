const db = require('../config/db');

/**
 * Cek apakah user sudah punya toko
 */
exports.checkUserHasToko = async (userId) => {
  const [rows] = await db.query(
    'SELECT id, nama_toko FROM tokos WHERE user_id = ? LIMIT 1',
    [userId]
  );
  
  return rows.length > 0 ? rows[0] : null;
};

/**
 * Get toko by user ID (untuk owner)
 */
exports.getByUserId = async (userId) => {
  const [rows] = await db.query(
    `SELECT 
      id,
      user_id,
      nama_toko,
      deskripsi_toko,
      lokasi,
      logo_path,
      created_at,
      updated_at
     FROM tokos 
     WHERE user_id = ?`,
    [userId]
  );
  
  return rows[0];
};

/**
 * Get toko by ID (untuk public view)
 */
exports.getById = async (tokoId) => {
  const [rows] = await db.query(
    `SELECT 
      id,
      user_id,
      nama_toko,
      deskripsi_toko,
      lokasi,
      logo_path,
      created_at
     FROM tokos 
     WHERE id = ?`,
    [tokoId]
  );
  
  return rows[0];
};

/**
 * Create toko baru
 */
exports.create = async (data) => {
  // Cek dulu apakah user sudah punya toko
  const existing = await exports.checkUserHasToko(data.user_id);
  
  if (existing) {
    throw new Error('User sudah memiliki toko');
  }

  const [result] = await db.query(
    `INSERT INTO tokos (user_id, nama_toko, deskripsi_toko, lokasi, logo_path)
     VALUES (?, ?, ?, ?, ?)`,
    [
      data.user_id,
      data.nama_toko,
      data.deskripsi_toko || null,
      data.lokasi || null,
      data.logo_path || null
    ]
  );

  return {
    id: result.insertId,
    message: 'Toko berhasil dibuat'
  };
};

/**
 * Update toko
 */
exports.update = async (tokoId, userId, data) => {
  // Cek apakah toko milik user ini
  const [rows] = await db.query(
    'SELECT * FROM tokos WHERE id = ? AND user_id = ?',
    [tokoId, userId]
  );

  if (!rows.length) {
    throw new Error('Toko tidak ditemukan atau bukan milik Anda');
  }

  const old = rows[0];

  const updated = {
    nama_toko: data.nama_toko ?? old.nama_toko,
    deskripsi_toko: data.deskripsi_toko ?? old.deskripsi_toko,
    lokasi: data.lokasi ?? old.lokasi,
    logo_path: data.logo_path ?? old.logo_path,
  };

  await db.query(
    `UPDATE tokos
     SET nama_toko=?, deskripsi_toko=?, lokasi=?, logo_path=?
     WHERE id=? AND user_id=?`,
    [
      updated.nama_toko,
      updated.deskripsi_toko,
      updated.lokasi,
      updated.logo_path,
      tokoId,
      userId
    ]
  );

  return { message: 'Toko berhasil diperbarui' };
};

/**
 * Delete toko
 */
exports.delete = async (tokoId, userId) => {
  // Cek apakah toko milik user ini
  const [rows] = await db.query(
    'SELECT id FROM tokos WHERE id = ? AND user_id = ?',
    [tokoId, userId]
  );

  if (!rows.length) {
    throw new Error('Toko tidak ditemukan atau bukan milik Anda');
  }

  // Hapus semua produk toko dulu (cascade)
  await db.query('DELETE FROM products WHERE toko_id = ?', [tokoId]);
  
  // Baru hapus toko
  await db.query('DELETE FROM tokos WHERE id = ?', [tokoId]);

  return { message: 'Toko dan semua produknya berhasil dihapus' };
};
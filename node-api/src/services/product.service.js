const db = require('../config/db');

/**
 * Get product detail by ID dengan join ke toko dan category
 */
exports.getById = async (id) => {
  const [rows] = await db.query(
    `SELECT
        p.id,
        p.nama,
        p.deskripsi,
        p.harga,
        p.diskon,
        p.stok,
        p.imagePath,
        p.toko_id,
        t.nama_toko,
        t.lokasi AS toko_lokasi,
        t.logo_path AS toko_logo,
        p.category_id,
        c.judulKategori AS category_nama,
        p.created_at,
        p.updated_at
     FROM products p
     LEFT JOIN tokos t ON p.toko_id = t.id
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.id = ?`,
    [id]
  );

  return rows[0];
};

/**
 * Get all products dengan pagination
 */
exports.getAll = async (limit = 20, offset = 0) => {
  const [rows] = await db.query(
    `SELECT
        p.id,
        p.nama,
        p.deskripsi,
        p.harga,
        p.diskon,
        p.stok,
        p.imagePath,
        p.toko_id,
        t.nama_toko,
        p.category_id,
        c.judulKategori AS category_nama
     FROM products p
     LEFT JOIN tokos t ON p.toko_id = t.id
     LEFT JOIN categories c ON p.category_id = c.id
     ORDER BY p.created_at DESC
     LIMIT ? OFFSET ?`,
    [parseInt(limit), parseInt(offset)]
  );

  return rows;
};

/**
 * Get products by toko ID
 */
exports.getByToko = async (tokoId) => {
  const [rows] = await db.query(
    `SELECT
        p.id,
        p.nama,
        p.deskripsi,
        p.harga,
        p.diskon,
        p.stok,
        p.imagePath,
        p.category_id,
        c.judulKategori AS category_nama,
        p.created_at
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.toko_id = ?
     ORDER BY p.created_at DESC`,
    [tokoId]
  );

  return rows;
};

/**
 * Get products by category ID
 */
exports.getByCategory = async (categoryId) => {
  const [rows] = await db.query(
    `SELECT
        p.id,
        p.nama,
        p.deskripsi,
        p.harga,
        p.diskon,
        p.stok,
        p.imagePath,
        p.toko_id,
        t.nama_toko,
        p.created_at
     FROM products p
     LEFT JOIN tokos t ON p.toko_id = t.id
     WHERE p.category_id = ?
     ORDER BY p.created_at DESC`,
    [categoryId]
  );

  return rows;
};

/**
 * Create new product
 */
exports.create = async (data) => {
  const [result] = await db.query(
    `INSERT INTO products (toko_id, category_id, nama, deskripsi, harga, diskon, stok, imagePath)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
    [
      data.toko_id,
      data.category_id,
      data.nama,
      data.deskripsi,
      data.harga,
      data.diskon || null,
      data.stok,
      data.imagePath
    ]
  );

  return {
    id: result.insertId,
    message: 'Produk berhasil dibuat'
  };
};

/**
 * Update product
 */
exports.update = async (id, data) => {
  // Cek dulu apakah product ada
  const [rows] = await db.query(
    'SELECT * FROM products WHERE id = ?',
    [id]
  );

  if (!rows.length) {
    throw new Error('Produk tidak ditemukan');
  }

  const old = rows[0];

  // Update hanya field yang dikirim, sisanya pakai nilai lama
  const updated = {
    toko_id: data.toko_id ?? old.toko_id,
    category_id: data.category_id ?? old.category_id,
    nama: data.nama ?? old.nama,
    deskripsi: data.deskripsi ?? old.deskripsi,
    harga: data.harga ?? old.harga,
    diskon: data.diskon ?? old.diskon,
    stok: data.stok ?? old.stok,
    imagePath: data.imagePath ?? old.imagePath,
  };

  await db.query(
    `UPDATE products
     SET toko_id=?, category_id=?, nama=?, deskripsi=?, harga=?, diskon=?, stok=?, imagePath=?
     WHERE id=?`,
    [
      updated.toko_id,
      updated.category_id,
      updated.nama,
      updated.deskripsi,
      updated.harga,
      updated.diskon,
      updated.stok,
      updated.imagePath,
      id
    ]
  );

  return { message: 'Produk berhasil diperbarui' };
};

/**
 * Delete product
 */
exports.delete = async (id) => {
  // Cek dulu apakah product ada
  const [rows] = await db.query(
    'SELECT id FROM products WHERE id = ?',
    [id]
  );

  if (!rows.length) {
    throw new Error('Produk tidak ditemukan');
  }

  await db.query('DELETE FROM products WHERE id = ?', [id]);

  return { message: 'Produk berhasil dihapus' };
};
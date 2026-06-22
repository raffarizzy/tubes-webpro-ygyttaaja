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
        p.berat,
        p.imagePath,
        p.toko_id,
        t.nama_toko,
        t.lokasi AS toko_lokasi,
        t.logo_path AS toko_logo,
        u.is_verified_seller,
        p.category_id,
        c.judulKategori AS category_nama,
        p.created_at,
        p.updated_at
     FROM products p
     LEFT JOIN tokos t ON p.toko_id = t.id
     LEFT JOIN users u ON t.user_id = u.id
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.id = ? AND p.deleted_at IS NULL`,
    [id]
  );

  return rows[0];
};

/**
 * Get all products dengan pagination
 */
exports.getAll = async (limit = 20, offset = 0, search = '') => {
  let sql = `SELECT
        p.id,
        p.nama,
        p.deskripsi,
        p.harga,
        p.diskon,
        p.stok,
        p.berat,
        p.imagePath,
        p.toko_id,
        t.nama_toko,
        t.lokasi AS toko_lokasi,
        t.logo_path AS toko_logo,
        u.is_verified_seller,
        p.category_id,
        c.judulKategori AS category_nama
     FROM products p
     LEFT JOIN tokos t ON p.toko_id = t.id
     LEFT JOIN users u ON t.user_id = u.id
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.deleted_at IS NULL`;

  const params = [];
  if (search) {
    sql += ` AND p.nama LIKE ?`;
    params.push(`%${search}%`);
  }

  sql += ` ORDER BY p.created_at DESC
     LIMIT ? OFFSET ?`;
  params.push(parseInt(limit), parseInt(offset));

  const [rows] = await db.query(sql, params);

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
        p.berat,
        p.imagePath,
        p.category_id,
        c.judulKategori AS category_nama,
        u.is_verified_seller,
        p.created_at
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     LEFT JOIN tokos t ON p.toko_id = t.id
     LEFT JOIN users u ON t.user_id = u.id
     WHERE p.toko_id = ? AND p.deleted_at IS NULL
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
        p.berat,
        p.imagePath,
        p.toko_id,
        t.nama_toko,
        u.is_verified_seller,
        p.created_at
     FROM products p
     LEFT JOIN tokos t ON p.toko_id = t.id
     LEFT JOIN users u ON t.user_id = u.id
     WHERE p.category_id = ? AND p.deleted_at IS NULL
     ORDER BY p.created_at DESC`,
    [categoryId]
  );

  return rows;
};

/**
 * Create new product
 */
exports.create = async (data) => {
  try {
    // Pastikan nilai numerik benar dan berikan default jika kosong
    const toko_id = parseInt(data.toko_id);
    const category_id = parseInt(data.category_id);
    const harga = parseInt(data.harga);
    const stok = parseInt(data.stok);
    const berat = data.berat ? parseInt(data.berat) : 1000;
    const diskon = data.diskon ? parseInt(data.diskon) : 0;
    
    const [result] = await db.query(
      `INSERT INTO products (toko_id, category_id, nama, deskripsi, harga, diskon, stok, berat, imagePath, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())`,
      [
        toko_id,
        category_id,
        data.nama,
        data.deskripsi || '',
        harga,
        diskon,
        stok,
        berat,
        data.imagePath
      ]
    );

    return {
      id: result.insertId,
      message: 'Produk berhasil dibuat'
    };
  } catch (error) {
    console.error('Error in product.service.js (create):', error);
    throw error;
  }
};

/**
 * Update product
 */
exports.update = async (id, data) => {
  // Cek dulu apakah product ada
  const [rows] = await db.query(
    'SELECT * FROM products WHERE id = ? AND deleted_at IS NULL',
    [id]
  );

  if (!rows.length) {
    throw new Error('Produk tidak ditemukan');
  }

  const old = rows[0];

  // Update hanya field yang dikirim, sisanya pakai nilai lama
  const updated = {
    toko_id: data.toko_id !== undefined ? parseInt(data.toko_id) : old.toko_id,
    category_id: data.category_id !== undefined ? parseInt(data.category_id) : old.category_id,
    nama: data.nama ?? old.nama,
    deskripsi: data.deskripsi ?? old.deskripsi,
    harga: data.harga !== undefined ? parseInt(data.harga) : old.harga,
    diskon: data.diskon !== undefined ? parseInt(data.diskon) : old.diskon,
    stok: data.stok !== undefined ? parseInt(data.stok) : old.stok,
    berat: (data.berat !== undefined && data.berat !== '') ? parseInt(data.berat) : old.berat,
    imagePath: data.imagePath ?? old.imagePath,
  };

  await db.query(
    `UPDATE products
     SET toko_id=?, category_id=?, nama=?, deskripsi=?, harga=?, diskon=?, stok=?, berat=?, imagePath=?, updated_at=NOW()
     WHERE id=?`,
    [
      updated.toko_id,
      updated.category_id,
      updated.nama,
      updated.deskripsi,
      updated.harga,
      updated.diskon,
      updated.stok,
      updated.berat,
      updated.imagePath,
      id
    ]
  );

  return { message: 'Produk berhasil diperbarui' };
};

/**
 * Delete product (Soft Delete)
 */
exports.delete = async (id) => {
  // Cek dulu apakah product ada
  const [rows] = await db.query(
    'SELECT id FROM products WHERE id = ? AND deleted_at IS NULL',
    [id]
  );

  if (!rows.length) {
    throw new Error('Produk tidak ditemukan');
  }

  // 1. Hapus dari semua keranjang (Hard Delete dari keranjang karena produk sudah tidak tersedia)
  await db.query('DELETE FROM barang_keranjangs WHERE product_id = ?', [id]);

  // 2. Hapus rating terkait (Opsional, tapi biasanya ikut hilang jika produk tidak ada)
  await db.query('DELETE FROM ratings WHERE product_id = ?', [id]);

  // 3. Soft Delete produk: Set deleted_at saja agar Order History aman
  await db.query('UPDATE products SET deleted_at = NOW() WHERE id = ?', [id]);

  return { message: 'Produk berhasil dihapus' };
};

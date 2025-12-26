const db = require('../config/db');

/**
 * Get cart items for a user with product details
 */
exports.getCartItems = async (userId) => {
  const query = `
    SELECT
      bk.id,
      bk.keranjang_id,
      bk.product_id,
      bk.jumlah,
      bk.harga,
      p.nama as product_nama,
      p.deskripsi as product_deskripsi,
      p.imagePath as product_imagePath,
      p.stok as product_stok,
      p.harga as product_harga_current,
      t.nama_toko,
      t.lokasi as toko_lokasi
    FROM barang_keranjangs bk
    INNER JOIN keranjangs k ON bk.keranjang_id = k.id
    INNER JOIN products p ON bk.product_id = p.id
    INNER JOIN tokos t ON p.toko_id = t.id
    WHERE k.user_id = ?
    ORDER BY bk.created_at DESC
  `;

  const [rows] = await db.query(query, [userId]);

  // Transform data to match frontend structure
  return rows.map(row => ({
    id: row.id,
    keranjang_id: row.keranjang_id,
    product_id: row.product_id,
    jumlah: row.jumlah,
    harga: row.harga,
    product: {
      id: row.product_id,
      nama: row.product_nama,
      deskripsi: row.product_deskripsi,
      imagePath: row.product_imagePath,
      stok: row.product_stok,
      harga: row.product_harga_current,
      nama_toko: row.nama_toko,
      toko_lokasi: row.toko_lokasi
    }
  }));
};

/**
 * Add item to cart or update quantity if exists
 */
exports.addItem = async (userId, productId, jumlah) => {
  // First, check product stock
  const [productRows] = await db.query(
    'SELECT stok, harga FROM products WHERE id = ?',
    [productId]
  );

  if (productRows.length === 0) {
    throw new Error('Produk tidak ditemukan');
  }

  const product = productRows[0];

  // Get or create cart for user
  let [cartRows] = await db.query(
    'SELECT id FROM keranjangs WHERE user_id = ?',
    [userId]
  );

  let cartId;
  if (cartRows.length === 0) {
    // Create new cart
    const [result] = await db.query(
      'INSERT INTO keranjangs (user_id) VALUES (?)',
      [userId]
    );
    cartId = result.insertId;
  } else {
    cartId = cartRows[0].id;
  }

  // Check if item already exists in cart
  const [existingItems] = await db.query(
    'SELECT id, jumlah FROM barang_keranjangs WHERE keranjang_id = ? AND product_id = ?',
    [cartId, productId]
  );

  if (existingItems.length > 0) {
    // Update existing item
    const newJumlah = existingItems[0].jumlah + jumlah;

    if (newJumlah > product.stok) {
      throw new Error(`Stok tidak mencukupi. Stok tersedia: ${product.stok}`);
    }

    await db.query(
      'UPDATE barang_keranjangs SET jumlah = ? WHERE id = ?',
      [newJumlah, existingItems[0].id]
    );

    return { id: existingItems[0].id, jumlah: newJumlah };
  } else {
    // Add new item
    if (jumlah > product.stok) {
      throw new Error(`Stok tidak mencukupi. Stok tersedia: ${product.stok}`);
    }

    const [result] = await db.query(
      'INSERT INTO barang_keranjangs (keranjang_id, product_id, jumlah, harga) VALUES (?, ?, ?, ?)',
      [cartId, productId, jumlah, product.harga]
    );

    return { id: result.insertId, jumlah };
  }
};

/**
 * Update cart item quantity
 */
exports.updateItem = async (itemId, jumlah) => {
  // Get item and check stock
  const [items] = await db.query(
    `SELECT bk.id, bk.product_id, p.stok
     FROM barang_keranjangs bk
     INNER JOIN products p ON bk.product_id = p.id
     WHERE bk.id = ?`,
    [itemId]
  );

  if (items.length === 0) {
    throw new Error('Item tidak ditemukan');
  }

  const item = items[0];

  if (jumlah > item.stok) {
    throw new Error(`Stok tidak mencukupi. Stok tersedia: ${item.stok}`);
  }

  await db.query(
    'UPDATE barang_keranjangs SET jumlah = ? WHERE id = ?',
    [jumlah, itemId]
  );

  return { id: itemId, jumlah };
};

/**
 * Remove item from cart
 */
exports.removeItem = async (itemId) => {
  const [result] = await db.query(
    'DELETE FROM barang_keranjangs WHERE id = ?',
    [itemId]
  );

  if (result.affectedRows === 0) {
    throw new Error('Item tidak ditemukan');
  }

  return { id: itemId };
};

/**
 * Clear all cart items for user
 */
exports.clearCart = async (userId) => {
  // Get cart id
  const [cartRows] = await db.query(
    'SELECT id FROM keranjangs WHERE user_id = ?',
    [userId]
  );

  if (cartRows.length === 0) {
    return { cleared: 0 };
  }

  const cartId = cartRows[0].id;

  // Delete all items
  const [result] = await db.query(
    'DELETE FROM barang_keranjangs WHERE keranjang_id = ?',
    [cartId]
  );

  return { cleared: result.affectedRows };
};
const db = require('../config/db');

/**
 * Get all orders for a user with items and address details
 */
exports.getUserOrders = async (userId) => {
  const [orderRows] = await db.query(
    `SELECT
      o.id,
      o.user_id,
      o.alamat_id,
      o.total_harga,
      o.status,
      o.created_at,
      o.updated_at,
      a.nama_penerima,
      a.alamat,
      a.nomor_penerima
    FROM orders o
    LEFT JOIN alamats a ON o.alamat_id = a.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC`,
    [userId]
  );

  // Get items for each order
  const orders = [];
  for (const order of orderRows) {
    const [itemRows] = await db.query(
      `SELECT
        oi.id,
        oi.product_id,
        oi.nama_produk,
        oi.harga,
        oi.qty,
        oi.subtotal,
        p.imagePath as product_image,
        p.deskripsi as product_deskripsi
      FROM order_items oi
      LEFT JOIN products p ON oi.product_id = p.id
      WHERE oi.order_id = ?`,
      [order.id]
    );

    orders.push({
      id: order.id,
      user_id: order.user_id,
      alamat_id: order.alamat_id,
      total_harga: order.total_harga,
      status: order.status,
      created_at: order.created_at,
      updated_at: order.updated_at,
      items: itemRows.map(item => ({
        id: item.id,
        product_id: item.product_id,
        nama_produk: item.nama_produk,
        harga: item.harga,
        qty: item.qty,
        subtotal: item.subtotal,
        product: {
          image_path: item.product_image,
          deskripsi: item.product_deskripsi
        }
      })),
      alamat: order.alamat_id ? {
        nama_penerima: order.nama_penerima,
        alamat: order.alamat,
        nomor_penerima: order.nomor_penerima
      } : null
    });
  }

  return orders;
};

/**
 * Get order by ID with details
 */
exports.getOrderById = async (orderId) => {
  const [orderRows] = await db.query(
    `SELECT
      o.id,
      o.user_id,
      o.alamat_id,
      o.total_harga,
      o.status,
      o.created_at,
      o.updated_at,
      a.nama_penerima,
      a.alamat,
      a.nomor_penerima
    FROM orders o
    LEFT JOIN alamats a ON o.alamat_id = a.id
    WHERE o.id = ?`,
    [orderId]
  );

  if (orderRows.length === 0) {
    throw new Error('Order tidak ditemukan');
  }

  const order = orderRows[0];

  // Get order items
  const [itemRows] = await db.query(
    `SELECT
      oi.id,
      oi.product_id,
      oi.nama_produk,
      oi.harga,
      oi.qty,
      oi.subtotal,
      p.imagePath as product_image,
      p.deskripsi as product_deskripsi
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?`,
    [orderId]
  );

  return {
    id: order.id,
    user_id: order.user_id,
    alamat_id: order.alamat_id,
    total_harga: order.total_harga,
    status: order.status,
    created_at: order.created_at,
    updated_at: order.updated_at,
    items: itemRows.map(item => ({
      id: item.id,
      product_id: item.product_id,
      nama_produk: item.nama_produk,
      harga: item.harga,
      qty: item.qty,
      subtotal: item.subtotal,
      product: {
        image_path: item.product_image,
        deskripsi: item.product_deskripsi
      }
    })),
    alamat: order.alamat_id ? {
      nama_penerima: order.nama_penerima,
      alamat: order.alamat,
      nomor_penerima: order.nomor_penerima
    } : null
  };
};

/**
 * Cancel order and restore stock
 */
exports.cancelOrder = async (orderId) => {
  const connection = await db.getConnection();

  try {
    await connection.beginTransaction();

    // Get order items
    const [items] = await connection.query(
      'SELECT product_id, qty FROM order_items WHERE order_id = ?',
      [orderId]
    );

    if (items.length === 0) {
      throw new Error('Order tidak ditemukan');
    }

    // Restore stock
    for (const item of items) {
      await connection.query(
        'UPDATE products SET stok = stok + ? WHERE id = ?',
        [item.qty, item.product_id]
      );
    }

    // Update order status
    const [result] = await connection.query(
      'UPDATE orders SET status = ? WHERE id = ?',
      ['cancelled', orderId]
    );

    if (result.affectedRows === 0) {
      throw new Error('Order tidak ditemukan');
    }

    await connection.commit();

    return { id: orderId, status: 'cancelled' };

  } catch (error) {
    await connection.rollback();
    throw error;
  } finally {
    connection.release();
  }
};
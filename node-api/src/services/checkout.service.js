const db = require("../config/db");
const { getWIBTimestamp } = require("../utils/dateHelper");

/**
 * Create new order with items
 */
exports.createOrder = async (userId, alamatId, items) => {
    const connection = await db.getConnection();

    try {
        await connection.query("SET time_zone = '+07:00'");
        await connection.beginTransaction();

        // Calculate total price
        let totalHarga = 0;
        const orderItemsData = [];

        // Get product details and calculate total
        for (const item of items) {
            const [productRows] = await connection.query(
                "SELECT id, nama, harga, stok FROM products WHERE id = ?",
                [item.product_id]
            );

            if (productRows.length === 0) {
                throw new Error(
                    `Produk dengan ID ${item.product_id} tidak ditemukan`
                );
            }

            const product = productRows[0];

            // Check stock availability
            if (product.stok < item.jumlah) {
                throw new Error(
                    `Stok tidak mencukupi untuk produk ${product.nama}. Stok tersedia: ${product.stok}`
                );
            }

            const subtotal = product.harga * item.jumlah;
            totalHarga += subtotal;

            orderItemsData.push({
                product_id: product.id,
                nama_produk: product.nama,
                harga: product.harga,
                qty: item.jumlah,
                subtotal: subtotal,
            });
        }

        // Create order with WIB timestamp
        const now = getWIBTimestamp();
        console.log("Creating order at WIB:", now);

        const [orderResult] = await connection.query(
            "INSERT INTO orders (user_id, alamat_id, total_harga, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)",
            [userId, alamatId, totalHarga, "pending", now, now]
        );

        const orderId = orderResult.insertId;

        // Insert order items
        for (const itemData of orderItemsData) {
            await connection.query(
                "INSERT INTO order_items (order_id, product_id, nama_produk, harga, qty, subtotal) VALUES (?, ?, ?, ?, ?, ?)",
                [
                    orderId,
                    itemData.product_id,
                    itemData.nama_produk,
                    itemData.harga,
                    itemData.qty,
                    itemData.subtotal,
                ]
            );

            // Update product stock
            await connection.query(
                "UPDATE products SET stok = stok - ? WHERE id = ?",
                [itemData.qty, itemData.product_id]
            );
        }

        // Clear user's cart after successful order
        const [cartRows] = await connection.query(
            "SELECT id FROM keranjangs WHERE user_id = ?",
            [userId]
        );

        if (cartRows.length > 0) {
            const cartId = cartRows[0].id;
            await connection.query(
                "DELETE FROM barang_keranjangs WHERE keranjang_id = ?",
                [cartId]
            );

            console.log(`Cart cleared for user ${userId}`);
        }

        await connection.commit();

        // Get created order with details
        const order = await this.getOrderById(orderId);

        return order;
    } catch (error) {
        await connection.rollback();
        throw error;
    } finally {
        connection.release();
    }
};

/**
 * Get order by ID with items and address details
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
        throw new Error("Order tidak ditemukan");
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
        items: itemRows.map((item) => ({
            id: item.id,
            product_id: item.product_id,
            nama_produk: item.nama_produk,
            harga: item.harga,
            qty: item.qty,
            subtotal: item.subtotal,
            product: {
                image_path: item.product_image,
                deskripsi: item.product_deskripsi,
            },
        })),
        alamat: order.alamat_id
            ? {
                  nama_penerima: order.nama_penerima,
                  alamat: order.alamat,
                  nomor_penerima: order.nomor_penerima,
              }
            : null,
    };
};

/**
 * Get all orders for a user
 */
exports.getOrdersByUserId = async (userId) => {
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
            items: itemRows.map((item) => ({
                id: item.id,
                product_id: item.product_id,
                nama_produk: item.nama_produk,
                harga: item.harga,
                qty: item.qty,
                subtotal: item.subtotal,
                product: {
                    image_path: item.product_image,
                    deskripsi: item.product_deskripsi,
                },
            })),
            alamat: order.alamat_id
                ? {
                      nama_penerima: order.nama_penerima,
                      alamat: order.alamat,
                      nomor_penerima: order.nomor_penerima,
                  }
                : null,
        });
    }

    return orders;
};

/**
 * Update order status
 */
exports.updateOrderStatus = async (orderId, status) => {
    const connection = await db.getConnection();

    try {
        // SET TIMEZONE
        await connection.query("SET time_zone = '+07:00'");

        const validStatuses = ["pending", "paid", "cancelled"];

        if (!validStatuses.includes(status)) {
            throw new Error(
                "Status tidak valid. Gunakan: pending, paid, atau cancelled"
            );
        }

        // Check if order exists
        const [orderRows] = await connection.query(
            "SELECT id, status FROM orders WHERE id = ?",
            [orderId]
        );

        if (orderRows.length === 0) {
            throw new Error("Order tidak ditemukan");
        }

        console.log(
            `Updating order ${orderId} status from ${orderRows[0].status} to ${status}`
        );

        const [result] = await connection.query(
            "UPDATE orders SET status = ?, updated_at = ? WHERE id = ?",
            [status, getWIBTimestamp(), orderId]
        );

        if (result.affectedRows === 0) {
            throw new Error("Gagal memperbarui status order");
        }

        return { id: orderId, status };
    } finally {
        connection.release();
    }
};

/**
 * Update order alamat
 */
exports.updateOrderAlamat = async (orderId, alamatId) => {
    const connection = await db.getConnection();

    try {
        await connection.query("SET time_zone = '+07:00'");

        // Check if alamat exists
        const [alamatRows] = await connection.query(
            "SELECT id FROM alamats WHERE id = ?",
            [alamatId]
        );

        if (alamatRows.length === 0) {
            throw new Error("Alamat tidak ditemukan");
        }

        // Check if order exists
        const [orderRows] = await connection.query(
            "SELECT id FROM orders WHERE id = ?",
            [orderId]
        );

        if (orderRows.length === 0) {
            throw new Error("Order tidak ditemukan");
        }

        console.log(`Updating order ${orderId} alamat to ${alamatId}`);

        const [result] = await connection.query(
            "UPDATE orders SET alamat_id = ?, updated_at = ? WHERE id = ?",
            [alamatId, getWIBTimestamp(), orderId]
        );

        if (result.affectedRows === 0) {
            throw new Error("Gagal memperbarui alamat order");
        }

        return { id: orderId, alamat_id: alamatId };
    } finally {
        connection.release();
    }
};

/**
 * Cancel order and restore stock
 */
exports.cancelOrder = async (orderId) => {
    const connection = await db.getConnection();

    try {
        await connection.query("SET time_zone = '+07:00'");
        await connection.beginTransaction();

        // Get order to check status
        const [orders] = await connection.query(
            "SELECT status FROM orders WHERE id = ?",
            [orderId]
        );

        if (orders.length === 0) {
            throw new Error("Order tidak ditemukan");
        }

        if (orders[0].status !== "pending") {
            throw new Error(
                "Hanya pesanan dengan status pending yang bisa dibatalkan"
            );
        }

        // Get order items
        const [items] = await connection.query(
            "SELECT product_id, qty FROM order_items WHERE order_id = ?",
            [orderId]
        );

        if (items.length === 0) {
            throw new Error("Order items tidak ditemukan");
        }

        // Restore stock
        for (const item of items) {
            await connection.query(
                "UPDATE products SET stok = stok + ? WHERE id = ?",
                [item.qty, item.product_id]
            );
        }

        // Update order status with WIB timestamp
        await connection.query(
            "UPDATE orders SET status = ?, updated_at = ? WHERE id = ?",
            ["cancelled", getWIBTimestamp(), orderId]
        );

        await connection.commit();

        console.log(`Order ${orderId} cancelled successfully`);

        return { id: orderId, status: "cancelled" };
    } catch (error) {
        await connection.rollback();
        throw error;
    } finally {
        connection.release();
    }
};

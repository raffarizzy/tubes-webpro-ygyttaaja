const service = require("../services/checkout.service");
const response = require("../utils/response");

/**
 * POST /api/orders
 * Create new order
 */
exports.createOrder = async (req, res) => {
    try {
        const { user_id, alamat_id, items } = req.body;

        // Validation
        if (!user_id) {
            return response.validationError(res, "Field user_id wajib diisi");
        }

        if (!alamat_id) {
            return response.validationError(res, "Field alamat_id wajib diisi");
        }

        if (!items || !Array.isArray(items) || items.length === 0) {
            return response.validationError(
                res,
                "Field items wajib diisi dan harus berupa array dengan minimal 1 item"
            );
        }

        // Validate each item
        for (const item of items) {
            if (!item.product_id || !item.jumlah) {
                return response.validationError(
                    res,
                    "Setiap item harus memiliki product_id dan jumlah"
                );
            }

            if (item.jumlah < 1) {
                return response.validationError(res, "Jumlah minimal 1");
            }
        }

        console.log("Creating order:", {
            user_id,
            alamat_id,
            items_count: items.length,
        });

        const order = await service.createOrder(user_id, alamat_id, items);

        return response.success(res, { order }, "Order berhasil dibuat", 201);
    } catch (error) {
        console.error("Error create order:", error);

        if (error.message.includes("tidak ditemukan")) {
            return response.notFound(res, error.message);
        }

        if (error.message.includes("Stok")) {
            return response.error(res, error.message, 400);
        }

        return response.error(res, "Gagal membuat order", 500, error.message);
    }
};

/**
 * GET /api/orders/:orderId
 * Get order details by ID
 */
exports.getOrderById = async (req, res) => {
    try {
        const { orderId } = req.params;

        const order = await service.getOrderById(orderId);

        return response.success(res, order, "Berhasil mengambil detail order");
    } catch (error) {
        console.error("Error get order:", error);

        if (error.message.includes("tidak ditemukan")) {
            return response.notFound(res, error.message);
        }

        return response.error(
            res,
            "Gagal mengambil detail order",
            500,
            error.message
        );
    }
};

/**
 * GET /api/orders/user/:userId
 * Get all orders for a user
 */
exports.getOrdersByUserId = async (req, res) => {
    try {
        const { userId } = req.params;

        const orders = await service.getOrdersByUserId(userId);

        return response.success(res, orders, "Berhasil mengambil daftar order");
    } catch (error) {
        console.error("Error get user orders:", error);
        return response.error(
            res,
            "Gagal mengambil daftar order",
            500,
            error.message
        );
    }
};

/**
 * PUT /api/orders/:orderId/status
 * Update order status
 */
exports.updateOrderStatus = async (req, res) => {
    try {
        const { orderId } = req.params;
        const { status } = req.body;

        if (!status) {
            return response.validationError(res, "Field status wajib diisi");
        }

        console.log(`Updating order ${orderId} status to ${status}`);

        const result = await service.updateOrderStatus(orderId, status);

        return response.success(
            res,
            result,
            "Status order berhasil diperbarui"
        );
    } catch (error) {
        console.error("Error update order status:", error);

        if (error.message.includes("tidak ditemukan")) {
            return response.notFound(res, error.message);
        }

        if (error.message.includes("tidak valid")) {
            return response.validationError(res, error.message);
        }

        return response.error(
            res,
            "Gagal memperbarui status order",
            500,
            error.message
        );
    }
};

/**
 * PUT /api/orders/:orderId/alamat
 * Update order alamat
 */
exports.updateOrderAlamat = async (req, res) => {
    try {
        const { orderId } = req.params;
        const { alamat_id } = req.body;

        if (!alamat_id) {
            return response.validationError(res, "Field alamat_id wajib diisi");
        }

        console.log(`Updating order ${orderId} alamat to ${alamat_id}`);

        const result = await service.updateOrderAlamat(orderId, alamat_id);

        return response.success(
            res,
            result,
            "Alamat order berhasil diperbarui"
        );
    } catch (error) {
        console.error("Error update order alamat:", error);

        if (error.message.includes("tidak ditemukan")) {
            return response.notFound(res, error.message);
        }

        return response.error(
            res,
            "Gagal memperbarui alamat order",
            500,
            error.message
        );
    }
};

/**
 * POST /api/orders/:orderId/cancel
 * Cancel order and restore stock
 */
exports.cancelOrder = async (req, res) => {
    try {
        const { orderId } = req.params;

        console.log(`Cancelling order ${orderId}`);

        const result = await service.cancelOrder(orderId);

        return response.success(res, result, "Order berhasil dibatalkan");
    } catch (error) {
        console.error("Error cancel order:", error);

        if (error.message.includes("tidak ditemukan")) {
            return response.notFound(res, error.message);
        }

        if (error.message.includes("Hanya pesanan")) {
            return response.error(res, error.message, 400);
        }

        return response.error(
            res,
            "Gagal membatalkan order",
            500,
            error.message
        );
    }
};

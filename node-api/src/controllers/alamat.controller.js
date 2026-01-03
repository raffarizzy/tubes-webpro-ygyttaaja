const service = require("../services/alamat.service");
const response = require("../utils/response");

/**
 * GET /api/alamat/:userId
 * Get all alamat for a user
 */
exports.getUserAlamat = async (req, res) => {
    try {
        const { userId } = req.params;

        if (!userId) {
            return response.validationError(res, "User ID wajib diisi");
        }

        const alamats = await service.getUserAlamat(userId);

        return response.success(
            res,
            alamats,
            "Berhasil mengambil daftar alamat"
        );
    } catch (error) {
        console.error("Error get user alamat:", error);
        return response.error(
            res,
            "Gagal mengambil daftar alamat",
            500,
            error.message
        );
    }
};

/**
 * GET /api/alamat/detail/:alamatId
 * Get alamat by ID
 */
exports.getAlamatById = async (req, res) => {
    try {
        const { alamatId } = req.params;

        if (!alamatId) {
            return response.validationError(res, "Alamat ID wajib diisi");
        }

        const alamat = await service.getAlamatById(alamatId);

        return response.success(
            res,
            alamat,
            "Berhasil mengambil detail alamat"
        );
    } catch (error) {
        console.error("Error get alamat:", error);

        if (error.message.includes("tidak ditemukan")) {
            return response.notFound(res, error.message);
        }

        return response.error(
            res,
            "Gagal mengambil detail alamat",
            500,
            error.message
        );
    }
};

/**
 * POST /api/alamat
 * Create new alamat
 */
exports.createAlamat = async (req, res) => {
    try {
        const { user_id, alamat, nama_penerima, nomor_penerima, is_default } =
            req.body;

        // Validation
        if (!user_id) {
            return response.validationError(res, "User ID wajib diisi");
        }

        if (!alamat) {
            return response.validationError(res, "Alamat wajib diisi");
        }

        if (!nama_penerima) {
            return response.validationError(res, "Nama penerima wajib diisi");
        }

        if (!nomor_penerima) {
            return response.validationError(res, "Nomor penerima wajib diisi");
        }

        // Check alamat limit (max 3 per user)
        const count = await service.countUserAlamat(user_id);
        if (count >= 3) {
            return response.error(res, "Maksimal 3 alamat per pengguna", 400);
        }

        const newAlamat = await service.createAlamat(user_id, {
            alamat,
            nama_penerima,
            nomor_penerima,
            is_default: is_default || false,
        });

        return response.success(
            res,
            newAlamat,
            "Alamat berhasil ditambahkan",
            201
        );
    } catch (error) {
        console.error("Error create alamat:", error);

        return response.error(
            res,
            "Gagal menambahkan alamat",
            500,
            error.message
        );
    }
};

/**
 * PUT /api/alamat/:alamatId
 * Update alamat
 */
exports.updateAlamat = async (req, res) => {
    try {
        const { alamatId } = req.params;
        const { user_id, alamat, nama_penerima, nomor_penerima, is_default } =
            req.body;

        // Validation
        if (!alamatId) {
            return response.validationError(res, "Alamat ID wajib diisi");
        }

        if (!user_id) {
            return response.validationError(res, "User ID wajib diisi");
        }

        if (!alamat) {
            return response.validationError(res, "Alamat wajib diisi");
        }

        if (!nama_penerima) {
            return response.validationError(res, "Nama penerima wajib diisi");
        }

        if (!nomor_penerima) {
            return response.validationError(res, "Nomor penerima wajib diisi");
        }

        const updatedAlamat = await service.updateAlamat(alamatId, user_id, {
            alamat,
            nama_penerima,
            nomor_penerima,
            is_default: is_default !== undefined ? is_default : false,
        });

        return response.success(
            res,
            updatedAlamat,
            "Alamat berhasil diperbarui"
        );
    } catch (error) {
        console.error("Error update alamat:", error);

        if (error.message.includes("tidak ditemukan")) {
            return response.notFound(res, error.message);
        }

        if (error.message.includes("tidak memiliki akses")) {
            return response.error(res, error.message, 403);
        }

        return response.error(
            res,
            "Gagal memperbarui alamat",
            500,
            error.message
        );
    }
};

/**
 * DELETE /api/alamat/:alamatId
 * Delete alamat
 */
exports.deleteAlamat = async (req, res) => {
    try {
        const { alamatId } = req.params;
        const { user_id } = req.body;

        // Validation
        if (!alamatId) {
            return response.validationError(res, "Alamat ID wajib diisi");
        }

        if (!user_id) {
            return response.validationError(res, "User ID wajib diisi");
        }

        const result = await service.deleteAlamat(alamatId, user_id);

        return response.success(res, result, "Alamat berhasil dihapus");
    } catch (error) {
        console.error("Error delete alamat:", error);

        if (error.message.includes("tidak ditemukan")) {
            return response.notFound(res, error.message);
        }

        if (error.message.includes("tidak memiliki akses")) {
            return response.error(res, error.message, 403);
        }

        return response.error(
            res,
            "Gagal menghapus alamat",
            500,
            error.message
        );
    }
};

/**
 * PUT /api/alamat/:alamatId/set-default
 * Set alamat as default
 */
exports.setDefaultAlamat = async (req, res) => {
    try {
        const { alamatId } = req.params;
        const { user_id } = req.body;

        // Validation
        if (!alamatId) {
            return response.validationError(res, "Alamat ID wajib diisi");
        }

        if (!user_id) {
            return response.validationError(res, "User ID wajib diisi");
        }

        const alamat = await service.setDefaultAlamat(alamatId, user_id);

        return response.success(
            res,
            alamat,
            "Alamat berhasil diatur sebagai default"
        );
    } catch (error) {
        console.error("Error set default alamat:", error);

        if (error.message.includes("tidak ditemukan")) {
            return response.notFound(res, error.message);
        }

        if (error.message.includes("tidak memiliki akses")) {
            return response.error(res, error.message, 403);
        }

        return response.error(
            res,
            "Gagal mengatur alamat sebagai default",
            500,
            error.message
        );
    }
};

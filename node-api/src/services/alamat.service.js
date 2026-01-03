const db = require("../config/db");
const { getWIBTimestamp } = require("../utils/dateHelper");

/**
 * Get all alamat for a user
 */
exports.getUserAlamat = async (userId) => {
    const [rows] = await db.query(
        `SELECT 
            id,
            user_id,
            alamat,
            nama_penerima,
            nomor_penerima,
            is_default,
            created_at,
            updated_at
        FROM alamats 
        WHERE user_id = ? 
        ORDER BY is_default DESC, created_at DESC`,
        [userId]
    );

    console.log(`Retrieved ${rows.length} alamat for user ${userId}`);

    return rows;
};

/**
 * Get alamat by ID
 */
exports.getAlamatById = async (alamatId) => {
    const [rows] = await db.query(
        `SELECT 
            id,
            user_id,
            alamat,
            nama_penerima,
            nomor_penerima,
            is_default,
            created_at,
            updated_at
        FROM alamats 
        WHERE id = ?`,
        [alamatId]
    );

    if (rows.length === 0) {
        throw new Error("Alamat tidak ditemukan");
    }

    return rows[0];
};

/**
 * Create new alamat
 */
exports.createAlamat = async (userId, data) => {
    const connection = await db.getConnection();

    try {
        await connection.query("SET time_zone = '+07:00'");
        await connection.beginTransaction();

        const { alamat, nama_penerima, nomor_penerima, is_default } = data;

        // Validate required fields
        if (!alamat || !nama_penerima || !nomor_penerima) {
            throw new Error(
                "Field alamat, nama_penerima, dan nomor_penerima wajib diisi"
            );
        }

        // Convert is_default to boolean (0 or 1)
        const isDefaultValue =
            is_default === true || is_default === 1 || is_default === "1"
                ? 1
                : 0;

        console.log("Creating alamat:", {
            user_id: userId,
            is_default: isDefaultValue,
            nama_penerima,
        });

        // If this alamat is set as default, unset other defaults for this user
        if (isDefaultValue === 1) {
            await connection.query(
                "UPDATE alamats SET is_default = 0 WHERE user_id = ?",
                [userId]
            );
            console.log(`Unset default alamat for user ${userId}`);
        }

        const now = getWIBTimestamp();

        // Insert new alamat
        const [result] = await connection.query(
            `INSERT INTO alamats 
            (user_id, alamat, nama_penerima, nomor_penerima, is_default, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)`,
            [
                userId,
                alamat,
                nama_penerima,
                nomor_penerima,
                isDefaultValue,
                now,
                now,
            ]
        );

        const alamatId = result.insertId;

        await connection.commit();

        console.log(`Alamat created successfully with ID: ${alamatId}`);

        // Return created alamat
        return await this.getAlamatById(alamatId);
    } catch (error) {
        await connection.rollback();
        throw error;
    } finally {
        connection.release();
    }
};

/**
 * Update alamat
 */
exports.updateAlamat = async (alamatId, userId, data) => {
    const connection = await db.getConnection();

    try {
        await connection.query("SET time_zone = '+07:00'");
        await connection.beginTransaction();

        // Check if alamat exists and belongs to user
        const [existing] = await connection.query(
            "SELECT id, user_id FROM alamats WHERE id = ?",
            [alamatId]
        );

        if (existing.length === 0) {
            throw new Error("Alamat tidak ditemukan");
        }

        if (existing[0].user_id !== userId) {
            throw new Error("Anda tidak memiliki akses ke alamat ini");
        }

        const { alamat, nama_penerima, nomor_penerima, is_default } = data;

        // Validate required fields
        if (!alamat || !nama_penerima || !nomor_penerima) {
            throw new Error(
                "Field alamat, nama_penerima, dan nomor_penerima wajib diisi"
            );
        }

        // Convert is_default to boolean (0 or 1)
        const isDefaultValue =
            is_default === true || is_default === 1 || is_default === "1"
                ? 1
                : 0;

        console.log("Updating alamat:", {
            id: alamatId,
            user_id: userId,
            is_default: isDefaultValue,
        });

        // If this alamat is set as default, unset other defaults for this user
        if (isDefaultValue === 1) {
            await connection.query(
                "UPDATE alamats SET is_default = 0 WHERE user_id = ? AND id != ?",
                [userId, alamatId]
            );
            console.log(
                `Unset default alamat for user ${userId} except ${alamatId}`
            );
        }

        const now = getWIBTimestamp();

        // Update alamat
        const [result] = await connection.query(
            `UPDATE alamats 
            SET alamat = ?, 
                nama_penerima = ?, 
                nomor_penerima = ?, 
                is_default = ?,
                updated_at = ?
            WHERE id = ?`,
            [
                alamat,
                nama_penerima,
                nomor_penerima,
                isDefaultValue,
                now,
                alamatId,
            ]
        );

        if (result.affectedRows === 0) {
            throw new Error("Gagal memperbarui alamat");
        }

        await connection.commit();

        console.log(`Alamat ${alamatId} updated successfully`);

        // Return updated alamat
        return await this.getAlamatById(alamatId);
    } catch (error) {
        await connection.rollback();
        throw error;
    } finally {
        connection.release();
    }
};

/**
 * Delete alamat
 */
exports.deleteAlamat = async (alamatId, userId) => {
    const connection = await db.getConnection();

    try {
        await connection.beginTransaction();

        // Check if alamat exists and belongs to user
        const [existing] = await connection.query(
            "SELECT id, user_id, is_default FROM alamats WHERE id = ?",
            [alamatId]
        );

        if (existing.length === 0) {
            throw new Error("Alamat tidak ditemukan");
        }

        if (existing[0].user_id !== userId) {
            throw new Error("Anda tidak memiliki akses ke alamat ini");
        }

        const wasDefault = existing[0].is_default === 1;

        console.log("Deleting alamat:", {
            id: alamatId,
            user_id: userId,
            was_default: wasDefault,
        });

        // Delete alamat
        const [result] = await connection.query(
            "DELETE FROM alamats WHERE id = ?",
            [alamatId]
        );

        if (result.affectedRows === 0) {
            throw new Error("Gagal menghapus alamat");
        }

        // If deleted alamat was default, set the first remaining alamat as default
        if (wasDefault) {
            const [remainingAlamat] = await connection.query(
                "SELECT id FROM alamats WHERE user_id = ? ORDER BY created_at ASC LIMIT 1",
                [userId]
            );

            if (remainingAlamat.length > 0) {
                await connection.query(
                    "UPDATE alamats SET is_default = 1 WHERE id = ?",
                    [remainingAlamat[0].id]
                );
                console.log(
                    `Set alamat ${remainingAlamat[0].id} as new default`
                );
            }
        }

        await connection.commit();

        console.log(`Alamat ${alamatId} deleted successfully`);

        return { id: alamatId, deleted: true };
    } catch (error) {
        await connection.rollback();
        throw error;
    } finally {
        connection.release();
    }
};

/**
 * Set alamat as default
 */
exports.setDefaultAlamat = async (alamatId, userId) => {
    const connection = await db.getConnection();

    try {
        await connection.query("SET time_zone = '+07:00'");
        await connection.beginTransaction();

        // Check if alamat exists and belongs to user
        const [existing] = await connection.query(
            "SELECT id, user_id FROM alamats WHERE id = ?",
            [alamatId]
        );

        if (existing.length === 0) {
            throw new Error("Alamat tidak ditemukan");
        }

        if (existing[0].user_id !== userId) {
            throw new Error("Anda tidak memiliki akses ke alamat ini");
        }

        console.log(`Setting alamat ${alamatId} as default for user ${userId}`);

        // Unset all defaults for this user
        await connection.query(
            "UPDATE alamats SET is_default = 0 WHERE user_id = ?",
            [userId]
        );

        // Set this alamat as default
        const [result] = await connection.query(
            "UPDATE alamats SET is_default = 1, updated_at = ? WHERE id = ?",
            [getWIBTimestamp(), alamatId]
        );

        if (result.affectedRows === 0) {
            throw new Error("Gagal mengatur alamat sebagai default");
        }

        await connection.commit();

        console.log(`Alamat ${alamatId} set as default successfully`);

        return await this.getAlamatById(alamatId);
    } catch (error) {
        await connection.rollback();
        throw error;
    } finally {
        connection.release();
    }
};

/**
 * Count user's alamat
 */
exports.countUserAlamat = async (userId) => {
    const [rows] = await db.query(
        "SELECT COUNT(*) as count FROM alamats WHERE user_id = ?",
        [userId]
    );

    return rows[0].count;
};

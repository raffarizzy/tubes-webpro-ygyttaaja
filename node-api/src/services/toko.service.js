const db = require('../config/db');

class TokoService {
    async getToko(user_id) {
        const [rows] = await db.execute('SELECT * FROM tokos WHERE user_id = ?', [user_id]);
        const toko = rows[0];

        if (!toko) {
            return { 
                hasToko : false,
                data : null
            };
        }

        return {
            hasToko : true,
            data : toko
        };
    }

    async createToko(user_id, fd) {
        const [result] = await db.execute(`
            INSERT INTO tokos(
                user_id,
                nama_toko,
                deskripsi_toko,
                lokasi,
                logo_path,
                created_at,
                updated_at
            ) VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        `, [user_id, fd.nama_toko, fd.deskripsi_toko, fd.lokasi, fd.logo]);

        return {
            success: true,
            id: result.insertId
        }
    }
}

module.exports = new TokoService();
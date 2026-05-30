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
}

module.exports = new TokoService();
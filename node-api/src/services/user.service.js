const db = require("../config/db");

exports.getUser = async(userId) => {
    const [rows] = await db.query(
        `SELECT * FROM users WHERE id = ?`,
        [userId]
    );

    return rows;
}
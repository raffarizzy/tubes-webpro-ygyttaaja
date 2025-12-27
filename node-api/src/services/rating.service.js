const db = require('../config/db');

exports.getByUser = async (userId) => {
  try {
    console.log('📌 Getting ratings for user:', userId);

    const [rows] = await db.query(
      `
      SELECT 
        r.id,
        r.product_id,
        r.rating,
        r.review,
        r.created_at,
        p.nama AS product_name,
        p.imagePath AS image_path
      FROM ratings r
      JOIN products p ON p.id = r.product_id
      WHERE r.user_id = ?
      ORDER BY r.created_at DESC
      `,
      [userId]
    );

    console.log('✅ Ratings found:', rows.length);
    return rows;

  } catch (error) {
    console.error('❌ ERROR DETAIL:', error);
    console.error('SQL Message:', error.sqlMessage);
    console.error('SQL Code:', error.code);
    throw error;
  }
};

exports.create = async ({ user_id, product_id, rating, review }) => {
  try {
    const [exist] = await db.query(
      `SELECT id FROM ratings WHERE user_id = ? AND product_id = ?`,
      [user_id, product_id]
    );

    if (exist.length > 0) {
      throw new Error('Kamu sudah pernah merating produk ini');
    }

    const [result] = await db.query(
      `INSERT INTO ratings (user_id, product_id, rating, review, created_at)
       VALUES (?, ?, ?, ?, NOW())`,
      [user_id, product_id, rating, review]
    );

    return { id: result.insertId, user_id, product_id, rating, review };
  } catch (error) {
    console.error('❌ Error in create:', error);
    throw error;
  }
};

exports.remove = async (id, userId) => {
  try {
    const [result] = await db.query(
      `DELETE FROM ratings WHERE id = ? AND user_id = ?`,
      [id, userId]
    );

    if (result.affectedRows === 0) {
      throw new Error('Rating tidak ditemukan atau bukan milik Anda');
    }

    return true;
  } catch (error) {
    console.error('❌ Error in remove:', error);
    throw error;
  }
};
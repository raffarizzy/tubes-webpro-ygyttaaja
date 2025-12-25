const db = require('../config/db');

exports.getById = async (id) => {
  const [rows] = await db.query(
    `SELECT 
        id, 
        name, 
        email, 
        phone, 
        birthDate, 
        gender, 
        pfpPath
     FROM users 
     WHERE id = ?`,
    [id]
  );

  return rows[0];
};

exports.update = async (id, data) => {
  const {
    name,
    phone,
    birthDate,
    gender,
    pfpPath
  } = data;

  await db.query(
    `UPDATE users 
     SET 
        name = ?, 
        phone = ?, 
        birthDate = ?, 
        gender = ?, 
        pfpPath = ?
     WHERE id = ?`,
    [name, phone, birthDate, gender, pfpPath, id]
  );

  return { message: 'Profil berhasil diperbarui' };
};
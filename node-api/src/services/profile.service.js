const db = require('../config/db');

exports.getById = async (id) => {
  const [rows] = await db.query(
    `SELECT 
        id, 
        name, 
        email, 
        phone, 
        DATE_FORMAT(birthDate, '%Y-%m-%d') AS birthDate, 
        gender, 
        pfpPath
     FROM users 
     WHERE id = ?`,
    [id]
  );

  return rows[0];
};

exports.update = async (id, data) => {
  const [rows] = await db.query(
    'SELECT name, phone, birthDate, gender, pfpPath FROM users WHERE id = ?',
    [id]
  );

  if (!rows.length) {
    throw new Error('User tidak ditemukan');
  }

  const old = rows[0];

  const updated = {
    name: data.name ?? old.name,
    phone: data.phone ?? old.phone,
    birthDate: data.birthDate ?? old.birthDate,
    gender: data.gender ?? old.gender,
    pfpPath: data.pfpPath ?? old.pfpPath,
  };

  await db.query(
    `UPDATE users SET name=?, phone=?, birthDate=?, gender=?, pfpPath=? WHERE id=?`,
    [
      updated.name,
      updated.phone,
      updated.birthDate,
      updated.gender,
      updated.pfpPath,
      id
    ]
  );

  return { message: 'Profil berhasil diperbarui' };
};
const db = require('../config/db');

exports.getById = async (id) => {
  const [rows] = await db.execute(
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
  // 1. Cek apakah user ada
  const [rows] = await db.execute('SELECT id FROM users WHERE id = ?', [id]);
  if (!rows.length) {
    throw new Error('User tidak ditemukan');
  }

  // 2. Siapkan data (pastikan tidak ada string kosong untuk kolom DATE/NULL)
  const name = data.name || null;
  const email = data.email || null;
  const phone = data.phone || null;
  const gender = data.gender || null;
  const pfpPath = data.pfpPath || null;
  
  // Tangani birthDate secara khusus
  let birthDate = null;
  if (data.birthDate && data.birthDate.trim() !== '') {
    birthDate = data.birthDate;
  }

  const values = [name, email, phone, birthDate, gender, pfpPath, id];
  
  console.log('SQL Parameters:', values);

  await db.execute(
    `UPDATE users SET name=?, email=?, phone=?, birthDate=?, gender=?, pfpPath=? WHERE id=?`,
    values
  );

  return { message: 'Profil berhasil diperbarui' };
};
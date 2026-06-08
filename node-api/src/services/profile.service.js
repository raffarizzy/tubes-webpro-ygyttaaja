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
  // 1. Ambil data lama untuk dibandingkan/digabung
  const [rows] = await db.execute(
    'SELECT name, email, phone, birthDate, gender, pfpPath FROM users WHERE id = ?', 
    [id]
  );

  if (!rows.length) {
    throw new Error('User tidak ditemukan');
  }

  const old = rows[0];

  // Helper untuk membersihkan data dari FormData (yang sering jadi string "null" atau "undefined")
  const clean = (val, oldVal) => {
    if (val === undefined) return oldVal;
    if (val === null || val === '' || val === 'null' || val === 'undefined') return null;
    return val;
  };

  // 2. Siapkan data update
  const updated = {
    name: data.name !== undefined ? data.name : old.name,
    email: data.email !== undefined ? data.email : old.email,
    phone: clean(data.phone, old.phone),
    gender: clean(data.gender, old.gender),
    pfpPath: data.pfpPath !== undefined ? data.pfpPath : old.pfpPath,
  };
  
  // Tangani birthDate secara khusus
  let birthDate = clean(data.birthDate, old.birthDate);
  // Format birthDate jika itu objek Date dari DB
  if (birthDate instanceof Date) {
    birthDate = birthDate.toISOString().split('T')[0];
  }

  const values = [
    updated.name,
    updated.email,
    updated.phone,
    birthDate,
    updated.gender,
    updated.pfpPath,
    id
  ];
  
  console.log('SQL Parameters for Update:', values);

  await db.execute(
    `UPDATE users SET name=?, email=?, phone=?, birthDate=?, gender=?, pfpPath=? WHERE id=?`,
    values
  );

  return { 
    id,
    name: updated.name,
    email: updated.email,
    phone: updated.phone,
    birthDate: birthDate,
    gender: updated.gender,
    pfpPath: updated.pfpPath
  };
};
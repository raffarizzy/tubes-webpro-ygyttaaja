/**
 * Script untuk membersihkan data "ghost" di database MySQL
 * Jalankan dengan: node clear-ghost-data.js
 */

require('dotenv').config();
const db = require('./src/config/db');

async function clearGhostData() {
  try {
    console.log('=== CLEARING GHOST DATA ===');

    // 1. Cek database yang terkoneksi
    const [dbInfo] = await db.query('SELECT DATABASE() as current_db');
    console.log('Connected to database:', dbInfo[0].current_db);

    // 2. Tampilkan semua toko yang ada
    const [tokos] = await db.query('SELECT * FROM tokos');
    console.log('\nCurrent tokos in database:', tokos);
    console.log('Total tokos:', tokos.length);

    // 3. Tanya user apakah mau dihapus
    if (tokos.length > 0) {
      console.log('\n⚠️  WARNING: This will DELETE ALL tokos from the database!');
      console.log('To delete, run: node clear-ghost-data.js --force');

      if (process.argv.includes('--force')) {
        const [result] = await db.query('DELETE FROM tokos');
        console.log(`\n✅ Deleted ${result.affectedRows} toko(s)`);

        // Reset AUTO_INCREMENT
        await db.query('ALTER TABLE tokos AUTO_INCREMENT = 1');
        console.log('✅ Reset AUTO_INCREMENT counter');
      }
    } else {
      console.log('\n✅ No tokos found. Database is clean!');
    }

    process.exit(0);
  } catch (error) {
    console.error('Error:', error);
    process.exit(1);
  }
}

clearGhostData();
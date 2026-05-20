const db = require('../config/db');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');

class AuthService {
    async login(email, password) {
        // 1. Cari user berdasarkan email
        const [rows] = await db.execute('SELECT * FROM users WHERE email = ?', [email]);
        const user = rows[0];

        if (!user) {
            const error = new Error('Email atau password salah');
            error.statusCode = 401;
            throw error;
        }

        // 2. Bandingkan password
        const isMatch = await bcrypt.compare(password, user.password);
        if (!isMatch) {
            const error = new Error('Email atau password salah');
            error.statusCode = 401;
            throw error;
        }

        // 3. Buat Token JWT
        const token = jwt.sign(
            { id: user.id, email: user.email },
            process.env.JWT_SECRET,
            { expiresIn: '24h' }
        );

        return {
            token,
            user: { id: user.id, name: user.name, email: user.email }
        };
    }
}

module.exports = new AuthService();

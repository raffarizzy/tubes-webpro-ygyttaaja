const authService = require('../services/auth.service');
const authMiddleware = require('../middleware/auth.middleware');

exports.login = async (req, res) => {
    try {
        const { email, password } = req.body;
        
        // Memanggil service untuk memproses logic bisnis
        const result = await authService.login(email, password);

        res.cookie('token', result.token, {
            httpOnly : true,
            secure: false, // Set false untuk development via HTTP (bukan HTTPS)
            sameSite: 'lax',
            maxAge: 24 * 60 * 60 * 1000,
            path: '/'
        });

        res.json({
            success: true,
            user: result.user
        });
    } catch (error) {
        // Menangkap error dari service (misal statusCode 401)
        const statusCode = error.statusCode || 500;
        res.status(statusCode).json({ message: error.message });
    }
};

const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');

exports.register = async (req, res) => {
    try {
        const { name, email, password, phone } = req.body;

        // 1. Cek apakah user sudah ada
        const [existing] = await db.execute('SELECT id FROM users WHERE email = ?', [email]);
        if (existing.length > 0) {
            return res.status(400).json({ message: 'Email sudah terdaftar' });
        }

        // 2. Hash Password
        const hashedPassword = await bcrypt.hash(password, 10);

        // 3. Simpan ke Database
        const [result] = await db.execute(
            'INSERT INTO users (name, email, password, phone, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())',
            [name, email, hashedPassword, phone]
        );

        const userId = result.insertId;

        // 4. Buat Token (Otomatis Login)
        const token = jwt.sign(
            { id: userId, email: email },
            process.env.JWT_SECRET,
            { expiresIn: '24h' }
        );

        res.cookie('token', token, {
            httpOnly: true,
            secure: false,
            sameSite: 'lax',
            maxAge: 24 * 60 * 60 * 1000,
            path: '/'
        });

        res.json({
            success: true,
            user: { id: userId, name, email, phone, pfpPath: null }
        });
    } catch (error) {
        console.error('Registration error:', error);
        res.status(500).json({ message: 'Gagal melakukan registrasi: ' + error.message });
    }
};

exports.logout = async (req, res) => {
    res.clearCookie('token', {
        httpOnly : true,
        secure : process.env.NODE_ENV === 'production',
        sameSite: 'lax'
    });

    res.json({
        success: true,
        message: 'Berhasil logout'
    });
}

const db = require('../config/db');

exports.getMe = async (req, res) => {
    try {
        const [rows] = await db.execute(
            'SELECT id, name, email, phone, pfpPath FROM users WHERE id = ?',
            [req.user.id]
        );

        if (rows.length === 0) {
            return res.status(404).json({ message: 'User tidak ditemukan' });
        }

        res.json({
            success: true,
            user: rows[0]
        });
    } catch (e) {
        res.status(500).json({
            message: `Gagal mengambil data sesi : ${e.message}`
        })
    }
}
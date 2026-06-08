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
const authService = require('../services/auth.service');

exports.login = async (req, res) => {
    try {
        const { email, password } = req.body;
        
        // Memanggil service untuk memproses logic bisnis
        const result = await authService.login(email, password);

        res.cookie('token', result.token, {
            httpOnly : true,
            secure: process.env.NODE_ENV === 'production',
            sameSite: 'lax',
            maxAge: 24 * 60 * 60 * 1000
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

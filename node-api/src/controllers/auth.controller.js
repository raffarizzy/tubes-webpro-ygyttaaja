const authService = require('../services/auth.service');

exports.login = async (req, res) => {
    try {
        const { email, password } = req.body;
        
        // Memanggil service untuk memproses logic bisnis
        const result = await authService.login(email, password);

        res.json({
            success: true,
            token: result.token,
            user: result.user
        });
    } catch (error) {
        // Menangkap error dari service (misal statusCode 401)
        const statusCode = error.statusCode || 500;
        res.status(statusCode).json({ message: error.message });
    }
};

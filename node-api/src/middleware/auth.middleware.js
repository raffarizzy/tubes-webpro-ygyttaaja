const jwt = require('jsonwebtoken');

module.exports = async (req, res, next) => {
    const token = req.cookies.token;

    if (!token) {
        return res.response(401).json({message : 'Akses ditolak, token tidak ada'});
    }

    try {
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        req.user = decoded;
        next();
    } catch (e) {
        res.status(401).json({message: 'Token tidak valid'});
    }
};
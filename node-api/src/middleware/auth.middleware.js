const jwt = require('jsonwebtoken');

module.exports = async (req, res, next) => {
    const token = req.cookies.token;

    console.log('--- Auth Middleware Check ---');
    console.log('Origin:', req.headers.origin);
    console.log('Cookies received:', req.cookies);
    console.log('Token found:', token ? 'YES' : 'NO');

    if (!token) {
        console.log('Result: Access Denied (No Token)');
        return res.status(401).json({message : 'Akses ditolak, token tidak ada'});
    }

    try {
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        req.user = decoded;
        console.log('Result: Authenticated User ID:', decoded.id);
        next();
    } catch (e) {
        console.log('Result: Invalid Token Error:', e.message);
        res.status(401).json({message: 'Token tidak valid'});
    }
};
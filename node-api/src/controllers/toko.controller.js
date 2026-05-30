const tokoService = require('../services/toko.service');

exports.getToko = async (req, res) => {
    try {
        const result = await tokoService.getToko(req.params.id);

        res.json({
            hasToko : result.hasToko,
            data : result.data
        });
    } catch (e) {
        const statusCode = res.statusCode || 500;
        res.status(statusCode).json({ message : `Gagal mengambil data toko : ${e}` });
    }
}
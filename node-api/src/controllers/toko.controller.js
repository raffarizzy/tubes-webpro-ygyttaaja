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

exports.createToko = async (req, res) => {
    try {
        const {nama_toko, deskripsi_toko, lokasi} = req.body;
        const user_id = req.user.id;

        const logo_path = req.file ? req.file.path : null;
        const dataToko = {nama_toko, deskripsi_toko, lokasi, logo: logo_path};

        const checkToko = await tokoService.getToko(user_id);
        if (checkToko.hasToko) {
            return res.status(400).json({message: "User sudah memiliki toko!"});
        }

        const result = await tokoService.createToko(user_id, dataToko);

        return res.status(201).json(result);
    } catch (e) {
        console.error(e);
        return res.status(500).json({message: "Terjadi kesalahan server"});
    }
}
const service = require('../services/toko.service');
const response = require('../utils/response');

/**
 * GET /api/toko/my-toko
 * Get toko milik user yang sedang login
 * CATATAN: Butuh middleware auth untuk dapetin user_id dari token
 */
exports.getMyToko = async (req, res) => {
  try {
    // TODO: Ambil user_id dari JWT token (req.user.id)
    // Sementara ambil dari header dulu
    const userId = req.headers['x-user-id'];
    
    if (!userId) {
      return response.validationError(res, 'User ID tidak ditemukan. Login terlebih dahulu.');
    }

    const toko = await service.getByUserId(userId);

    if (!toko) {
      return response.notFound(res, 'Anda belum memiliki toko');
    }

    return response.success(res, toko, 'Berhasil mengambil data toko');
  } catch (error) {
    console.error('Error get my toko:', error);
    return response.error(res, 'Gagal mengambil data toko', 500, error.message);
  }
};

/**
 * GET /api/toko/check
 * Cek apakah user sudah punya toko
 */
exports.checkHasToko = async (req, res) => {
  try {
    const userId = req.headers['x-user-id'];
    
    if (!userId) {
      return response.validationError(res, 'User ID tidak ditemukan');
    }

    const toko = await service.checkUserHasToko(userId);

    return response.success(res, {
      hasToko: !!toko,
      toko: toko
    }, 'Berhasil cek status toko');
  } catch (error) {
    console.error('Error check toko:', error);
    return response.error(res, 'Gagal cek status toko', 500, error.message);
  }
};

/**
 * GET /api/toko/:id
 * Get detail toko by ID (untuk public)
 */
exports.show = async (req, res) => {
  try {
    const toko = await service.getById(req.params.id);

    if (!toko) {
      return response.notFound(res, 'Toko tidak ditemukan');
    }

    return response.success(res, toko, 'Berhasil mengambil detail toko');
  } catch (error) {
    console.error('Error get toko detail:', error);
    return response.error(res, 'Gagal mengambil detail toko', 500, error.message);
  }
};

/**
 * POST /api/toko
 * Create toko baru
 */
exports.create = async (req, res) => {
  try {
    console.log('=== CREATE TOKO ===');
    console.log('HEADERS:', req.headers);
    console.log('BODY:', req.body);

    const userId = req.headers['x-user-id'];
    
    if (!userId) {
      return response.validationError(res, 'User ID tidak ditemukan. Login terlebih dahulu.');
    }

    const { nama_toko, deskripsi_toko, lokasi, logo_path } = req.body;

    if (!nama_toko) {
      return response.validationError(res, 'Nama toko wajib diisi');
    }

    const result = await service.create({
      user_id: userId,
      nama_toko,
      deskripsi_toko,
      lokasi,
      logo_path
    });

    return response.success(res, result, 'Toko berhasil dibuat', 201);
  } catch (error) {
    console.error('Error create toko:', error);
    
    if (error.message === 'User sudah memiliki toko') {
      return response.validationError(res, error.message);
    }

    return response.error(res, 'Gagal membuat toko', 500, error.message);
  }
};

/**
 * PATCH /api/toko/:id
 * Update toko (hanya owner)
 */
exports.update = async (req, res) => {
  try {
    console.log('=== UPDATE TOKO ===');
    console.log('PARAM ID:', req.params.id);
    console.log('BODY:', req.body);

    const userId = req.headers['x-user-id'];
    
    if (!userId) {
      return response.validationError(res, 'User ID tidak ditemukan. Login terlebih dahulu.');
    }

    const result = await service.update(req.params.id, userId, req.body);

    return response.success(res, result, 'Toko berhasil diperbarui');
  } catch (error) {
    console.error('Error update toko:', error);

    if (error.message.includes('tidak ditemukan') || error.message.includes('bukan milik')) {
      return response.notFound(res, error.message);
    }

    return response.error(res, 'Gagal memperbarui toko', 500, error.message);
  }
};

/**
 * DELETE /api/toko/:id
 * Delete toko (hanya owner)
 */
exports.delete = async (req, res) => {
  try {
    console.log('=== DELETE TOKO ===');
    console.log('PARAM ID:', req.params.id);

    const userId = req.headers['x-user-id'];
    
    if (!userId) {
      return response.validationError(res, 'User ID tidak ditemukan. Login terlebih dahulu.');
    }

    const result = await service.delete(req.params.id, userId);

    return response.success(res, result, 'Toko berhasil dihapus');
  } catch (error) {
    console.error('Error delete toko:', error);

    if (error.message.includes('tidak ditemukan') || error.message.includes('bukan milik')) {
      return response.notFound(res, error.message);
    }

    return response.error(res, 'Gagal menghapus toko', 500, error.message);
  }
};
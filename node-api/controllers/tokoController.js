// =====================================================
// Toko Controller - Business Logic
// =====================================================
const TokoRepository = require('../repositories/tokoRepository');
const { success, error, notFound } = require('../utils/response');

class TokoController {
  constructor(pool) {
    this.repository = new TokoRepository(pool);
  }

  /**
   * GET all tokos
   */
  async index(req, res) {
    try {
      const tokos = await this.repository.getAll();
      return success(res, tokos, 'Tokos retrieved successfully');
    } catch (err) {
      console.error('Error fetching tokos:', err);
      return error(res, 'Failed to fetch tokos', 500, err.message);
    }
  }

  /**
   * GET toko by ID
   */
  async show(req, res) {
    try {
      const { id } = req.params;
      const toko = await this.repository.getById(id);

      if (!toko) {
        return notFound(res, 'Toko not found');
      }

      return success(res, toko, 'Toko retrieved successfully');
    } catch (err) {
      console.error('Error fetching toko:', err);
      return error(res, 'Failed to fetch toko', 500, err.message);
    }
  }

  /**
   * POST create new toko
   */
  async store(req, res) {
    try {
      const { nama_toko, user_id, deskripsi, lokasi } = req.body;

      // Validation
      if (!nama_toko || !user_id) {
        return error(res, 'Missing required fields: nama_toko, user_id', 400);
      }

      const tokoId = await this.repository.create({
        nama_toko,
        user_id,
        deskripsi,
        lokasi
      });

      return success(res, { id: tokoId }, 'Toko created successfully', 201);
    } catch (err) {
      console.error('Error creating toko:', err);
      return error(res, 'Failed to create toko', 500, err.message);
    }
  }

  /**
   * PUT update toko
   */
  async update(req, res) {
    try {
      const { id } = req.params;
      const { nama_toko, deskripsi, lokasi } = req.body;

      const updated = await this.repository.update(id, {
        nama_toko,
        deskripsi,
        lokasi
      });

      if (!updated) {
        return notFound(res, 'Toko not found');
      }

      return success(res, { id }, 'Toko updated successfully');
    } catch (err) {
      console.error('Error updating toko:', err);
      return error(res, 'Failed to update toko', 500, err.message);
    }
  }

  /**
   * DELETE toko
   */
  async destroy(req, res) {
    try {
      const { id } = req.params;
      const deleted = await this.repository.delete(id);

      if (!deleted) {
        return notFound(res, 'Toko not found');
      }

      return success(res, null, 'Toko deleted successfully');
    } catch (err) {
      console.error('Error deleting toko:', err);
      return error(res, 'Failed to delete toko', 500, err.message);
    }
  }
}

module.exports = TokoController;
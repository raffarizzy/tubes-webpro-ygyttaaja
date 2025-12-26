const service = require('../services/product.service');
const response = require('../utils/response');

/**
 * GET /api/products/:id
 * Get detail produk by ID
 */
exports.show = async (req, res) => {
  try {
    const product = await service.getById(req.params.id);

    if (!product) {
      return response.notFound(res, 'Produk tidak ditemukan');
    }

    return response.success(res, product, 'Berhasil mengambil detail produk');
  } catch (error) {
    console.error('Error get product detail:', error);
    return response.error(res, 'Gagal mengambil detail produk', 500, error.message);
  }
};

/**
 * GET /api/products
 * Get all products dengan pagination
 */
exports.index = async (req, res) => {
  try {
    const limit = req.query.limit || 20;
    const offset = req.query.offset || 0;

    const products = await service.getAll(limit, offset);

    return response.success(res, products, 'Berhasil mengambil daftar produk');
  } catch (error) {
    console.error('Error get all products:', error);
    return response.error(res, 'Gagal mengambil daftar produk', 500, error.message);
  }
};

/**
 * GET /api/products/toko/:tokoId
 * Get products by toko ID
 */
exports.getByToko = async (req, res) => {
  try {
    const products = await service.getByToko(req.params.tokoId);

    return response.success(res, products, 'Berhasil mengambil produk toko');
  } catch (error) {
    console.error('Error get products by toko:', error);
    return response.error(res, 'Gagal mengambil produk toko', 500, error.message);
  }
};

/**
 * GET /api/products/category/:categoryId
 * Get products by category ID
 */
exports.getByCategory = async (req, res) => {
  try {
    const products = await service.getByCategory(req.params.categoryId);

    return response.success(res, products, 'Berhasil mengambil produk kategori');
  } catch (error) {
    console.error('Error get products by category:', error);
    return response.error(res, 'Gagal mengambil produk kategori', 500, error.message);
  }
};

/**
 * POST /api/products
 * Create new product
 */
exports.create = async (req, res) => {
  try {
    console.log('=== CREATE PRODUCT ===');
    console.log('BODY:', req.body);

    // Validasi input
    const { toko_id, category_id, nama, deskripsi, harga, stok, imagePath } = req.body;

    if (!toko_id || !category_id || !nama || !deskripsi || !harga || !stok || !imagePath) {
      return response.validationError(res, 'Field toko_id, category_id, nama, deskripsi, harga, stok, dan imagePath wajib diisi');
    }

    const result = await service.create(req.body);

    return response.success(res, result, 'Produk berhasil dibuat', 201);
  } catch (error) {
    console.error('Error create product:', error);
    return response.error(res, 'Gagal membuat produk', 500, error.message);
  }
};

/**
 * PATCH /api/products/:id
 * Update product
 */
exports.update = async (req, res) => {
  try {
    console.log('=== UPDATE PRODUCT ===');
    console.log('PARAM ID:', req.params.id);
    console.log('BODY:', req.body);

    const result = await service.update(req.params.id, req.body);

    return response.success(res, result, 'Produk berhasil diperbarui');
  } catch (error) {
    console.error('Error update product:', error);

    if (error.message === 'Produk tidak ditemukan') {
      return response.notFound(res, error.message);
    }

    return response.error(res, 'Gagal memperbarui produk', 500, error.message);
  }
};

/**
 * DELETE /api/products/:id
 * Delete product
 */
exports.delete = async (req, res) => {
  try {
    console.log('=== DELETE PRODUCT ===');
    console.log('PARAM ID:', req.params.id);

    const result = await service.delete(req.params.id);

    return response.success(res, result, 'Produk berhasil dihapus');
  } catch (error) {
    console.error('Error delete product:', error);

    if (error.message === 'Produk tidak ditemukan') {
      return response.notFound(res, error.message);
    }

    return response.error(res, 'Gagal menghapus produk', 500, error.message);
  }
};
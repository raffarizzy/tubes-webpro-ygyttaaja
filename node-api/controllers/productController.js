// =====================================================
// Product Controller - Business Logic
// =====================================================
const ProductRepository = require('../repositories/productRepository');
const { success, error, notFound } = require('../utils/response');

class ProductController {
  constructor(pool) {
    this.repository = new ProductRepository(pool);
  }

  /**
   * GET all products
   */
  async index(req, res) {
    try {
      const products = await this.repository.getAll();
      return success(res, products, 'Products retrieved successfully');
    } catch (err) {
      console.error('Error fetching products:', err);
      return error(res, 'Failed to fetch products', 500, err.message);
    }
  }

  /**
   * GET product by ID
   */
  async show(req, res) {
    try {
      const { id } = req.params;
      const product = await this.repository.getById(id);

      if (!product) {
        return notFound(res, 'Product not found');
      }

      return success(res, product, 'Product retrieved successfully');
    } catch (err) {
      console.error('Error fetching product:', err);
      return error(res, 'Failed to fetch product', 500, err.message);
    }
  }

  /**
   * GET product ratings
   */
  async ratings(req, res) {
    try {
      const { id } = req.params;
      const ratings = await this.repository.getRatings(id);
      return success(res, ratings, 'Ratings retrieved successfully');
    } catch (err) {
      console.error('Error fetching ratings:', err);
      return error(res, 'Failed to fetch ratings', 500, err.message);
    }
  }

  /**
   * POST create new product
   */
  async store(req, res) {
    try {
      const { nama, harga, deskripsi, toko_id, category_id, stok } = req.body;

      // Validation
      if (!nama || !harga || !toko_id || !category_id) {
        return error(res, 'Missing required fields: nama, harga, toko_id, category_id', 400);
      }

      const productId = await this.repository.create({
        nama,
        harga,
        deskripsi,
        toko_id,
        category_id,
        stok
      });

      return success(res, { id: productId }, 'Product created successfully', 201);
    } catch (err) {
      console.error('Error creating product:', err);
      return error(res, 'Failed to create product', 500, err.message);
    }
  }

  /**
   * PUT update product
   */
  async update(req, res) {
    try {
      const { id } = req.params;
      const { nama, harga, deskripsi, toko_id, category_id, stok } = req.body;

      const updated = await this.repository.update(id, {
        nama,
        harga,
        deskripsi,
        toko_id,
        category_id,
        stok
      });

      if (!updated) {
        return notFound(res, 'Product not found');
      }

      return success(res, { id }, 'Product updated successfully');
    } catch (err) {
      console.error('Error updating product:', err);
      return error(res, 'Failed to update product', 500, err.message);
    }
  }

  /**
   * DELETE product
   */
  async destroy(req, res) {
    try {
      const { id } = req.params;
      const deleted = await this.repository.delete(id);

      if (!deleted) {
        return notFound(res, 'Product not found');
      }

      return success(res, null, 'Product deleted successfully');
    } catch (err) {
      console.error('Error deleting product:', err);
      return error(res, 'Failed to delete product', 500, err.message);
    }
  }
}

module.exports = ProductController;
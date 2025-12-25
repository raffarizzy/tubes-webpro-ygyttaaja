// =====================================================
// User Controller - Business Logic
// =====================================================
const bcrypt = require('bcrypt');
const UserRepository = require('../repositories/userRepository');
const { success, error, notFound, validationError } = require('../utils/response');

class UserController {
  constructor(pool) {
    this.repository = new UserRepository(pool);
  }

  /**
   * GET all users
   */
  async index(req, res) {
    try {
      const users = await this.repository.getAll();
      return success(res, users, 'Users retrieved successfully');
    } catch (err) {
      console.error('Error fetching users:', err);
      return error(res, 'Failed to fetch users', 500, err.message);
    }
  }

  /**
   * GET user by ID
   */
  async show(req, res) {
    try {
      const { id } = req.params;
      const user = await this.repository.getById(id);

      if (!user) {
        return notFound(res, 'User not found');
      }

      return success(res, user, 'User retrieved successfully');
    } catch (err) {
      console.error('Error fetching user:', err);
      return error(res, 'Failed to fetch user', 500, err.message);
    }
  }

  /**
   * POST register new user
   */
  async register(req, res) {
    try {
      const { name, email, password, phone, birthDate, gender } = req.body;

      // Validation
      if (!name || !email || !password) {
        return validationError(res, 'Name, email, and password are required');
      }

      // Check if email exists
      const existingUser = await this.repository.getByEmail(email);
      if (existingUser) {
        return error(res, 'Email already exists', 409);
      }

      // Hash password
      const hashedPassword = await bcrypt.hash(password, 10);

      // Create user
      const userId = await this.repository.create({
        name,
        email,
        password: hashedPassword,
        phone,
        birthDate,
        gender
      });

      // Return user data without password
      return success(
        res,
        {
          id: userId,
          name,
          email,
          phone,
          birthDate,
          gender
        },
        'User registered successfully',
        201
      );
    } catch (err) {
      console.error('Error registering user:', err);
      return error(res, 'Failed to register user', 500, err.message);
    }
  }

  /**
   * PUT update user
   */
  async update(req, res) {
    try {
      const { id } = req.params;
      const { name, email, phone } = req.body;

      const updated = await this.repository.update(id, {
        name,
        email,
        phone
      });

      if (!updated) {
        return notFound(res, 'User not found');
      }

      return success(res, { id }, 'User updated successfully');
    } catch (err) {
      console.error('Error updating user:', err);
      return error(res, 'Failed to update user', 500, err.message);
    }
  }

  /**
   * DELETE user
   */
  async destroy(req, res) {
    try {
      const { id } = req.params;
      const deleted = await this.repository.delete(id);

      if (!deleted) {
        return notFound(res, 'User not found');
      }

      return success(res, null, 'User deleted successfully');
    } catch (err) {
      console.error('Error deleting user:', err);
      return error(res, 'Failed to delete user', 500, err.message);
    }
  }
}

module.exports = UserController;
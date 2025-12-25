// =====================================================
// Users/Auth API Routes
// =====================================================
const express = require('express');
const router = express.Router();
const UserController = require('../controllers/userController');

module.exports = (pool) => {
  const controller = new UserController(pool);

  // GET all users
  router.get('/', (req, res) => controller.index(req, res));

  // GET user by ID
  router.get('/:id', (req, res) => controller.show(req, res));

  // POST register new user
  router.post('/register', (req, res) => controller.register(req, res));

  // PUT update user
  router.put('/:id', (req, res) => controller.update(req, res));

  // DELETE user
  router.delete('/:id', (req, res) => controller.destroy(req, res));

  return router;
};
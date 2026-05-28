const express = require('express');
const router = express.Router();
const controller = require('../controllers/product.controller');

// GET all products (dengan pagination)
router.get('/', controller.index);

// GET detail product by ID
router.get('/:id', controller.show);

// GET products by toko ID
router.get('/toko/:tokoId', controller.getByToko);

// GET products by category ID
router.get('/category/:categoryId', controller.getByCategory);

// POST create new product
router.post('/', controller.create);

// PATCH update product
router.patch('/:id', controller.update);

// DELETE product
router.delete('/:id', controller.delete);

module.exports = router;
const express = require('express');
const router = express.Router();
const controller = require('../controllers/toko.controller');

// GET my toko (user yang login)
router.get('/my-toko', controller.getMyToko);

// GET check apakah user punya toko
router.get('/check', controller.checkHasToko);

// GET detail toko by ID (public)
router.get('/:id', controller.show);

// POST create toko baru
router.post('/', controller.create);

// PATCH update toko
router.patch('/:id', controller.update);

// DELETE toko
router.delete('/:id', controller.delete);

module.exports = router;
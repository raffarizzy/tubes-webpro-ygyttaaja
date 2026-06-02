const express = require('express');
const router = express.Router();
const tokoController = require('../controllers/toko.controller');
const authMiddleware = require('../middleware/auth.middleware');

router.get('/:id', authMiddleware, tokoController.getToko);

module.exports = router;

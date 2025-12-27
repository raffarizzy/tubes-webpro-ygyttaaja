const express = require('express');
const router = express.Router();
const ratingController = require('../controllers/rating.controller');

// GET semua rating user (dipanggil Laravel index)
router.get('/', ratingController.index);

// POST tambah rating (dipanggil Laravel store)
router.post('/', ratingController.store);

// DELETE rating (opsional)
router.delete('/:id', ratingController.destroy);

module.exports = router;
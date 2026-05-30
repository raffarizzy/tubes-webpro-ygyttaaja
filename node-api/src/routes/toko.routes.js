const express = require('express');
const router = express.Router();
const tokoController = require('../controllers/toko.controller');

router.get('/:id', tokoController.getToko);

module.exports = router;

const express = require('express');
const router = express.Router();
const multer = require('multer');
const upload = multer({dest: 'uploads/'});
const tokoController = require('../controllers/toko.controller');
const authMiddleware = require('../middleware/auth.middleware');

router.get('/:id', authMiddleware, tokoController.getToko);
router.post('/', authMiddleware, upload.single('logo'), tokoController.createToko);
module.exports = router;

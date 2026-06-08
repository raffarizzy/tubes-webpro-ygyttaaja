const express = require('express');
const router = express.Router();
const controller = require('../controllers/profile.controller');
const authMiddleware = require('../middleware/auth.middleware');
const upload = require('../middleware/upload.middleware');

router.get('/:id', authMiddleware, controller.show);     // GET profil
router.patch('/:id', authMiddleware, upload.single('pfp'), controller.update);   // UPDATE profil

module.exports = router;
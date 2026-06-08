const express = require('express');
const router = express.Router();
const controller = require('../controllers/profile.controller');
const authMiddleware = require('../middleware/auth.middleware');

router.get('/:id', authMiddleware, controller.show);     // GET profil
router.patch('/:id', authMiddleware, controller.update);   // UPDATE profil

module.exports = router;
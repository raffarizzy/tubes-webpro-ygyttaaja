const express = require('express');
const router = express.Router();
const controller = require('../controllers/profile.controller');

router.get('/:id', controller.show);     // GET profil
router.put('/:id', controller.update);   // UPDATE profil

module.exports = router;
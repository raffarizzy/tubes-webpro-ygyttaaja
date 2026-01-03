const express = require("express");
const router = express.Router();
const controller = require("../controllers/alamat.controller");

// GET all alamat for a user
router.get("/:userId", controller.getUserAlamat);

// GET alamat by ID
router.get("/detail/:alamatId", controller.getAlamatById);

// POST create new alamat
router.post("/", controller.createAlamat);

// PUT update alamat
router.put("/:alamatId", controller.updateAlamat);

// DELETE alamat
router.delete("/:alamatId", controller.deleteAlamat);

// PUT set alamat as default
router.put("/:alamatId/set-default", controller.setDefaultAlamat);

module.exports = router;

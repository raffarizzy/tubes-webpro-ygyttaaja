const service = require('../services/profile.service');

exports.show = async (req, res) => {
  try {
    const user = await service.getById(req.params.id);
    if (!user) {
      return res.status(404).json({ message: 'User tidak ditemukan' });
    }
    res.json(user);
  } catch (err) {
    res.status(500).json({ message: err.message });
  }
};

exports.update = async (req, res) => {
  try {
    // Basic validation
    if (!req.body.name || !req.body.email) {
      return res.status(400).json({ message: 'Nama dan Email wajib diisi' });
    }

    const updated = await service.update(req.params.id, req.body);
    res.json({
      success: true,
      message: 'Profil berhasil diperbarui',
      data: updated
    });
  } catch (err) {
    console.error('❌ Error Update Profile:', err);
    res.status(500).json({ 
      success: false,
      message: `Backend Error: ${err.message}`,
      error: err
    });
  }
};

const ratingService = require('../services/rating.service');

exports.index = async (req, res) => {
  try {
    const { user_id } = req.query;

    if (!user_id) {
      return res.status(400).json({ 
        success: false,
        message: 'user_id wajib diisi' 
      });
    }

    const ratings = await ratingService.getByUser(user_id);

    res.json({
      success: true,
      data: ratings
    });
  } catch (err) {
    console.error('Error fetching ratings:', err);
    res.status(500).json({ 
      success: false,
      message: err.message 
    });
  }
};

exports.store = async (req, res) => {
  try {
    const { user_id, product_id, rating, review } = req.body;

    // Validasi input
    if (!user_id || !product_id || !rating || !review) {
      return res.status(400).json({ 
        success: false,
        message: 'Data tidak lengkap' 
      });
    }

    // Validasi rating 1-5
    if (rating < 1 || rating > 5) {
      return res.status(400).json({ 
        success: false,
        message: 'Rating harus antara 1-5' 
      });
    }

    const result = await ratingService.create({
      user_id,
      product_id,
      rating,
      review
    });

    res.status(201).json({
      success: true,
      message: 'Rating berhasil ditambahkan',
      data: result
    });
  } catch (err) {
    console.error('Error creating rating:', err);
    res.status(400).json({ 
      success: false,
      message: err.message 
    });
  }
};

exports.destroy = async (req, res) => {
  try {
    const { id } = req.params;
    const { user_id } = req.body;

    if (!user_id) {
      return res.status(400).json({ 
        success: false,
        message: 'user_id wajib diisi' 
      });
    }

    await ratingService.remove(id, user_id);
    
    res.json({ 
      success: true,
      message: 'Rating berhasil dihapus' 
    });
  } catch (err) {
    console.error('Error deleting rating:', err);
    res.status(500).json({ 
      success: false,
      message: err.message 
    });
  }
};
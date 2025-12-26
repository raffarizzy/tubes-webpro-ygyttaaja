const service = require('../services/profile.service');

exports.show = async (req, res) => {
  const user = await service.getById(req.params.id);
  res.json(user);
};

exports.update = async (req, res) => {
  console.log('=== MASUK PATCH PROFILE ===');
  console.log('PARAM ID:', req.params.id);
  console.log('HEADERS:', req.headers);
  console.log('BODY:', req.body);

  const updated = await service.update(req.params.id, req.body);
  res.json(updated);
};

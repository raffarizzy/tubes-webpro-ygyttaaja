const service = require('../services/profile.service');

exports.show = async (req, res) => {
  const user = await service.getById(req.params.id);
  res.json(user);
};

exports.update = async (req, res) => {
  const updated = await service.update(req.params.id, req.body);
  res.json(updated);
};

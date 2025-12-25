const express = require('express');
const cors = require('cors');
const profileRoutes = require('./routes/profile.routes');

const app = express();
app.use(cors());
app.use(express.json());

app.get('/api/test', (req, res) => {
  res.json({ message: 'Node.js API jalan' });
});

app.use('/api/profile', profileRoutes);

module.exports = app;
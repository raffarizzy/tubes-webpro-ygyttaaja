const express = require('express');
const cors = require('cors');
const app = express();
const port = 3001;

app.use(cors());
app.use(express.json());

// Log Request di paling atas
app.use((req, res, next) => {
  console.log(`[${new Date().toLocaleTimeString()}] ${req.method} ${req.url}`);
  next();
});

app.get('/api/products', (req, res) => {
  res.json({
    success: true,
    data: [
      { id: 1, nama: 'Busi Racing', harga: 50000, stok: 10, imagePath: 'produk/busi.jpg', deskripsi: 'Busi kencang' },
      { id: 2, nama: 'Oli Mesin', harga: 80000, stok: 5, imagePath: 'produk/oli.jpg', deskripsi: 'Oli licin' }
    ]
  });
});

app.get('/api/products/:id', (req, res) => {
  res.json({
    success: true,
    data: { id: req.params.id, nama: 'Busi Racing ' + req.params.id, harga: 50000, stok: 10, imagePath: 'produk/busi.jpg', deskripsi: 'Busi kencang', nama_toko: 'Mock Toko', category_nama: 'Sparepart' }
  });
});

app.get('/api/toko/check', (req, res) => {
  res.json({ data: { hasToko: false } });
});

let mockCart = [];
app.post('/api/cart/item', (req, res) => {
  const item = { id: Date.now(), product_id: req.body.product_id, jumlah: req.body.jumlah, product: { nama: 'Busi Racing', harga: 50000 } };
  mockCart.push(item);
  res.status(201).json({ success: true, message: 'Item added', data: item });
});

app.get('/api/cart/data/:userId', (req, res) => {
  res.json({ success: true, data: { total_items: mockCart.length, total_price: mockCart.length * 50000 } });
});

app.listen(port, '127.0.0.1', () => {
  console.log(`\n✅ MOCK API ACTIVE AT http://127.0.0.1:${port}`);
  console.log(`Log request akan muncul di bawah ini:\n`);
});

// Penyelamat agar server tidak exit jika ada error tak terduga
process.on('uncaughtException', (err) => {
  console.error('Ada error di Mock API:', err.message);
});

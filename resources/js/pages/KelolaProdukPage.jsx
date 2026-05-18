import { useState, useEffect } from 'react';
import api from '../services/api';
import nodeApi from '../services/nodeApi';
import { useAuth } from '../hooks/useAuth';
import { useToast, Toast } from '../components/Toast';
import { formatRupiah } from '../utils/format';

export default function KelolaProdukPage() {
  const { user } = useAuth();
  const { toast, showToast } = useToast();
  const [produkList, setProdukList] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [editProduk, setEditProduk] = useState(null);
  const [form, setForm] = useState({ nama: '', harga: '', stok: '', deskripsi: '', category_id: 1, image: null });
  const [tokoId, setTokoId] = useState(null);

  useEffect(() => {
    api.get('/api/toko').then((res) => {
      if (res.data) setTokoId(res.data.id);
    });
    loadProduk();
  }, []);

  async function loadProduk() {
    try {
      const res = await nodeApi.get('/products');
      setProdukList(res.data.success ? res.data.data : []);
    } catch {}
  }

  async function handleSubmit(e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('nama', form.nama);
    fd.append('harga', form.harga);
    fd.append('stok', form.stok);
    fd.append('deskripsi', form.deskripsi);
    fd.append('category_id', form.category_id);
    if (form.image) fd.append('image', form.image);

    try {
      if (editProduk) {
        fd.append('_method', 'PUT');
        await api.post(`/product/${editProduk.id}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
        showToast('Produk berhasil diperbarui!', 'success');
      } else {
        await api.post('/product/store', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
        showToast('Produk berhasil ditambahkan!', 'success');
      }
      setShowForm(false);
      setEditProduk(null);
      setForm({ nama: '', harga: '', stok: '', deskripsi: '', category_id: 1, image: null });
      loadProduk();
    } catch (err) {
      showToast(err.response?.data?.message || 'Gagal menyimpan produk', 'error');
    }
  }

  async function handleDelete(id, nama) {
    if (!confirm(`Hapus produk "${nama}"?`)) return;
    try {
      await api.delete(`/product/${id}`);
      showToast('Produk dihapus', 'info');
      loadProduk();
    } catch {
      showToast('Gagal menghapus produk', 'error');
    }
  }

  function openEdit(produk) {
    setEditProduk(produk);
    setForm({ nama: produk.nama, harga: produk.harga, stok: produk.stok, deskripsi: produk.deskripsi, category_id: produk.category_id || 1, image: null });
    setShowForm(true);
  }

  const myProduk = tokoId ? produkList.filter((p) => p.toko_id === tokoId) : [];

  return (
    <div className="max-w-5xl mx-auto px-4 py-10">
      <Toast toast={toast} />
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-bold text-gray-800">Kelola Produk</h1>
        <button
          onClick={() => { setEditProduk(null); setForm({ nama: '', harga: '', stok: '', deskripsi: '', category_id: 1, image: null }); setShowForm(true); }}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700"
        >
          + Tambah Produk
        </button>
      </div>

      {showForm && (
        <div className="bg-white rounded-2xl shadow p-6 mb-6">
          <h2 className="font-semibold text-gray-800 mb-4">{editProduk ? 'Edit Produk' : 'Tambah Produk'}</h2>
          <form onSubmit={handleSubmit} className="space-y-3">
            {[
              { name: 'nama', label: 'Nama Produk', type: 'text' },
              { name: 'harga', label: 'Harga (Rp)', type: 'number' },
              { name: 'stok', label: 'Stok', type: 'number' },
              { name: 'deskripsi', label: 'Deskripsi', type: 'text' },
            ].map(({ name, label, type }) => (
              <div key={name}>
                <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
                <input
                  type={type}
                  value={form[name]}
                  onChange={(e) => setForm({ ...form, [name]: e.target.value })}
                  className="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
                  required
                />
              </div>
            ))}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Gambar {editProduk && '(opsional)'}</label>
              <input
                type="file"
                accept="image/*"
                onChange={(e) => setForm({ ...form, image: e.target.files[0] })}
                required={!editProduk}
              />
            </div>
            <div className="flex gap-2 mt-2">
              <button type="submit" className="flex-1 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                {editProduk ? 'Simpan Perubahan' : 'Tambah Produk'}
              </button>
              <button type="button" onClick={() => { setShowForm(false); setEditProduk(null); }} className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Batal
              </button>
            </div>
          </form>
        </div>
      )}

      {myProduk.length === 0 ? (
        <p className="text-gray-400 text-center py-16">Belum ada produk. Tambah produk pertama Anda!</p>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          {myProduk.map((p) => (
            <div key={p.id} className="bg-white rounded-xl shadow p-4">
              <img src={p.imagePath} alt={p.nama} className="w-full h-36 object-cover rounded-lg mb-3"
                onError={(e) => { e.target.onerror = null; e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="150"%3E%3Crect fill="%23ddd" width="200" height="150"/%3E%3C/svg%3E'; }}
              />
              <h3 className="font-medium text-gray-800 text-sm mb-1">{p.nama}</h3>
              <p className="text-blue-600 font-bold text-sm">{formatRupiah(p.harga)}</p>
              <p className="text-gray-400 text-xs mb-3">Stok: {p.stok}</p>
              <div className="flex gap-2">
                <button onClick={() => openEdit(p)} className="flex-1 py-1.5 text-sm border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50">
                  Edit
                </button>
                <button onClick={() => handleDelete(p.id, p.nama)} className="flex-1 py-1.5 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600">
                  Hapus
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';
import nodeApi from '../services/nodeApi';
import { useAuth } from '../hooks/useAuth';
import { useToast, Toast } from '../components/Toast';
import { formatRupiah } from '../utils/format';

export default function TokoPage() {
  const { user } = useAuth();
  const { toast, showToast } = useToast();
  const navigate = useNavigate();
  const [toko, setToko] = useState(null);
  const [produkList, setProdukList] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [showEditForm, setShowEditForm] = useState(false);
  const [createForm, setCreateForm] = useState({ nama_toko: '', deskripsi_toko: '', lokasi: '', logo: null });
  const [editForm, setEditForm] = useState({ nama_toko: '', deskripsi_toko: '', lokasi: '', logo: null });

  useEffect(() => {
    if (user && user.id) {
      api.get(`http://localhost:3001/api/toko/${user.id}`)
        .then((res) => {
          try {
            if (!res.data.hasToko) {
              setShowCreateForm(true)
            } else {
              setToko(res.data.data);
              loadProduk(res.data.data.id);
            }
          } catch (e) {
            console.error(e);
          }
        })
        .catch(() => setShowCreateForm(true))
        .finally(() => setLoading(false));
    }
  }, [user]);

  async function loadProduk(tokoId) {
    try {
      const res = await nodeApi.get('/products');
      const all = res.data.success ? res.data.data : [];
      setProdukList(all.filter((p) => p.toko_id === tokoId));
    } catch {}
  }

  async function handleCreate(e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('nama_toko', createForm.nama_toko);
    fd.append('deskripsi_toko', createForm.deskripsi_toko);
    fd.append('lokasi', createForm.lokasi);
    if (createForm.logo) fd.append('logo', createForm.logo);
    try {
      await nodeApi.post('/toko', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast('Toko berhasil dibuat!', 'success');
      const res = await nodeApi.get(`/toko/${user.id}`);
      setToko(res.data.data);
      setShowCreateForm(false);
      if (res.data.data) loadProduk(res.data.id);
    } catch (err) {
      showToast(err.response?.data?.message || 'Gagal membuat toko', 'error');
    }
  }

  async function handleEdit(e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('nama_toko', editForm.nama_toko);
    fd.append('deskripsi_toko', editForm.deskripsi_toko);
    fd.append('lokasi', editForm.lokasi);
    if (editForm.logo) fd.append('logo', editForm.logo);
    fd.append('_method', 'PUT');
    try {
      await api.post(`/toko/${toko.id}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast('Toko berhasil diperbarui!', 'success');
      const res = await api.get('/api/toko');
      setToko(res.data);
      setShowEditForm(false);
    } catch (err) {
      showToast(err.response?.data?.message || 'Gagal memperbarui toko', 'error');
    }
  }

  if (loading) return <div className="text-center py-20 text-gray-500">Memuat...</div>;

  if (showCreateForm) {
    return (
      <div className="max-w-2xl mx-auto px-4 py-10">
        <Toast toast={toast} />
        <h1 className="text-2xl font-bold text-gray-800 mb-6">Buat Toko Anda</h1>
        <div className="bg-white rounded-2xl shadow p-6">
          <form onSubmit={handleCreate} className="space-y-4">
            {[
              { name: 'nama_toko', label: 'Nama Toko', type: 'text' },
              { name: 'deskripsi_toko', label: 'Deskripsi', type: 'text' },
              { name: 'lokasi', label: 'Lokasi', type: 'text' },
            ].map(({ name, label, type }) => (
              <div key={name}>
                <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
                <input
                  type={type}
                  value={createForm[name]}
                  onChange={(e) => setCreateForm({ ...createForm, [name]: e.target.value })}
                  className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
                  required
                />
              </div>
            ))}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Logo Toko</label>
              <input
                type="file"
                accept="image/*"
                onChange={(e) => setCreateForm({ ...createForm, logo: e.target.files[0] })}
                className="w-full text-sm text-gray-500"
                required
              />
            </div>
            <button type="submit" className="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700">
              Buat Toko
            </button>
          </form>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-5xl mx-auto px-4 py-10">
      <Toast toast={toast} />

      {/* Header Toko */}
      <div className="bg-white rounded-2xl shadow p-6 mb-6 flex items-center gap-5">
        <img
          src={toko?.logo_path ? `/storage/${toko.logo_path}` : 'https://i.ibb.co.com/RkZ105G9/default-avatar.png'}
          alt="Logo Toko"
          className="w-20 h-20 rounded-full object-cover border-2 border-gray-200"
          onError={(e) => { e.target.onerror = null; e.target.src = 'https://i.ibb.co.com/RkZ105G9/default-avatar.png'; }}
        />
        <div className="flex-1">
          <h1 className="text-xl font-bold text-gray-800">{toko?.nama_toko}</h1>
          <p className="text-gray-500 text-sm">{toko?.deskripsi_toko}</p>
          <p className="text-gray-400 text-sm mt-1">📍 {toko?.lokasi}</p>
        </div>
        <div className="flex gap-2">
          <button
            onClick={() => { setEditForm({ nama_toko: toko.nama_toko, deskripsi_toko: toko.deskripsi_toko, lokasi: toko.lokasi, logo: null }); setShowEditForm(true); }}
            className="px-4 py-2 border-2 border-blue-600 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-50"
          >
            Edit Toko
          </button>
          <button
            onClick={() => navigate('/kelola-produk')}
            className="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
          >
            Kelola Produk
          </button>
        </div>
      </div>

      {/* Edit Form */}
      {showEditForm && (
        <div className="bg-white rounded-2xl shadow p-6 mb-6">
          <h2 className="font-semibold text-gray-800 mb-4">Edit Toko</h2>
          <form onSubmit={handleEdit} className="space-y-3">
            {[
              { name: 'nama_toko', label: 'Nama Toko' },
              { name: 'deskripsi_toko', label: 'Deskripsi' },
              { name: 'lokasi', label: 'Lokasi' },
            ].map(({ name, label }) => (
              <div key={name}>
                <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
                <input
                  type="text"
                  value={editForm[name]}
                  onChange={(e) => setEditForm({ ...editForm, [name]: e.target.value })}
                  className="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
                />
              </div>
            ))}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Logo Baru (opsional)</label>
              <input type="file" accept="image/*" onChange={(e) => setEditForm({ ...editForm, logo: e.target.files[0] })} />
            </div>
            <div className="flex gap-2">
              <button type="submit" className="flex-1 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Simpan</button>
              <button type="button" onClick={() => setShowEditForm(false)} className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Batal</button>
            </div>
          </form>
        </div>
      )}

      {/* Produk Toko */}
      <h2 className="text-lg font-bold text-gray-800 mb-4">Produk Toko ({produkList.length})</h2>
      {produkList.length === 0 ? (
        <p className="text-gray-400 text-center py-10">Belum ada produk. Tambah di halaman Kelola Produk.</p>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          {produkList.map((p) => (
            <div key={p.id} className="bg-white rounded-xl shadow p-4">
              <img
                src={p.imagePath}
                alt={p.nama}
                className="w-full h-36 object-cover rounded-lg mb-3"
                onError={(e) => { e.target.onerror = null; e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="150"%3E%3Crect fill="%23ddd" width="200" height="150"/%3E%3C/svg%3E'; }}
              />
              <h3 className="font-medium text-gray-800 text-sm">{p.nama}</h3>
              <p className="text-blue-600 font-bold text-sm">{formatRupiah(p.harga)}</p>
              <p className="text-gray-400 text-xs">Stok: {p.stok}</p>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

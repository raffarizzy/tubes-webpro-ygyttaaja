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

  if (loading) {
    return (
      <div className="flex flex-col items-center justify-center min-h-[60vh]">
        <div className="w-12 h-12 border-4 border-slate-200 border-t-indigo-600 rounded-full animate-spin"></div>
        <p className="mt-4 text-slate-500 font-medium tracking-wide">Menyiapkan toko anda...</p>
      </div>
    );
  }

  if (showCreateForm) {
    return (
      <div className="max-w-xl mx-auto px-6 py-16">
        <Toast toast={toast} />
        <div className="text-center mb-10">
          <h1 className="text-3xl font-bold text-slate-900 tracking-tight">Mulai Bisnis Anda</h1>
          <p className="text-slate-500 mt-2 text-lg">Hanya beberapa langkah untuk membuka toko pertama Anda.</p>
        </div>
        <div className="bg-white rounded-[2rem] shadow-xl shadow-slate-100 border border-slate-100 p-8 md:p-10">
          <form onSubmit={handleCreate} className="space-y-6">
            {[
              { name: 'nama_toko', label: 'Nama Toko', type: 'text', placeholder: 'Contoh: SpareHub Solo' },
              { name: 'deskripsi_toko', label: 'Deskripsi Singkat', type: 'text', placeholder: 'Apa yang anda jual?' },
              { name: 'lokasi', label: 'Alamat Lokasi', type: 'text', placeholder: 'Kota atau alamat lengkap' },
            ].map(({ name, label, type, placeholder }) => (
              <div key={name}>
                <label className="block text-sm font-semibold text-slate-700 mb-2 ml-1">{label}</label>
                <input
                  type={type}
                  value={createForm[name]}
                  placeholder={placeholder}
                  onChange={(e) => setCreateForm({ ...createForm, [name]: e.target.value })}
                  className="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all placeholder:text-slate-400"
                  required
                />
              </div>
            ))}
            <div>
              <label className="block text-sm font-semibold text-slate-700 mb-2 ml-1">Logo Toko</label>
              <div className="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-100 border-dashed rounded-2xl bg-slate-50/50 hover:bg-slate-50 transition-colors cursor-pointer relative">
                <div className="space-y-1 text-center">
                  <svg className="mx-auto h-12 w-12 text-slate-300" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                  </svg>
                  <div className="flex text-sm text-slate-600">
                    <span className="relative cursor-pointer font-medium text-indigo-600 hover:text-indigo-500">Unggah file</span>
                    <p className="pl-1">atau seret dan lepas</p>
                  </div>
                  <p className="text-xs text-slate-400">PNG, JPG, GIF hingga 10MB</p>
                </div>
                <input
                  type="file"
                  accept="image/*"
                  onChange={(e) => setCreateForm({ ...createForm, logo: e.target.files[0] })}
                  className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                  required
                />
              </div>
              {createForm.logo && <p className="mt-2 text-sm text-indigo-600 font-medium">✓ {createForm.logo.name}</p>}
            </div>
            <button type="submit" className="w-full py-4 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all active:scale-[0.98]">
              Buka Toko Sekarang
            </button>
          </form>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-6xl mx-auto px-6 py-12">
      <Toast toast={toast} />

      {/* Modern Header Toko */}
      <div className="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-slate-50 p-8 mb-12 flex flex-col md:flex-row items-center gap-8 relative overflow-hidden">
        {/* Background decorative element */}
        <div className="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-50 rounded-full opacity-50 blur-3xl"></div>
        
        <div className="relative">
          <img
            src={toko?.logo_path ? `/storage/${toko.logo_path}` : 'https://i.ibb.co.com/RkZ105G9/default-avatar.png'}
            alt="Logo Toko"
            className="w-32 h-32 rounded-[2rem] object-cover ring-4 ring-slate-50 shadow-inner"
            onError={(e) => { e.target.onerror = null; e.target.src = 'https://i.ibb.co.com/RkZ105G9/default-avatar.png'; }}
          />
        </div>

        <div className="flex-1 text-center md:text-left relative z-10">
          <div className="flex items-center justify-center md:justify-start gap-3 mb-2">
            <h1 className="text-3xl font-extrabold text-slate-900 tracking-tight">{toko?.nama_toko}</h1>
            <span className="bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-md">Official Store</span>
          </div>
          <p className="text-slate-500 text-lg leading-relaxed max-w-xl">{toko?.deskripsi_toko}</p>
          <div className="flex items-center justify-center md:justify-start gap-4 mt-4">
            <span className="flex items-center text-sm font-medium text-slate-400 bg-slate-50 px-3 py-1.5 rounded-full">
              <i className="bi bi-geo-alt me-2 text-indigo-500"></i> {toko?.lokasi}
            </span>
            <span className="flex items-center text-sm font-medium text-slate-400 bg-slate-50 px-3 py-1.5 rounded-full">
              <i className="bi bi-box-seam me-2 text-indigo-500"></i> {produkList.length} Produk
            </span>
          </div>
        </div>

        <div className="flex flex-col sm:flex-row gap-3 relative z-10 w-full md:w-auto">
          <button
            onClick={() => { setEditForm({ nama_toko: toko.nama_toko, deskripsi_toko: toko.deskripsi_toko, lokasi: toko.lokasi, logo: null }); setShowEditForm(true); }}
            className="px-6 py-3 bg-slate-50 text-slate-700 rounded-2xl text-sm font-bold hover:bg-slate-100 transition-all active:scale-95 border border-slate-100 flex items-center justify-center gap-2"
          >
            <i className="bi bi-gear-wide-connected text-lg"></i>
            Pengaturan Toko
          </button>
          <button
            onClick={() => navigate('/kelola-produk')}
            className="px-6 py-3 bg-indigo-600 text-white rounded-2xl text-sm font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center gap-2"
          >
            <i className="bi bi-plus-circle text-lg"></i>
            Kelola Produk
          </button>
        </div>
      </div>

      {/* Edit Form Modal Overlay */}
      {showEditForm && (
        <div className="fixed inset-0 z-50 flex items-center justify-center px-4 bg-slate-900/40 backdrop-blur-sm transition-all">
          <div className="bg-white rounded-[2rem] shadow-2xl w-full max-w-lg p-8 transform transition-all scale-100">
            <div className="flex justify-between items-center mb-6">
              <h2 className="text-2xl font-bold text-slate-900">Edit Profil Toko</h2>
              <button onClick={() => setShowEditForm(false)} className="text-slate-400 hover:text-slate-600">
                <i className="bi bi-x-lg"></i>
              </button>
            </div>
            <form onSubmit={handleEdit} className="space-y-5">
              {[
                { name: 'nama_toko', label: 'Nama Toko' },
                { name: 'deskripsi_toko', label: 'Deskripsi' },
                { name: 'lokasi', label: 'Lokasi' },
              ].map(({ name, label }) => (
                <div key={name}>
                  <label className="block text-sm font-semibold text-slate-700 mb-1.5 ml-1">{label}</label>
                  <input
                    type="text"
                    value={editForm[name]}
                    onChange={(e) => setEditForm({ ...editForm, [name]: e.target.value })}
                    className="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all"
                  />
                </div>
              ))}
              <div>
                <label className="block text-sm font-semibold text-slate-700 mb-1.5 ml-1">Logo Baru (opsional)</label>
                <input 
                    type="file" 
                    accept="image/*" 
                    onChange={(e) => setEditForm({ ...editForm, logo: e.target.files[0] })} 
                    className="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all"
                />
              </div>
              <div className="flex gap-3 pt-4">
                <button type="button" onClick={() => setShowEditForm(false)} className="px-4 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition-all">Batal</button>
                <button type="submit" className="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">Simpan Perubahan</button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Produk Toko Grid */}
      <div className="flex items-center justify-between mb-8">
        <h2 className="text-2xl font-bold text-slate-900 flex items-center gap-2">
          Etalase Produk
          <span className="text-slate-300 text-lg font-normal">({produkList.length})</span>
        </h2>
        <div className="h-px flex-1 bg-slate-100 mx-6 hidden sm:block"></div>
      </div>

      {produkList.length === 0 ? (
        <div className="bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 py-20 text-center">
          <div className="bg-white w-20 h-20 rounded-3xl shadow-sm flex items-center justify-center mx-auto mb-4">
            <i className="bi bi-box2 text-4xl text-slate-300"></i>
          </div>
          <p className="text-slate-500 font-medium">Belum ada produk di toko anda.</p>
          <button 
            onClick={() => navigate('/kelola-produk')}
            className="mt-4 text-indigo-600 font-bold hover:text-indigo-700 flex items-center justify-center gap-2 mx-auto"
          >
            <i className="bi bi-plus-lg"></i> Mulai Jualan Sekarang
          </button>
        </div>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
          {produkList.map((p) => (
            <div key={p.id} className="group bg-white rounded-[2rem] border border-slate-100 p-3 pb-6 hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-500 transform hover:-translate-y-2">
              <div className="relative overflow-hidden rounded-[1.5rem] aspect-square mb-5">
                <img
                  src={p.imagePath}
                  alt={p.nama}
                  className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                  onError={(e) => { e.target.onerror = null; e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f8fafc" width="200" height="200"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" fill="%23cbd5e1"%3ENo Image%3C/text%3E%3C/svg%3E'; }}
                />
                <div className="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5">
                  <i className="bi bi-stack text-indigo-500 text-[10px]"></i>
                  <p className="text-slate-900 font-bold text-xs">Stok: {p.stok}</p>
                </div>
              </div>
              <div className="px-3">
                <h3 className="font-bold text-slate-800 text-lg mb-1 truncate group-hover:text-indigo-600 transition-colors">{p.nama}</h3>
                <p className="text-indigo-600 font-extrabold text-xl mb-4">{formatRupiah(p.harga)}</p>
                <button className="w-full py-2.5 bg-slate-50 text-slate-500 rounded-xl text-xs font-bold group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-all flex items-center justify-center gap-2">
                  <i className="bi bi-eye"></i> Lihat Detail
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

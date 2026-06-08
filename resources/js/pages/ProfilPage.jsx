import { useState, useEffect } from 'react';
import { useAuth } from '../hooks/useAuth';
import api from '../services/nodeApi';
import { useToast, Toast } from '../components/Toast';

export default function ProfilPage() {
  const { user } = useAuth();
  const { toast, showToast } = useToast();
  const [form, setForm] = useState({
    name: '',
    email: '',
    phone: '',
    birthDate: '',
    gender: 'male'
  });
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});

  useEffect(() => {
    if (user?.id) {
      setLoading(true);
      api.get(`/profile/${user.id}`)
        .then((res) => {
          setForm({
            name: res.data.name || '',
            email: res.data.email || '',
            phone: res.data.phone || '',
            birthDate: res.data.birthDate || '',
            gender: res.data.gender || 'male',
          });
        })
        .catch((err) => {
          console.error("Gagal mengambil detail profil:", err);
        })
        .finally(() => setLoading(false));
    }
  }, [user]);

  async function handleSubmit(e) {
    e.preventDefault();
    setErrors({});
    setLoading(true);
    try {
      await api.patch(`/profile/${user.id}`, form);
      showToast('Profil berhasil diperbarui!', 'success');
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors || {});
      } else {
        showToast('Gagal memperbarui profil', 'error');
      }
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="max-w-2xl mx-auto px-4 py-10">
      <Toast toast={toast} />
      <h1 className="text-2xl font-bold text-gray-800 mb-6">Profil Saya</h1>

      <div className="bg-white rounded-2xl shadow p-6">
        <div className="flex items-center gap-4 mb-6">
          <img
            src={user?.pfpPath || 'https://i.ibb.co.com/RkZ105G9/default-avatar.png'}
            alt="Avatar"
            className="w-16 h-16 rounded-full object-cover border-2 border-gray-200"
            onError={(e) => { e.target.onerror = null; e.target.src = 'https://i.ibb.co.com/RkZ105G9/default-avatar.png'; }}
          />
          <div>
            <p className="font-bold text-lg text-gray-800">{form.name || user?.name}</p>
            <p className="text-gray-500 text-sm">{form.email || user?.email}</p>
          </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Nama</label>
              <input
                type="text"
                value={form.name}
                onChange={(e) => setForm({ ...form, name: e.target.value })}
                className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
                required
              />
              {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name[0]}</p>}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input
                type="email"
                value={form.email}
                onChange={(e) => setForm({ ...form, email: e.target.value })}
                className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
                required
              />
              {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email[0]}</p>}
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
              <input
                type="text"
                value={form.phone}
                onChange={(e) => setForm({ ...form, phone: e.target.value })}
                className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
              <input
                type="date"
                value={form.birthDate}
                onChange={(e) => setForm({ ...form, birthDate: e.target.value })}
                className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
            <div className="flex gap-4 mt-2">
              {['male', 'female'].map((g) => (
                <label key={g} className="flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    name="gender"
                    value={g}
                    checked={form.gender === g}
                    onChange={(e) => setForm({ ...form, gender: e.target.value })}
                    className="w-4 h-4 text-blue-600"
                  />
                  <span className="capitalize">{g === 'male' ? 'Laki-laki' : 'Perempuan'}</span>
                </label>
              ))}
            </div>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition disabled:opacity-50"
          >
            {loading ? 'Menyimpan...' : 'Simpan Perubahan'}
          </button>
        </form>
      </div>
    </div>
  );
}

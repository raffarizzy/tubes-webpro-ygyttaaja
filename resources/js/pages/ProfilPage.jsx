import { useState, useEffect } from 'react';
import { useAuth } from '../hooks/useAuth';
import api from '../services/nodeApi';
import { useToast, Toast } from '../components/Toast';

export default function ProfilPage() {
  const { user, updateUser } = useAuth();
  const { toast, showToast } = useToast();
  const [form, setForm] = useState({
    name: '',
    email: '',
    phone: '',
    birthDate: '',
    gender: 'male'
  });
  const [selectedFile, setSelectedFile] = useState(null);
  const [previewUrl, setPreviewUrl] = useState(null);
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
          if (res.data.pfpPath) {
            setPreviewUrl(res.data.pfpPath);
          }
        })
        .catch((err) => {
          console.error("Gagal mengambil detail profil:", err);
        })
        .finally(() => setLoading(false));
    }
  }, [user]);

  const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      setSelectedFile(file);
      setPreviewUrl(URL.createObjectURL(file));
    }
  };

  async function handleSubmit(e) {
    e.preventDefault();
    setErrors({});
    setLoading(true);

    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('email', form.email);
    formData.append('phone', form.phone);
    formData.append('birthDate', form.birthDate);
    formData.append('gender', form.gender);
    if (selectedFile) {
      formData.append('pfp', selectedFile);
    }

    try {
      const res = await api.patch(`/profile/${user.id}`, formData);
      
      if (res.data.success) {
        updateUser(res.data.data); 
      }

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
        <div className="flex flex-col items-center gap-4 mb-6">
          <div className="relative group">
            <img
              src={previewUrl || user?.pfpPath || 'https://i.ibb.co.com/RkZ105G9/default-avatar.png'}
              alt="Avatar"
              className="w-32 h-32 rounded-full object-cover border-4 border-blue-100"
              onError={(e) => { e.target.onerror = null; e.target.src = 'https://i.ibb.co.com/RkZ105G9/default-avatar.png'; }}
            />
            <label className="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition shadow-lg">
              <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fillRule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.172-1.172A1 1 0 009.707 3H6.293a1 1 0 00-.707.293L4.414 4.707A1 1 0 013.707 5H4zM17 13a5 5 0 11-10 0 5 5 0 0110 0z" clipRule="evenodd" />
                <path fillRule="evenodd" d="M12 13a2 2 0 11-4 0 2 2 0 014 0z" clipRule="evenodd" />
              </svg>
              <input type="file" className="hidden" accept="image/*" onChange={handleFileChange} />
            </label>
          </div>
          <div className="text-center">
            <p className="font-bold text-xl text-gray-800">{form.name || user?.name}</p>
            <p className="text-gray-500">{form.email || user?.email}</p>
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

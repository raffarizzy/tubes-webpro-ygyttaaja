import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import nodeApi from '../services/nodeApi';
import { useAuth } from '../hooks/useAuth';

export default function RegisterPage() {
  const navigate = useNavigate();
  const { setUser } = useAuth(); // Kita butuh ini untuk set user secara manual setelah register
  const [form, setForm] = useState({ name: '', email: '', password: '', password_confirmation: '', phone: ''});
  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);

  function handleChange(e) {
    setForm({ ...form, [e.target.name]: e.target.value });
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setErrors({});
    if (form.password !== form.password_confirmation) {
      setErrors({ password_confirmation: ['Password tidak cocok!'] });
      return;
    }
    setLoading(true);
    try {
      const res = await nodeApi.post('/auth/register', form);
      
      if (res.data.success) {
        // Simpan data user ke localStorage dan state global (Otomatis Login)
        localStorage.setItem('user', JSON.stringify(res.data.user));
        setUser(res.data.user);
        navigate('/');
      }
    } catch (err) {
      if (err.response?.status === 400 || err.response?.status === 422) {
        // Jika backend mengirim message tunggal
        if (err.response.data.message) {
          setErrors({ general: [err.response.data.message] });
        } else {
          setErrors(err.response.data.errors || {});
        }
      } else {
        setErrors({ general: ['Terjadi kesalahan, coba lagi.'] });
      }
    } finally {
      setLoading(false);
    }
  }

  const fields = [
    { name: 'name', label: 'Nama Lengkap', type: 'text', placeholder: 'Nama Anda' },
    { name: 'email', label: 'Email', type: 'email', placeholder: 'email@example.com' },
    { name: 'phone', label: 'Phone', type: 'text', placeholder: '0812xxxxxxxx' },
    { name: 'password', label: 'Password', type: 'password', placeholder: '••••••••' },
    { name: 'password_confirmation', label: 'Konfirmasi Password', type: 'password', placeholder: '••••••••' },
  ];

  return (
    <div className="min-h-[80vh] flex items-center justify-center bg-gray-50 px-4 py-8">
      <div className="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <div className="text-center mb-6">
          <img src="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png" alt="SpareHub" className="h-12 mx-auto mb-3" />
          <h1 className="text-2xl font-bold text-gray-800">Daftar ke SpareHub</h1>
        </div>

        {errors.general && (
          <div className="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg mb-4">
            {errors.general[0]}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          {fields.map(({ name, label, type, placeholder }) => (
            <div key={name}>
              <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
              <input
                type={type}
                name={name}
                value={form[name]}
                onChange={handleChange}
                placeholder={placeholder}
                className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
              />
              {errors[name] && (
                <p className="text-red-500 text-xs mt-1">{errors[name][0]}</p>
              )}
            </div>
          ))}
          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition disabled:opacity-50"
          >
            {loading ? 'Mendaftar...' : 'Daftar'}
          </button>
        </form>

        <p className="text-center text-sm text-gray-500 mt-6">
          Sudah punya akun?{' '}
          <Link to="/login" className="text-blue-600 font-medium hover:underline">
            Masuk di sini
          </Link>
        </p>
      </div>
    </div>
  );
}

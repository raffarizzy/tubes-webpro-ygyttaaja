import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../hooks/useAuth';
import { useCart } from '../hooks/useCart';

export default function Navbar() {
  const { user, isAuthenticated, logout } = useAuth();
  const { totalItems } = useCart();
  const navigate = useNavigate();

  async function handleLogout(e) {
    e.preventDefault();
    if (!confirm('Apakah Anda yakin ingin logout?')) return;
    await logout();
    navigate('/');
  }

  return (
    <nav className="bg-white shadow-md sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <Link to="/">
          <img
            src="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png"
            alt="SpareHub"
            className="h-10 cursor-pointer"
          />
        </Link>

        <ul className="flex items-center gap-6 list-none m-0">
          <li>
            <Link to="/" className="text-gray-700 hover:text-blue-600 font-medium">
              Beranda
            </Link>
          </li>

          <li className="relative">
            <Link to="/keranjang" className="text-gray-700 hover:text-blue-600 font-medium">
              Keranjang
              {totalItems > 0 && (
                <span className="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                  {totalItems}
                </span>
              )}
            </Link>
          </li>

          <li>
            <Link to="/toko" className="text-gray-700 hover:text-blue-600 font-medium">
              Toko Saya
            </Link>
          </li>

          <li className="flex items-center gap-2">
            {isAuthenticated ? (
              <>
                <img
                  src={user?.pfpPath || 'https://i.ibb.co.com/RkZ105G9/default-avatar.png'}
                  alt="User"
                  className="w-8 h-8 rounded-full object-cover"
                />
                <Link to="/profil" className="text-gray-700 hover:text-blue-600 font-medium">
                  {user?.name}
                </Link>
                <span className="text-gray-300">|</span>
                <button
                  onClick={handleLogout}
                  className="text-red-500 hover:text-red-700 font-medium bg-transparent border-none cursor-pointer"
                >
                  Logout
                </button>
              </>
            ) : (
              <Link to="/login" className="text-blue-600 hover:text-blue-800 font-medium">
                Login
              </Link>
            )}
          </li>
        </ul>
      </div>
    </nav>
  );
}

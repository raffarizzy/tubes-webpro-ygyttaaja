import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './context/AuthContext';
import { CartProvider } from './context/CartContext';
import Navbar from './components/Navbar';
import Footer from './components/Footer';
import ProtectedRoute from './components/ProtectedRoute';
import HomePage from './pages/HomePage';
import DetailProdukPage from './pages/DetailProdukPage';
import KeranjangPage from './pages/KeranjangPage';
import CheckoutPage from './pages/CheckoutPage';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import ProfilPage from './pages/ProfilPage';
import TokoPage from './pages/TokoPage';
import RiwayatPage from './pages/RiwayatPage';
import RatingPage from './pages/RatingPage';
import KelolaProdukPage from './pages/KelolaProdukPage';

export default function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <CartProvider>
          <div className="min-h-screen flex flex-col">
            <Navbar />
            <main className="flex-1">
              <Routes>
                <Route path="/" element={<HomePage />} />
                <Route path="/produk/:id" element={<DetailProdukPage />} />
                <Route path="/login" element={<LoginPage />} />
                <Route path="/register" element={<RegisterPage />} />
                <Route path="/keranjang" element={<KeranjangPage />} />
                <Route path="/checkout" element={
                  <ProtectedRoute><CheckoutPage /></ProtectedRoute>
                } />
                <Route path="/profil" element={
                  <ProtectedRoute><ProfilPage /></ProtectedRoute>
                } />
                <Route path="/toko" element={
                  <ProtectedRoute><TokoPage /></ProtectedRoute>
                } />
                <Route path="/riwayat" element={
                  <ProtectedRoute><RiwayatPage /></ProtectedRoute>
                } />
                <Route path="/rating" element={
                  <ProtectedRoute><RatingPage /></ProtectedRoute>
                } />
                <Route path="/kelola-produk" element={
                  <ProtectedRoute><KelolaProdukPage /></ProtectedRoute>
                } />
              </Routes>
            </main>
            <Footer />
          </div>
        </CartProvider>
      </AuthProvider>
    </BrowserRouter>
  );
}

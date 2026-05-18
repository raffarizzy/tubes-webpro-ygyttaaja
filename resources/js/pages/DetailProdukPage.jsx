import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import nodeApi from '../services/nodeApi';
import { useCart } from '../hooks/useCart';
import { useAuth } from '../hooks/useAuth';
import { useToast, Toast } from '../components/Toast';
import { formatRupiah } from '../utils/format';

export default function DetailProdukPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { addItem } = useCart();
  const { user } = useAuth();
  const { toast, showToast } = useToast();

  const [produk, setProduk] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [quantity, setQuantity] = useState(1);

  useEffect(() => {
    nodeApi.get(`/products/${id}`)
      .then((res) => {
        if (res.data.success) setProduk(res.data.data);
        else setError('Produk tidak ditemukan');
      })
      .catch(() => setError('Gagal memuat produk'))
      .finally(() => setLoading(false));
  }, [id]);

  function decreaseQty() {
    setQuantity((q) => Math.max(1, q - 1));
  }

  function increaseQty() {
    if (quantity >= produk.stok) {
      showToast(`Stok hanya tersedia ${produk.stok} item`, 'warning');
      return;
    }
    setQuantity((q) => q + 1);
  }

  function handleAddToCart() {
    const success = addItem(produk, quantity, user?.id || 1);
    if (success !== false) {
      showToast(`${quantity} ${produk.nama} berhasil ditambahkan ke keranjang!`, 'success');
      setQuantity(1);
    }
  }

  function handleBeliSekarang() {
    const checkoutData = [{
      nama: produk.nama,
      harga: produk.harga,
      hargaAsli: produk.hargaAsli || produk.harga,
      diskon: produk.diskon || 0,
      jumlah: quantity,
      imagePath: produk.imagePath,
      deskripsi: produk.deskripsi,
    }];
    localStorage.setItem('checkoutData', JSON.stringify(checkoutData));
    navigate('/checkout');
  }

  if (loading) {
    return <div className="text-center py-20 text-gray-500">Memuat produk...</div>;
  }

  if (error || !produk) {
    return (
      <div className="text-center py-20">
        <h2 className="text-xl font-semibold text-gray-700 mb-4">{error || 'Produk tidak ditemukan'}</h2>
        <button onClick={() => navigate('/')} className="text-blue-600 hover:underline">
          ← Kembali ke Beranda
        </button>
      </div>
    );
  }

  const hargaDiskon = produk.diskon > 0
    ? produk.harga - (produk.harga * (produk.diskon < 1 ? produk.diskon : produk.diskon / 100))
    : produk.harga;

  return (
    <div className="max-w-5xl mx-auto px-4 py-10">
      <Toast toast={toast} />

      <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
        {/* Gambar */}
        <div className="flex justify-center">
          <img
            src={produk.imagePath}
            alt={produk.nama}
            className="w-full max-w-sm rounded-2xl shadow-lg object-cover"
            onError={(e) => {
              e.target.onerror = null;
              e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="300" height="300"%3E%3Crect fill="%23ddd" width="300" height="300"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-size="20"%3ENo Image%3C/text%3E%3C/svg%3E';
            }}
          />
        </div>

        {/* Detail */}
        <div>
          <h1 className="text-2xl font-bold text-blue-700 mb-3">{produk.nama}</h1>

          <div className="flex items-center gap-3 mb-4">
            <span className="text-2xl font-bold text-blue-600">{formatRupiah(hargaDiskon)}</span>
            {produk.diskon > 0 && (
              <>
                <span className="text-gray-400 line-through text-base">{formatRupiah(produk.harga)}</span>
                <span className="bg-red-100 text-red-600 text-sm font-semibold px-2 py-0.5 rounded">
                  -{produk.diskon}%
                </span>
              </>
            )}
          </div>

          <div className="bg-gray-50 rounded-xl p-4 mb-4 space-y-2 text-sm">
            <p><span className="font-medium text-gray-600">Stok:</span> <span className="text-gray-800">{produk.stok} item</span></p>
            {produk.nama_toko && (
              <p><span className="font-medium text-gray-600">Toko:</span> <span className="text-gray-800">{produk.nama_toko}</span></p>
            )}
            {produk.lokasi && (
              <p><span className="font-medium text-gray-600">Lokasi:</span> <span className="text-gray-800">{produk.lokasi}</span></p>
            )}
          </div>

          <p className="text-gray-600 mb-6 leading-relaxed">{produk.deskripsi}</p>

          {/* Quantity */}
          <div className="flex items-center gap-4 mb-4">
            <span className="text-sm font-medium text-gray-600">Jumlah:</span>
            <div className="flex items-center border-2 border-gray-200 rounded-lg overflow-hidden">
              <button
                onClick={decreaseQty}
                className="px-4 py-2 text-lg font-bold text-gray-600 hover:bg-gray-100 transition"
              >
                -
              </button>
              <span className="px-4 py-2 font-semibold text-gray-800 min-w-[40px] text-center">
                {quantity}
              </span>
              <button
                onClick={increaseQty}
                className="px-4 py-2 text-lg font-bold text-gray-600 hover:bg-gray-100 transition"
              >
                +
              </button>
            </div>
          </div>

          <p className="text-sm text-gray-500 mb-6">
            Total: <span className="font-bold text-gray-800 text-base">{formatRupiah(hargaDiskon * quantity)}</span>
          </p>

          <div className="flex gap-3">
            <button
              onClick={handleAddToCart}
              className="flex-1 py-3 border-2 border-blue-600 text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition"
            >
              + Keranjang
            </button>
            <button
              onClick={handleBeliSekarang}
              className="flex-1 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition"
            >
              Beli Sekarang
            </button>
          </div>
        </div>
      </div>

      {/* Rating section placeholder */}
      <div className="mt-12">
        <h2 className="text-xl font-bold text-gray-800 mb-4">Ulasan Pembeli</h2>
        <p className="text-gray-400 text-center py-8">Belum ada ulasan untuk produk ini.</p>
      </div>
    </div>
  );
}

import { useNavigate } from 'react-router-dom';
import { useCart } from '../hooks/useCart';
import { useAuth } from '../hooks/useAuth';
import { useToast, Toast } from '../components/Toast';
import { formatRupiah } from '../utils/format';

export default function KeranjangPage() {
  const { items, updateItem, removeItem } = useCart();
  const { user } = useAuth();
  const { toast, showToast } = useToast();
  const navigate = useNavigate();

  const userId = user?.id || 1;
  const keranjangUser = items.filter((i) => i.userId === userId);

  function getHargaEfektif(item) {
    if (!item.diskon) return item.harga;
    const diskonPersen = item.diskon < 1 ? Math.round(item.diskon * 100) : item.diskon;
    return Math.round(item.harga * (1 - diskonPersen / 100));
  }

  const totalHarga = keranjangUser.reduce((sum, i) => sum + getHargaEfektif(i) * i.jumlah, 0);
  const totalItem = keranjangUser.reduce((sum, i) => sum + i.jumlah, 0);

  function handleMinus(item) {
    if (item.jumlah <= 1) return;
    updateItem(item.produkId, item.jumlah - 1, userId);
  }

  function handlePlus(item) {
    if (item.jumlah >= item.stok) {
      showToast(`Stok hanya ${item.stok} item`, 'warning');
      return;
    }
    updateItem(item.produkId, item.jumlah + 1, userId);
  }

  function handleHapus(item) {
    if (!confirm(`Hapus "${item.nama}" dari keranjang?`)) return;
    removeItem(item.produkId, userId);
    showToast(`"${item.nama}" dihapus dari keranjang`, 'info');
  }

  function handleCheckout() {
    const checkoutData = keranjangUser.map((item) => ({
      nama: item.nama,
      harga: getHargaEfektif(item),
      hargaAsli: item.hargaAsli || item.harga,
      diskon: item.diskon || 0,
      jumlah: item.jumlah,
      imagePath: item.imagePath,
      deskripsi: item.deskripsi,
    }));
    localStorage.setItem('checkoutData', JSON.stringify(checkoutData));
    navigate('/checkout');
  }

  if (keranjangUser.length === 0) {
    return (
      <div className="max-w-3xl mx-auto px-4 py-20 text-center">
        <Toast toast={toast} />
        <p className="text-xl text-gray-400 mb-4">Keranjang kosong.</p>
        <p className="text-gray-400 mb-6">Yuk belanja dulu!</p>
        <button onClick={() => navigate('/')} className="text-blue-600 hover:underline font-medium">
          ← Kembali ke Beranda
        </button>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto px-4 py-10">
      <Toast toast={toast} />
      <h1 className="text-2xl font-bold text-gray-800 mb-6">Keranjang Belanja</h1>

      <div className="space-y-4 mb-8">
        {keranjangUser.map((item) => (
          <div key={item.produkId} className="flex gap-4 bg-white rounded-xl shadow p-4 items-center">
            <img
              src={item.imagePath}
              alt={item.nama}
              className="w-24 h-24 object-cover rounded-lg flex-shrink-0"
              onError={(e) => { e.target.onerror = null; e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="96" height="96"%3E%3Crect fill="%23ddd" width="96" height="96"/%3E%3C/svg%3E'; }}
            />
            <div className="flex-1">
              <h3 className="font-semibold text-gray-800">{item.nama}</h3>
              <p className="text-blue-600 font-bold">{formatRupiah(getHargaEfektif(item))}</p>
              {item.diskon > 0 && (
                <p className="text-xs text-gray-400 line-through">{formatRupiah(item.harga)}</p>
              )}
              <p className="text-sm text-gray-500">
                Subtotal: <strong>{formatRupiah(getHargaEfektif(item) * item.jumlah)}</strong>
              </p>
            </div>
            <div className="flex items-center gap-2">
              <button
                onClick={() => handleMinus(item)}
                className="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 font-bold text-gray-700 flex items-center justify-center"
              >
                -
              </button>
              <span className="w-8 text-center font-semibold">{item.jumlah}</span>
              <button
                onClick={() => handlePlus(item)}
                className="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 font-bold text-gray-700 flex items-center justify-center"
              >
                +
              </button>
            </div>
            <button
              onClick={() => handleHapus(item)}
              className="ml-2 text-red-400 hover:text-red-600 font-medium text-sm"
            >
              Hapus
            </button>
          </div>
        ))}
      </div>

      {/* Ringkasan */}
      <div className="bg-white rounded-xl shadow p-6">
        <div className="flex justify-between text-gray-600 mb-2">
          <span>Total Item</span>
          <span className="font-semibold">{totalItem} pcs</span>
        </div>
        <div className="flex justify-between text-gray-800 text-lg font-bold border-t pt-3">
          <span>Total Harga</span>
          <span>{formatRupiah(totalHarga)}</span>
        </div>
        <button
          onClick={handleCheckout}
          className="w-full mt-4 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition"
        >
          Checkout
        </button>
      </div>
    </div>
  );
}

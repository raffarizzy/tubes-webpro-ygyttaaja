import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';
import { useCart } from '../hooks/useCart';
import { useAuth } from '../hooks/useAuth';
import { useToast, Toast } from '../components/Toast';
import { formatRupiah } from '../utils/format';

export default function CheckoutPage() {
  const { clearCart } = useCart();
  const { user } = useAuth();
  const { toast, showToast } = useToast();
  const navigate = useNavigate();

  const [checkoutData, setCheckoutData] = useState([]);
  const [alamatList, setAlamatList] = useState([]);
  const [selectedAlamat, setSelectedAlamat] = useState(null);
  const [selectedPayment, setSelectedPayment] = useState(null);
  const [showForm, setShowForm] = useState(false);
  const [editIndex, setEditIndex] = useState(null);
  const [formData, setFormData] = useState({ nama: '', alamat: '', nomor: '' });
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const data = JSON.parse(localStorage.getItem('checkoutData')) || [];
    setCheckoutData(data);
    const saved = JSON.parse(localStorage.getItem('alamatList')) || [];
    setAlamatList(saved);
  }, []);

  const totals = checkoutData.reduce((acc, item) => {
    const hargaAsli = item.hargaAsli || item.harga;
    const diskon = item.diskon || 0;
    const persen = diskon < 1 ? diskon : diskon / 100;
    const hargaFinal = diskon > 0 ? Math.round(hargaAsli - hargaAsli * persen) : item.harga;
    acc.asli += hargaAsli * item.jumlah;
    acc.final += hargaFinal * item.jumlah;
    return acc;
  }, { asli: 0, final: 0 });

  const totalDiskon = totals.asli - totals.final;

  function saveAlamat() {
    if (!formData.nama || !formData.alamat || !formData.nomor) {
      showToast('Lengkapi semua data alamat!', 'warning');
      return;
    }
    const updated = [...alamatList];
    if (editIndex !== null) {
      updated[editIndex] = formData;
    } else {
      if (updated.length >= 3) { showToast('Maksimal 3 alamat!', 'warning'); return; }
      updated.push(formData);
    }
    setAlamatList(updated);
    localStorage.setItem('alamatList', JSON.stringify(updated));
    setShowForm(false);
    setEditIndex(null);
    setFormData({ nama: '', alamat: '', nomor: '' });
    showToast('Alamat berhasil disimpan!', 'success');
  }

  function deleteAlamat() {
    if (editIndex === null || !confirm('Yakin hapus alamat ini?')) return;
    const updated = alamatList.filter((_, i) => i !== editIndex);
    setAlamatList(updated);
    localStorage.setItem('alamatList', JSON.stringify(updated));
    setShowForm(false);
    setEditIndex(null);
    if (selectedAlamat === editIndex) setSelectedAlamat(null);
    else if (selectedAlamat > editIndex) setSelectedAlamat(selectedAlamat - 1);
    showToast('Alamat dihapus', 'info');
  }

  async function handlePay() {
    if (selectedAlamat === null || !selectedPayment) {
      showToast('Pilih alamat dan metode pembayaran!', 'warning');
      return;
    }
    setLoading(true);
    try {
      const res = await api.post('/checkout/pay', { total: totals.final });
      const invoiceUrl = res.data?.invoice_url;
      if (!invoiceUrl) throw new Error('Invoice URL tidak tersedia');
      clearCart(user?.id || 1);
      localStorage.removeItem('keranjangData');
      localStorage.removeItem('checkoutData');
      window.location.href = invoiceUrl;
    } catch {
      showToast('Gagal memproses pembayaran', 'error');
      setLoading(false);
    }
  }

  if (checkoutData.length === 0) {
    return (
      <div className="max-w-3xl mx-auto px-4 py-20 text-center">
        <p className="text-xl text-gray-400 mb-4">Tidak ada produk untuk checkout.</p>
        <button onClick={() => navigate('/keranjang')} className="text-blue-600 hover:underline">
          ← Kembali ke Keranjang
        </button>
      </div>
    );
  }

  const paymentOptions = ['Transfer Bank', 'QRIS', 'OVO', 'GoPay'];

  return (
    <div className="max-w-5xl mx-auto px-4 py-10">
      <Toast toast={toast} />
      <h1 className="text-2xl font-bold text-gray-800 mb-8">Checkout</h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div className="lg:col-span-2 space-y-6">
          {/* Produk */}
          <div className="bg-white rounded-xl shadow p-5">
            <h2 className="font-semibold text-gray-800 mb-4">Produk</h2>
            {checkoutData.map((item, i) => (
              <div key={i} className="flex gap-3 mb-3">
                <img src={item.imagePath} alt={item.nama} className="w-16 h-16 object-cover rounded-lg" onError={(e) => { e.target.onerror = null; e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="64" height="64"%3E%3Crect fill="%23ddd" width="64" height="64"/%3E%3C/svg%3E'; }} />
                <div>
                  <p className="font-medium text-gray-800">{item.nama}</p>
                  <p className="text-sm text-gray-500">{item.deskripsi}</p>
                  <p className="text-blue-600 font-bold">{formatRupiah(item.harga)} × {item.jumlah}</p>
                </div>
              </div>
            ))}
          </div>

          {/* Alamat */}
          <div className="bg-white rounded-xl shadow p-5">
            <h2 className="font-semibold text-gray-800 mb-4">Alamat Pengiriman</h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
              {alamatList.map((a, i) => (
                <div
                  key={i}
                  onClick={() => setSelectedAlamat(i)}
                  className={`border-2 rounded-lg p-3 cursor-pointer transition ${
                    selectedAlamat === i ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'
                  }`}
                >
                  <div className="flex justify-between">
                    <p className="font-semibold text-sm">{a.nama}</p>
                    <button
                      onClick={(e) => { e.stopPropagation(); setEditIndex(i); setFormData(a); setShowForm(true); }}
                      className="text-blue-500 text-xs hover:underline"
                    >
                      Edit
                    </button>
                  </div>
                  <p className="text-xs text-gray-500 mt-1">{a.alamat}</p>
                  <p className="text-xs text-gray-500">{a.nomor}</p>
                </div>
              ))}
              {alamatList.length < 3 && (
                <button
                  onClick={() => { setEditIndex(null); setFormData({ nama: '', alamat: '', nomor: '' }); setShowForm(true); }}
                  className="border-2 border-dashed border-gray-300 rounded-lg p-3 text-gray-400 hover:border-gray-400 hover:text-gray-600 transition text-sm"
                >
                  + Tambah Alamat
                </button>
              )}
            </div>

            {showForm && (
              <div className="border rounded-lg p-4 bg-gray-50 mt-3 space-y-3">
                <h3 className="font-medium text-sm">{editIndex !== null ? 'Edit Alamat' : 'Alamat Baru'}</h3>
                {['nama', 'alamat', 'nomor'].map((field) => (
                  <input
                    key={field}
                    type="text"
                    placeholder={field.charAt(0).toUpperCase() + field.slice(1)}
                    value={formData[field]}
                    onChange={(e) => setFormData({ ...formData, [field]: e.target.value })}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                  />
                ))}
                <div className="flex gap-2">
                  <button onClick={saveAlamat} className="flex-1 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                    Simpan
                  </button>
                  {editIndex !== null && (
                    <button onClick={deleteAlamat} className="px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600">
                      Hapus
                    </button>
                  )}
                  <button onClick={() => { setShowForm(false); setEditIndex(null); setFormData({ nama: '', alamat: '', nomor: '' }); }} className="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300">
                    Batal
                  </button>
                </div>
              </div>
            )}
          </div>

          {/* Pembayaran */}
          <div className="bg-white rounded-xl shadow p-5">
            <h2 className="font-semibold text-gray-800 mb-4">Metode Pembayaran</h2>
            <div className="grid grid-cols-2 gap-3">
              {paymentOptions.map((opt) => (
                <div
                  key={opt}
                  onClick={() => setSelectedPayment(opt)}
                  className={`border-2 rounded-lg p-3 cursor-pointer text-sm font-medium text-center transition ${
                    selectedPayment === opt ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 hover:border-gray-300'
                  }`}
                >
                  {opt}
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Ringkasan */}
        <div className="bg-white rounded-xl shadow p-5 h-fit sticky top-24">
          <h2 className="font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h2>
          <div className="space-y-2 text-sm">
            <div className="flex justify-between text-gray-600">
              <span>Subtotal</span><span>{formatRupiah(totals.asli)}</span>
            </div>
            <div className="flex justify-between text-gray-600">
              <span>Pengiriman</span><span className="text-green-600 font-medium">Gratis</span>
            </div>
            {totalDiskon > 0 && (
              <div className="flex justify-between text-red-500">
                <span>Diskon</span><span>- {formatRupiah(totalDiskon)}</span>
              </div>
            )}
            <div className="flex justify-between font-bold text-gray-800 text-base border-t pt-3">
              <span>Total</span><span>{formatRupiah(totals.final)}</span>
            </div>
          </div>
          <button
            onClick={handlePay}
            disabled={loading || selectedAlamat === null || !selectedPayment}
            className="w-full mt-5 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {loading ? 'Memproses...' : 'Bayar Sekarang'}
          </button>
        </div>
      </div>
    </div>
  );
}

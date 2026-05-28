import { useState, useEffect } from 'react';
import { useToast, Toast } from '../components/Toast';

export default function RatingPage() {
  const { toast, showToast } = useToast();
  const [ratingList, setRatingList] = useState([]);
  const [produkList, setProdukList] = useState([]);
  const [selectedProduk, setSelectedProduk] = useState('');
  const [rating, setRating] = useState(5);
  const [komentar, setKomentar] = useState('');

  useEffect(() => {
    const saved = JSON.parse(localStorage.getItem('ratingList')) || [];
    setRatingList(saved);
    const pesanan = JSON.parse(localStorage.getItem('pesananList')) || [];
    const unik = [...new Map(pesanan.map((p) => [p.produkId, p])).values()];
    setProdukList(unik.filter((p) => !saved.some((r) => r.produkId === p.produkId)));
  }, []);

  function handleSubmit(e) {
    e.preventDefault();
    if (!selectedProduk) { showToast('Pilih produk!', 'warning'); return; }
    const newRating = {
      id: Date.now(),
      produkId: Number(selectedProduk),
      rating,
      komentar,
      tanggal: new Date().toISOString().split('T')[0],
    };
    const updated = [...ratingList, newRating];
    setRatingList(updated);
    localStorage.setItem('ratingList', JSON.stringify(updated));
    setSelectedProduk('');
    setRating(5);
    setKomentar('');
    showToast('Rating berhasil ditambahkan!', 'success');
  }

  function handleDelete(id) {
    if (!confirm('Hapus rating ini?')) return;
    const updated = ratingList.filter((r) => r.id !== id);
    setRatingList(updated);
    localStorage.setItem('ratingList', JSON.stringify(updated));
    const pesanan = JSON.parse(localStorage.getItem('pesananList')) || [];
    const unik = [...new Map(pesanan.map((p) => [p.produkId, p])).values()];
    setProdukList(unik.filter((p) => !updated.some((r) => r.produkId === p.produkId)));
    showToast('Rating dihapus', 'info');
  }

  return (
    <div className="max-w-3xl mx-auto px-4 py-10">
      <Toast toast={toast} />
      <h1 className="text-2xl font-bold text-gray-800 mb-6">Rating & Ulasan</h1>

      {produkList.length > 0 && (
        <div className="bg-white rounded-2xl shadow p-6 mb-6">
          <h2 className="font-semibold text-gray-800 mb-4">Tambah Ulasan</h2>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Produk</label>
              <select
                value={selectedProduk}
                onChange={(e) => setSelectedProduk(e.target.value)}
                className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
              >
                <option value="">-- Pilih Produk --</option>
                {produkList.map((p) => (
                  <option key={p.produkId} value={p.produkId}>{p.nama}</option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Rating</label>
              <div className="flex gap-2">
                {[1, 2, 3, 4, 5].map((n) => (
                  <button
                    key={n}
                    type="button"
                    onClick={() => setRating(n)}
                    className={`text-2xl transition ${n <= rating ? 'text-yellow-400' : 'text-gray-300'}`}
                  >
                    ★
                  </button>
                ))}
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Komentar</label>
              <textarea
                value={komentar}
                onChange={(e) => setKomentar(e.target.value)}
                rows={3}
                placeholder="Tulis ulasan Anda..."
                className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none resize-none"
              />
            </div>
            <button type="submit" className="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700">
              Kirim Ulasan
            </button>
          </form>
        </div>
      )}

      <div className="space-y-4">
        {ratingList.map((r) => (
          <div key={r.id} className="bg-white rounded-xl shadow p-4 flex gap-4">
            <div className="flex-1">
              <div className="flex items-center gap-2 mb-1">
                <span className="text-yellow-400 text-lg">{'★'.repeat(r.rating)}{'☆'.repeat(5 - r.rating)}</span>
              </div>
              <p className="text-gray-700">{r.komentar}</p>
              <p className="text-xs text-gray-400 mt-1">{r.tanggal}</p>
            </div>
            <button
              onClick={() => handleDelete(r.id)}
              className="text-red-400 hover:text-red-600 text-sm self-start"
            >
              Hapus
            </button>
          </div>
        ))}
        {ratingList.length === 0 && (
          <p className="text-gray-400 text-center py-8">Belum ada ulasan.</p>
        )}
      </div>
    </div>
  );
}

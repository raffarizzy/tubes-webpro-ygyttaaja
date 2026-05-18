import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { formatRupiah } from '../utils/format';

export default function RiwayatPage() {
  const [pesananList, setPesananList] = useState([]);

  useEffect(() => {
    const data = JSON.parse(localStorage.getItem('pesananList')) || [];
    setPesananList(data);
  }, []);

  if (pesananList.length === 0) {
    return (
      <div className="max-w-3xl mx-auto px-4 py-20 text-center">
        <p className="text-xl text-gray-400 mb-4">Belum ada riwayat pesanan.</p>
        <Link to="/" className="text-blue-600 hover:underline">← Belanja Sekarang</Link>
      </div>
    );
  }

  const statusColors = {
    selesai: 'bg-green-100 text-green-700',
    proses: 'bg-yellow-100 text-yellow-700',
    dibatalkan: 'bg-red-100 text-red-700',
  };

  return (
    <div className="max-w-3xl mx-auto px-4 py-10">
      <h1 className="text-2xl font-bold text-gray-800 mb-6">Riwayat Pesanan</h1>
      <div className="space-y-4">
        {pesananList.map((pesanan, i) => (
          <div key={i} className="bg-white rounded-xl shadow p-4 flex gap-4">
            <img
              src={pesanan.imagePath || ''}
              alt={pesanan.nama}
              className="w-20 h-20 object-cover rounded-lg flex-shrink-0"
              onError={(e) => { e.target.onerror = null; e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="80" height="80"%3E%3Crect fill="%23ddd" width="80" height="80"/%3E%3C/svg%3E'; }}
            />
            <div className="flex-1">
              <h3 className="font-semibold text-gray-800">{pesanan.nama || 'Produk'}</h3>
              <p className="text-sm text-gray-500">Jumlah: {pesanan.jumlah}</p>
              <p className="text-sm text-gray-500">Total: {formatRupiah(pesanan.totalHarga || 0)}</p>
              <p className="text-sm text-gray-500">Tanggal: {pesanan.tanggal}</p>
              <p className="text-xs text-gray-400">{pesanan.alamatPengiriman}</p>
            </div>
            <div className="flex flex-col items-end gap-2">
              <span className={`text-xs font-medium px-2 py-1 rounded-full ${statusColors[pesanan.status] || 'bg-gray-100 text-gray-600'}`}>
                {pesanan.status}
              </span>
              {pesanan.status === 'selesai' && (
                <Link to="/rating" className="text-blue-600 text-xs hover:underline">Review</Link>
              )}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

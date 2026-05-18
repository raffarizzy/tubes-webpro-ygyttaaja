import { Link } from 'react-router-dom';
import { formatRupiah } from '../utils/format';

export default function ProdukCard({ produk }) {
  return (
    <Link to={`/produk/${produk.id}`} className="no-underline">
      <div className="bg-white rounded-xl shadow hover:shadow-lg transition-shadow cursor-pointer overflow-hidden h-full flex flex-col">
        <img
          src={produk.imagePath}
          alt={produk.nama}
          className="w-full h-48 object-cover"
          onError={(e) => {
            e.target.src =
              'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23ddd" width="200" height="200"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-size="16"%3ENo Image%3C/text%3E%3C/svg%3E';
          }}
        />
        <div className="p-4 flex flex-col flex-1">
          <h3 className="font-semibold text-gray-800 text-sm mb-1 line-clamp-2">{produk.nama}</h3>
          <p className="text-blue-600 font-bold text-base mt-auto">{formatRupiah(produk.harga)}</p>
          {produk.diskon > 0 && (
            <span className="text-xs text-red-500 font-medium">Diskon {produk.diskon}%</span>
          )}
          <p className="text-xs text-gray-400 mt-1">Stok: {produk.stok}</p>
        </div>
      </div>
    </Link>
  );
}

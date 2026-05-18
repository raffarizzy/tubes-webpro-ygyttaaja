import { useState, useEffect, useMemo } from 'react';
import nodeApi from '../services/nodeApi';
import ProdukCard from '../components/ProdukCard';

const ITEMS_PER_PAGE = 9;

export default function HomePage() {
  const [produkList, setProdukList] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [search, setSearch] = useState('');
  const [priceMin, setPriceMin] = useState('');
  const [priceMax, setPriceMax] = useState('');
  const [currentPage, setCurrentPage] = useState(1);

  useEffect(() => {
    nodeApi.get('/products')
      .then((res) => setProdukList(res.data.success ? res.data.data : []))
      .catch(() => setError('Gagal memuat produk. Pastikan Node.js API berjalan.'))
      .finally(() => setLoading(false));
  }, []);

  const filtered = useMemo(() => {
    return produkList.filter((p) => {
      const matchSearch = p.nama.toLowerCase().includes(search.toLowerCase());
      const matchMin = priceMin === '' || p.harga >= Number(priceMin);
      const matchMax = priceMax === '' || p.harga <= Number(priceMax);
      return matchSearch && matchMin && matchMax;
    });
  }, [produkList, search, priceMin, priceMax]);

  const totalPages = Math.ceil(filtered.length / ITEMS_PER_PAGE);
  const paginated = filtered.slice(
    (currentPage - 1) * ITEMS_PER_PAGE,
    currentPage * ITEMS_PER_PAGE
  );

  function handleFilter(e) {
    e.preventDefault();
    setCurrentPage(1);
  }

  function handleReset() {
    setSearch('');
    setPriceMin('');
    setPriceMax('');
    setCurrentPage(1);
  }

  return (
    <div>
      {/* Hero */}
      <section className="bg-blue-600 text-white text-center py-20 px-4">
        <h1 className="text-4xl font-bold mb-3">
          Selamat Datang di <span className="text-yellow-300">SpareHub</span>
        </h1>
        <p className="text-lg mb-6">Tempat terbaik untuk mencari suku cadang kendaraan Anda!</p>
        <button
          onClick={() => document.getElementById('produk-section').scrollIntoView({ behavior: 'smooth' })}
          className="bg-white text-blue-600 font-semibold px-6 py-3 rounded-full hover:bg-blue-50 transition"
        >
          Jelajahi Produk
        </button>
      </section>

      {/* Search & Filter */}
      <section className="max-w-6xl mx-auto px-4 mt-10">
        <form onSubmit={handleFilter} className="flex flex-wrap gap-4 items-end">
          <div className="flex-1 min-w-[200px]">
            <label className="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
            <input
              type="text"
              value={search}
              onChange={(e) => { setSearch(e.target.value); setCurrentPage(1); }}
              placeholder="Cari nama produk..."
              className="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
            />
          </div>
          <div className="min-w-[130px]">
            <label className="block text-sm font-medium text-gray-700 mb-1">Harga Min</label>
            <input
              type="number"
              value={priceMin}
              onChange={(e) => { setPriceMin(e.target.value); setCurrentPage(1); }}
              placeholder="Rp 0"
              className="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
            />
          </div>
          <div className="min-w-[130px]">
            <label className="block text-sm font-medium text-gray-700 mb-1">Harga Max</label>
            <input
              type="number"
              value={priceMax}
              onChange={(e) => { setPriceMax(e.target.value); setCurrentPage(1); }}
              placeholder="Rp ∞"
              className="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
            />
          </div>
          <button
            type="submit"
            className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
          >
            Filter
          </button>
          <button
            type="button"
            onClick={handleReset}
            className="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium"
          >
            Reset
          </button>
        </form>
      </section>

      {/* Produk Grid */}
      <section id="produk-section" className="max-w-6xl mx-auto px-4 py-10">
        <h2 className="text-2xl font-bold text-gray-800 mb-6">Produk Tersedia</h2>

        {loading && (
          <div className="text-center py-20 text-gray-500">Memuat produk...</div>
        )}

        {error && (
          <div className="text-center py-10 text-red-500">{error}</div>
        )}

        {!loading && !error && paginated.length === 0 && (
          <div className="text-center py-20 text-gray-400">
            <p className="text-lg">Tidak ada produk ditemukan.</p>
          </div>
        )}

        {!loading && !error && paginated.length > 0 && (
          <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {paginated.map((produk) => (
                <ProdukCard key={produk.id} produk={produk} />
              ))}
            </div>

            {/* Pagination */}
            {totalPages > 1 && (
              <div className="flex justify-center gap-2 mt-8">
                {Array.from({ length: totalPages }, (_, i) => i + 1).map((page) => (
                  <button
                    key={page}
                    onClick={() => {
                      setCurrentPage(page);
                      document.getElementById('produk-section')?.scrollIntoView({ behavior: 'smooth' });
                    }}
                    className={`w-9 h-9 rounded-full font-medium transition ${
                      page === currentPage
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    }`}
                  >
                    {page}
                  </button>
                ))}
              </div>
            )}
          </>
        )}
      </section>
    </div>
  );
}

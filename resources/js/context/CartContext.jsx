import { createContext, useState, useEffect } from 'react';

export const CartContext = createContext(null);

export function CartProvider({ children }) {
  const [items, setItems] = useState(() => {
    try {
      return JSON.parse(localStorage.getItem('keranjangData')) || [];
    } catch {
      return [];
    }
  });

  useEffect(() => {
    localStorage.setItem('keranjangData', JSON.stringify(items));
  }, [items]);

  function addItem(produk, jumlah, userId = 1) {
    const existing = items.find((i) => i.produkId === produk.id && i.userId === userId);
    if (existing && existing.jumlah + jumlah > produk.stok) return false;
    if (!existing && jumlah > produk.stok) return false;

    setItems((prev) => {
      const idx = prev.findIndex(
        (i) => i.produkId === produk.id && i.userId === userId
      );
      if (idx !== -1) {
        const updated = [...prev];
        updated[idx] = { ...updated[idx], jumlah: updated[idx].jumlah + jumlah };
        return updated;
      }
      return [
        ...prev,
        {
          userId,
          produkId: produk.id,
          nama: produk.nama,
          harga: produk.harga,
          hargaAsli: produk.hargaAsli || produk.harga,
          diskon: produk.diskon || 0,
          jumlah,
          imagePath: produk.imagePath,
          deskripsi: produk.deskripsi,
          stok: produk.stok,
        },
      ];
    });
    return true;
  }

  function updateItem(produkId, jumlah, userId = 1) {
    setItems((prev) =>
      prev.map((i) =>
        i.produkId === produkId && i.userId === userId
          ? { ...i, jumlah }
          : i
      )
    );
  }

  function removeItem(produkId, userId = 1) {
    setItems((prev) =>
      prev.filter((i) => !(i.produkId === produkId && i.userId === userId))
    );
  }

  function clearCart(userId = 1) {
    setItems((prev) => prev.filter((i) => i.userId !== userId));
  }

  const totalItems = items
    .filter((i) => i.userId === 1)
    .reduce((sum, i) => sum + i.jumlah, 0);

  return (
    <CartContext.Provider value={{ items, addItem, updateItem, removeItem, clearCart, totalItems }}>
      {children}
    </CartContext.Provider>
  );
}

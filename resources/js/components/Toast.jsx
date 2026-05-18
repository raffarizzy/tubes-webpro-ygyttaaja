import { useState, useEffect } from 'react';

export function useToast() {
  const [toast, setToast] = useState(null);

  function showToast(message, type = 'success') {
    setToast({ message, type });
    setTimeout(() => setToast(null), 3000);
  }

  return { toast, showToast };
}

export function Toast({ toast }) {
  if (!toast) return null;

  const colors = {
    success: 'bg-green-500 text-white',
    warning: 'bg-yellow-400 text-black',
    error: 'bg-red-500 text-white',
    info: 'bg-blue-500 text-white',
  };

  return (
    <div
      className={`fixed top-20 right-5 z-[10000] px-5 py-4 rounded-lg shadow-lg font-medium max-w-xs animate-slide-in ${colors[toast.type] || colors.success}`}
    >
      {toast.message}
    </div>
  );
}

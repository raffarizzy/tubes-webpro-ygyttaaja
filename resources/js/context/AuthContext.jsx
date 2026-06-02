import { createContext, useState, useEffect } from 'react';
import api from '../services/api';
import nodeApi from '../services/nodeApi';

export const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(() => {
    const savedUser = localStorage.getItem('user');
    return savedUser ? JSON.parse(savedUser) : null;
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get(nodeApi.get('/auth/me'))
      .then((res) => {
        console.log("Data dari server:", res.data); // LIHAT DI CONSOLE F12
        if (res.data.success) {
          setUser(res.data);
          localStorage.setItem('user', JSON.stringify(res.data.user));
        }
      })
      .catch((e) => {
        console.error("Gagal mengambil data user: ", e);
        setUser(null);
        localStorage.removeItem('user');
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  async function login(email, password) {
    const res = await api.post('http://localhost:3001/api/auth/login', { email, password });

    if (res.data.success) {
      localStorage.setItem('user', JSON.stringify(res.data.user));
      setUser(res.data.user);
    }
  }

  async function logout() {
    try {
      await api.post('http://localhost:3001/api/auth/logout');
      localStorage.removeItem('user');
      setUser(null);
    } catch (e) {
      console.error("Gagal logout : ", e);
    }
  }

  return (
    <AuthContext.Provider value={{ user, loading, isAuthenticated: !!user, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

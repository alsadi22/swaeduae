import React, { createContext, useState, useEffect } from 'react';
import i18n from '../i18n/i18n';

export const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch('/api/user', { credentials: 'include' })
      .then(res => res.ok ? res.json() : Promise.reject())
      .then(data => setUser(data))
      .catch(() => setUser(null))
      .finally(() => setLoading(false));
  }, []);

  const login = async (email, password) => {
    const res = await fetch('/api/login', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      credentials: 'include',
      body: JSON.stringify({ email, password })
    });
    if (!res.ok) throw new Error('Login failed');
    const data = await res.json();
    setUser(data.user);
  };

  const logout = async () => {
    await fetch('/api/logout', { method: 'POST', credentials: 'include' });
    setUser(null);
  };

  const changeLanguage = (lng) => {
    i18n.changeLanguage(lng);
  };

  return (
    <AuthContext.Provider value={{ user, loading, login, logout, changeLanguage, currentLang: i18n.language }}>
      {children}
    </AuthContext.Provider>
  );
};

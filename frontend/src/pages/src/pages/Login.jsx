import React, { useState, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';
import { useTranslation } from 'react-i18next';

export default function Login() {
  const { login } = useContext(AuthContext);
  const { t } = useTranslation();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState(null);
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await login(email, password);
      navigate('/dashboard');
    } catch {
      setError(t('error'));
    }
  };

  return (
    <div className="max-w-md mx-auto p-8 mt-16 border rounded shadow" dir="ltr">
      <h2 className="text-2xl font-bold mb-4">{t('login')}</h2>
      {error && <div className="mb-4 text-red-600">{error}</div>}
      <form onSubmit={handleSubmit} className="space-y-4">
        <input type="email" placeholder={t('email')} value={email} onChange={e => setEmail(e.target.value)} required className="w-full p-2 border rounded" />
        <input type="password" placeholder={t('password')} value={password} onChange={e => setPassword(e.target.value)} required className="w-full p-2 border rounded" />
        <button type="submit" className="w-full bg-blue-700 text-white py-2 rounded hover:bg-blue-800">{t('login')}</button>
      </form>
    </div>
  );
}

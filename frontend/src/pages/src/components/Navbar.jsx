import React, { useContext } from 'react';
import { Link } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';
import { useTranslation } from 'react-i18next';

export default function Navbar() {
  const { user, logout, changeLanguage, currentLang } = useContext(AuthContext);
  const { t } = useTranslation();

  const toggleLang = () => {
    changeLanguage(currentLang === 'en' ? 'ar' : 'en');
  };

  return (
    <nav className="bg-blue-700 text-white p-4 flex justify-between items-center" dir={currentLang === 'ar' ? 'rtl' : 'ltr'}>
      <div className="space-x-4">
        <Link to="/" className="font-bold">{t('home')}</Link>
        {user ? (
          <>
            <Link to="/dashboard" className="hover:underline">{t('dashboard')}</Link>
            <Link to="/events" className="hover:underline">{t('events')}</Link>
            <Link to="/profile" className="hover:underline">{t('profile')}</Link>
            <Link to="/certificates" className="hover:underline">{t('certificates')}</Link>
            {user.role === 'admin' && <Link to="/admin" className="hover:underline">{t('admin')}</Link>}
            <button onClick={logout} className="ml-4 hover:underline">{t('logout')}</button>
          </>
        ) : (
          <Link to="/login" className="hover:underline">{t('login')}</Link>
        )}
      </div>
      <button onClick={toggleLang} className="font-bold border px-2 rounded">{currentLang === 'en' ? t('arabic') : t('english')}</button>
    </nav>
  );
}

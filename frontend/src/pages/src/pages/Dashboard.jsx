import React, { useContext } from 'react';
import { AuthContext } from '../context/AuthContext';
import { useTranslation } from 'react-i18next';

export default function Dashboard() {
  const { user } = useContext(AuthContext);
  const { t } = useTranslation();

  return (
    <div className="p-8" dir={user?.lang === 'ar' ? 'rtl' : 'ltr'}>
      <h2 className="text-2xl font-bold">{t('welcome')}, {user?.name || user?.email}</h2>
      <p>{t('dashboard')}</p>
    </div>
  );
}

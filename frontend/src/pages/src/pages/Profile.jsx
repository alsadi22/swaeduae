import React, { useEffect, useState, useContext } from 'react';
import { AuthContext } from '../context/AuthContext';
import { useTranslation } from 'react-i18next';

export default function Profile() {
  const { t } = useTranslation();
  const { user } = useContext(AuthContext);
  const [name, setName] = useState(user?.name || '');
  const [email, setEmail] = useState(user?.email || '');
  const [kycFile, setKycFile] = useState(null);
  const [message, setMessage] = useState(null);

  const handleSubmit = (e) => {
    e.preventDefault();
    fetch('/api/profile', {
      method: 'PUT',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, email }),
    })
    .then(res => res.ok ? setMessage(t('submit_success')) : setMessage(t('submit_error')));
  };

  const handleKycUpload = () => {
    if (!kycFile) return;
    const formData = new FormData();
    formData.append('file', kycFile);
    fetch('/api/profile/upload-kyc', {
      method: 'POST',
      credentials: 'include',
      body: formData,
    })
    .then(res => res.ok ? setMessage(t('submit_success')) : setMessage(t('submit_error')));
  };

  return (
    <div className="max-w-md mx-auto p-8 mt-8 border rounded shadow" dir={user?.lang === 'ar' ? 'rtl' : 'ltr'}>
      <h2 className="text-2xl font-bold mb-4">{t('profile')}</h2>
      {message && <div className="mb-4 text-green-600">{message}</div>}
      <form onSubmit={handleSubmit} className="space-y-4">
        <input type="text" placeholder={t('name')} value={name} onChange={e => setName(e.target.value)} className="w-full p-2 border rounded" />
        <input type="email" placeholder={t('email')} value={email} onChange={e => setEmail(e.target.value)} className="w-full p-2 border rounded" />
        <button type="submit" className="w-full bg-blue-700 text-white py-2 rounded hover:bg-blue-800">{t('submit')}</button>
      </form>
      <div className="mt-6">
        <label className="block mb-2">{t('kyc_upload')}</label>
        <input type="file" onChange={e => setKycFile(e.target.files[0])} className="w-full" />
        <button onClick={handleKycUpload} className="mt-2 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">{t('submit')}</button>
      </div>
    </div>
  );
}

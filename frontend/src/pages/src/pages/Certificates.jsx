import React, { useEffect, useState, useContext } from 'react';
import { AuthContext } from '../context/AuthContext';
import { useTranslation } from 'react-i18next';

export default function Certificates() {
  const { t } = useTranslation();
  const { user } = useContext(AuthContext);
  const [certificates, setCertificates] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch('/api/certificates', { credentials: 'include' })
      .then(res => res.json())
      .then(data => {
        setCertificates(data);
        setLoading(false);
      });
  }, []);

  if (loading) return <div>{t('loading')}</div>;
  if (!certificates.length) return <div>{t('no_certificates')}</div>;

  return (
    <div className="p-8" dir={user?.lang === 'ar' ? 'rtl' : 'ltr'}>
      <h2 className="text-2xl font-bold mb-4">{t('certificates')}</h2>
      <ul>
        {certificates.map(cert => (
          <li key={cert.id} className="border p-4 mb-2 rounded flex justify-between items-center">
            <div>
              <p>{cert.event_id}</p>
              <p>{new Date(cert.created_at).toLocaleDateString()}</p>
            </div>
            <a href={`/api/certificates/download/${cert.id}`} target="_blank" rel="noreferrer" className="px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800">
              {t('certificate_download')}
            </a>
          </li>
        ))}
      </ul>
    </div>
  );
}

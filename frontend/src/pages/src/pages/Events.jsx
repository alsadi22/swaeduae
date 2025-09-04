import React, { useEffect, useState, useContext } from 'react';
import { AuthContext } from '../context/AuthContext';
import { useTranslation } from 'react-i18next';

export default function Events() {
  const { t } = useTranslation();
  const { user } = useContext(AuthContext);
  const [events, setEvents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [message, setMessage] = useState(null);

  useEffect(() => {
    fetch('/api/events', { credentials: 'include' })
      .then(res => res.json())
      .then(data => {
        setEvents(data);
        setLoading(false);
      });
  }, []);

  const registerForEvent = (eventId) => {
    fetch('/api/event_registrations', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ event_id: eventId }),
    }).then(res => {
      if(res.ok) {
        setMessage(t('submit_success'));
      } else {
        setMessage(t('submit_error'));
      }
    });
  };

  if (loading) return <div>{t('loading')}</div>;
  if (!events.length) return <div>{t('no_events')}</div>;

  return (
    <div className="p-8" dir={user?.lang === 'ar' ? 'rtl' : 'ltr'}>
      <h2 className="text-2xl font-bold mb-4">{t('events')}</h2>
      {message && <div className="mb-4 text-green-600">{message}</div>}
      <ul>
        {events.map(event => (
          <li key={event.id} className="border p-4 mb-2 rounded">
            <h3 className="font-bold">{event.title}</h3>
            <p>{event.description}</p>
            <p>{new Date(event.date).toLocaleDateString()}</p>
            <button
              onClick={() => registerForEvent(event.id)}
              className="mt-2 px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800"
            >
              {t('event_registration')}
            </button>
          </li>
        ))}
      </ul>
    </div>
  );
}

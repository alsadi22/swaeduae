#!/bin/bash

echo "Creating modern React + Tailwind frontend scaffold..."

# package.json
cat << 'EOPKG' > package.json
{
  "name": "swaeduae-frontend",
  "version": "1.0.0",
  "private": true,
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview"
  },
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0",
    "react-router-dom": "^6.11.2",
    "i18next": "^23.4.6",
    "react-i18next": "^13.0.2"
  },
  "devDependencies": {
    "autoprefixer": "^10.4.14",
    "postcss": "^8.4.24",
    "tailwindcss": "^3.3.2",
    "vite": "^4.3.9",
    "@vitejs/plugin-react": "^4.0.0"
  }
}
EOPKG

# tailwind.config.js
cat << 'EOTAILWIND' > tailwind.config.js
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./index.html",
    "./src/**/*.{js,jsx}"
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
EOTAILWIND

# postcss.config.js
cat << 'EOPC' > postcss.config.js
module.exports = {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
}
EOPC

# vite.config.js
cat << 'EOVITE' > vite.config.js
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    port: 3000,
  },
  base: '/',
})
EOVITE

# index.html
cat << 'EOHTML' > index.html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Swaed UAE</title>
  <script type="module" crossorigin src="/src/main.jsx"></script>
  <link href="/src/index.css" rel="stylesheet" />
</head>
<body class="bg-gray-50">
  <div id="root"></div>
</body>
</html>
EOHTML

mkdir -p src/components src/pages src/context src/services src/i18n

# src/index.css
cat << 'EOCSS' > src/index.css
@tailwind base;
@tailwind components;
@tailwind utilities;
EOCSS

# src/main.jsx
cat << 'EOMAIN' > src/main.jsx
import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App'
import './index.css'
import './i18n/i18n'

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
)
EOMAIN

# src/i18n/i18n.js
cat << 'EOI18N' > src/i18n/i18n.js
import i18n from 'i18next'
import { initReactI18next } from 'react-i18next'

const resources = {
  en: {
    translation: {
      welcome: "Welcome",
      home: "Home",
      login: "Login",
      register: "Register",
      dashboard: "Dashboard",
      logout: "Logout",
      messages: "Messages",
      profile: "Profile",
      events: "Events",
      loading: "Loading...",
      arabic: "Arabic",
      english: "English"
    }
  },
  ar: {
    translation: {
      welcome: "مرحبا",
      home: "الرئيسية",
      login: "تسجيل الدخول",
      register: "إنشاء حساب",
      dashboard: "لوحة التحكم",
      logout: "تسجيل خروج",
      messages: "الرسائل",
      profile: "الملف الشخصي",
      events: "الفعاليات",
      loading: "جاري التحميل...",
      arabic: "العربية",
      english: "الإنجليزية"
    }
  }
}

i18n.use(initReactI18next).init({
  resources,
  lng: "en",
  fallbackLng: "en",
  interpolation: {
    escapeValue: false
  }
})

export default i18n
EOI18N

# src/context/AuthContext.jsx
cat << 'EOAUTH' > src/context/AuthContext.jsx
import React, { createContext, useState, useEffect } from 'react'

export const AuthContext = createContext()

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null)

  // TODO: Replace with real auth check / token validation
  useEffect(() => {
    const storedUser = localStorage.getItem('user')
    if (storedUser) {
      setUser(JSON.parse(storedUser))
    }
  }, [])

  const login = (userData) => {
    localStorage.setItem('user', JSON.stringify(userData))
    setUser(userData)
  }

  const logout = () => {
    localStorage.removeItem('user')
    setUser(null)
  }

  return (
    <AuthContext.Provider value={{ user, login, logout }}>
      {children}
    </AuthContext.Provider>
  )
}
EOAUTH

# src/services/api.js
cat << 'EOAPI' > src/services/api.js
const API_BASE = import.meta.env.VITE_API_BASE_URL || 'https://swaeduae.ae'

export async function login(credentials) {
  // TODO: Implement real API call
  // Example:
  // const res = await fetch(`${API_BASE}/login`, { method: 'POST', body: JSON.stringify(credentials) })
  // return await res.json()

  // Fake delay & user
  return new Promise(resolve => setTimeout(() => {
    resolve({ id: 1, name: "HAMAD ALSHEHYARI", token: "fake-jwt-token" })
  }, 500))
}

export async function logout() {
  // TODO: Implement real API logout if needed
  return Promise.resolve()
}

export async function fetchEvents() {
  // TODO: Implement real API call
  return Promise.resolve([
    { id: 1, title: "Blood Donation Camp", date: "2025-08-15" },
    { id: 2, title: "Beach Cleanup", date: "2025-09-05" },
  ])
}
EOAPI

# src/components/Navbar.jsx
cat << 'EONAV' > src/components/Navbar.jsx
import React, { useContext } from 'react'
import { Link } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { AuthContext } from '../context/AuthContext'

export default function Navbar() {
  const { t, i18n } = useTranslation()
  const { user, logout } = useContext(AuthContext)

  const toggleLang = () => {
    const newLng = i18n.language === 'en' ? 'ar' : 'en'
    i18n.changeLanguage(newLng)
  }

  return (
    <nav className="bg-blue-700 text-white p-4 flex justify-between">
      <div className="space-x-4">
        <Link to="/" className="font-bold">{t('home')}</Link>
        {user ? (
          <>
            <Link to="/messages" className="hover:underline">{t('messages')}</Link>
            <Link to="/dashboard" className="hover:underline">{t('dashboard')}</Link>
            <button onClick={logout} className="hover:underline">{t('logout')}</button>
          </>
        ) : (
          <>
            <Link to="/login" className="hover:underline">{t('login')}</Link>
            <Link to="/register" className="hover:underline">{t('register')}</Link>
          </>
        )}
      </div>
      <button onClick={toggleLang} className="font-bold">
        {i18n.language === 'en' ? t('arabic') : t('english')}
      </button>
    </nav>
  )
}
EONAV

# src/pages/Home.jsx
cat << 'EOHOME' > src/pages/Home.jsx
import React from 'react'
import { useTranslation } from 'react-i18next'

export default function Home() {
  const { t } = useTranslation()
  return (
    <div className="p-8 text-center">
      <h1 className="text-3xl font-bold">{t('welcome')}</h1>
      <p className="mt-4 text-gray-700">Volunteer and make a difference in your community!</p>
    </div>
  )
}
EOHOME

# src/pages/Login.jsx
cat << 'EOLOGIN' > src/pages/Login.jsx
import React, { useState, useContext } from 'react'
import { useNavigate } from 'react-router-dom'
import { useTranslation } from 'react-i18next'
import { login } from '../services/api'
import { AuthContext } from '../context/AuthContext'

export default function Login() {
  const { t } = useTranslation()
  const { login: doLogin } = useContext(AuthContext)
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState(null)
  const [loading, setLoading] = useState(false)
  const navigate = useNavigate()

  const handleSubmit = async (e) => {
    e.preventDefault()
    setLoading(true)
    setError(null)
    try {
      const user = await login({ email, password })
      doLogin(user)
      navigate('/dashboard')
    } catch {
      setError('Failed to login')
    }
    setLoading(false)
  }

  return (
    <div className="max-w-md mx-auto p-8 mt-16 border rounded shadow">
      <h2 className="text-2xl font-bold mb-4">{t('login')}</h2>
      {error && <div className="mb-4 text-red-600">{error}</div>}
      <form onSubmit={handleSubmit} className="space-y-4">
        <input type="email" placeholder="Email" value={email} onChange={e => setEmail(e.target.value)} required className="w-full p-2 border rounded" />
        <input type="password" placeholder="Password" value={password} onChange={e => setPassword(e.target.value)} required className="w-full p-2 border rounded" />
        <button type="submit" disabled={loading} className="w-full bg-blue-700 text-white py-2 rounded hover:bg-blue-800">
          {loading ? t('loading') : t('login')}
        </button>
      </form>
    </div>
  )
}
EOLOGIN

# src/pages/Register.jsx
cat << 'EOREGISTER' > src/pages/Register.jsx
import React, { useState } from 'react'
import { useTranslation } from 'react-i18next'

export default function Register() {
  const { t } = useTranslation()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')

  const handleSubmit = (e) => {
    e.preventDefault()
    alert('Registration not implemented yet.')
  }

  return (
    <div className="max-w-md mx-auto p-8 mt-16 border rounded shadow">
      <h2 className="text-2xl font-bold mb-4">{t('register')}</h2>
      <form onSubmit={handleSubmit} className="space-y-4">
        <input type="email" placeholder="Email" value={email} onChange={e => setEmail(e.target.value)} required className="w-full p-2 border rounded" />
        <input type="password" placeholder="Password" value={password} onChange={e => setPassword(e.target.value)} required className="w-full p-2 border rounded" />
        <button type="submit" className="w-full bg-blue-700 text-white py-2 rounded hover:bg-blue-800">{t('register')}</button>
      </form>
    </div>
  )
}
EOREGISTER

# src/pages/Dashboard.jsx
cat << 'EODASH' > src/pages/Dashboard.jsx
import React from 'react'
import { useTranslation } from 'react-i18next'

export default function Dashboard() {
  const { t } = useTranslation()
  return (
    <div className="p-8">
      <h2 className="text-2xl font-bold">{t('dashboard')}</h2>
      <p className="mt-4">This is your dashboard. Content coming soon.</p>
    </div>
  )
}
EODASH

# src/App.jsx
cat << 'EOAPP' > src/App.jsx
import React, { useContext } from 'react'
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import Navbar from './components/Navbar'
import Home from './pages/Home'
import Login from './pages/Login'
import Register from './pages/Register'
import Dashboard from './pages/Dashboard'
import { AuthContext, AuthProvider } from './context/AuthContext'

function PrivateRoute({ children }) {
  const { user } = useContext(AuthContext)
  return user ? children : <Navigate to="/login" replace />
}

export default function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Navbar />
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/dashboard" element={
            <PrivateRoute>
              <Dashboard />
            </PrivateRoute>
          } />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  )
}
EOAPP

chmod +x setup_frontend.sh
echo "Setup script created as setup_frontend.sh"
echo "Run './setup_frontend.sh' to create frontend files, then run 'npm install' and 'npm run dev' to start development server."


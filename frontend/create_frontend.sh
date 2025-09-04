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

# ... (Continue adding all other files as in previous message) ...

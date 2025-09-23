// SwaedUAE Theme — Phase 1 (tokens only)
module.exports = {
  darkMode: 'class',
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './public/**/*.html',
  ],
  theme: {
    extend: {
      container: { center: true, padding: { DEFAULT: '1rem', lg: '2rem', xl: '2rem', '2xl': '3rem' } },
      fontFamily: {
        sans: ['Inter','Cairo','Tajawal','ui-sans-serif','system-ui','-apple-system','Segoe UI','Roboto','Noto Sans','Helvetica Neue','Arial','sans-serif'],
      },
      colors: {
        background: '#ffffff',
        foreground: '#0f172a',
        primary:   { DEFAULT: '#0ea5e9', 50:'#f0f9ff',100:'#e0f2fe',600:'#0284c7',700:'#0369a1' },
        secondary: { DEFAULT: '#10b981', 600:'#059669',700:'#047857' },
        success:   { DEFAULT: '#16a34a' },
        warning:   { DEFAULT: '#f59e0b' },
        danger:    { DEFAULT: '#ef4444' },
        info:      { DEFAULT: '#06b6d4' },
        muted:     { DEFAULT: '#f3f4f6', foreground: '#6b7280' },
      },
      borderRadius: { xl: '1rem', '2xl': '1.25rem', '3xl': '1.5rem' },
      boxShadow: { soft: '0 8px 24px rgba(0,0,0,0.06)', elevated: '0 16px 32px rgba(0,0,0,0.08)' },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
    require('@tailwindcss/line-clamp'),
  ],
}

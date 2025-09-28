/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
    "./public/index.html",
  ],
  theme: {
    extend: {
      colors: {
        'ocean-blue': '#1e3a8a',
        'ocean-light': '#3b82f6',
        'ocean-dark': '#1e40af',
        'wave-blue': '#60a5fa',
        'sand-beige': '#f8fafc',
        'stone-grey': '#64748b',
        'pono-green': '#059669',
        'agape-purple': '#7c3aed',
        'text-dark': '#1e293b',
        'text-light': '#64748b',
      },
      fontFamily: {
        'fira-code': ['Fira Code', 'monospace'],
        'inter': ['Inter', 'sans-serif'],
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-out',
        'slide-in': 'slideIn 0.3s ease-out',
        'pulse-slow': 'pulse 2s infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        slideIn: {
          '0%': { opacity: '0', transform: 'translateX(-20px)' },
          '100%': { opacity: '1', transform: 'translateX(0)' },
        },
      },
      backdropBlur: {
        'xs': '2px',
      },
      boxShadow: {
        'glow': '0 0 20px rgba(59, 130, 246, 0.3)',
        'glow-green': '0 0 20px rgba(5, 150, 105, 0.3)',
        'glow-purple': '0 0 20px rgba(124, 58, 237, 0.3)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}

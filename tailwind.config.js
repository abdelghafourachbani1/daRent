export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./public/js/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50:  '#fdf3ee',
          100: '#fae0d0',
          200: '#f4bfa0',
          300: '#ec9870',
          400: '#e07248',
          500: '#C0704A',
          600: '#a85a38',
          700: '#8B4513',
          800: '#6b3310',
          900: '#4a230a',
        },
        navy: {
          DEFAULT: '#1a2332',
          light:   '#243044',
          dark:    '#111827',
        },
        dark:  '#1a1a1a',
        muted: '#6b7280',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'card':      '0 2px 16px rgba(0,0,0,0.10)',
        'card-hover':'0 8px 32px rgba(0,0,0,0.15)',
        'nav':       '0 2px 8px rgba(0,0,0,0.15)',
      },
      borderRadius: {
        'xl':  '12px',
        '2xl': '16px',
        '3xl': '24px',
      },
    },
  },
  plugins: [],
}
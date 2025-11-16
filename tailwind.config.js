/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        // Primary colors
        primary: {
          DEFAULT: '#1B84FF',
          light: '#EDF5FD',
          dark: '#253662',
        },
        secondary: {
          DEFAULT: '#43CED7',
          light: '#F2FCFC',
          dark: '#1C455D',
        },
        // Status colors
        success: {
          DEFAULT: '#2CD07E',
          light: '#EDFDF2',
          dark: '#1B3C48',
        },
        warning: {
          DEFAULT: '#F6C000',
          light: '#FFFCF0',
          dark: '#4D3A2A',
        },
        error: {
          DEFAULT: '#F8285A',
          light: '#FFF0F4',
          dark: '#4B313D',
        },
        info: {
          DEFAULT: '#2CABE3',
          light: '#E4F5FF',
          dark: '#223662',
        },
        // Accent colors
        accent: {
          DEFAULT: '#FFAB91',
          dark: '#FF4081',
        },
        purple: '#725AF2',
        indigo: '#6610f2',
        // Text colors
        'text-primary': {
          DEFAULT: '#3A4752',
          dark: '#EAEFF4',
        },
        'text-secondary': {
          DEFAULT: '#768B9E',
          dark: '#7C8FAC',
        },
        // Background colors
        surface: {
          DEFAULT: '#ffffff',
          dark: '#152332',
        },
        container: {
          DEFAULT: '#ffffff',
          dark: '#2a3447',
        },
        background: {
          DEFAULT: '#eef5f9',
          dark: '#192838',
        },
        hover: {
          DEFAULT: '#f6f9fc',
          dark: '#333f55',
        },
        // Border colors
        border: {
          DEFAULT: '#ebf1f6',
          dark: '#333F55',
        },
        'input-border': {
          DEFAULT: '#DFE5EF',
          dark: '#465670',
        },
        // Grey scale
        grey: {
          100: '#F2F6FA',
          200: '#EAEFF4',
          '100-dark': '#333F55',
          '200-dark': '#465670',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
      borderRadius: {
        DEFAULT: '8px',
        lg: '12px',
        xl: '16px',
        '2xl': '24px',
        '3xl': '30px',
      },
      boxShadow: {
        card: '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
        'card-hover': '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
        dropdown: '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
      },
    },
  },
  plugins: [],
}

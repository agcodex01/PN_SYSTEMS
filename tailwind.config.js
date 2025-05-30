/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#22bbea',
        'primary-dark': '#1a9bc7',
        secondary: '#2c3e50',
      },
    },
  },
  plugins: [],
} 
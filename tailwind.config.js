/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['themes/app/src/scripts/**/*.js', 'themes/app/templates/**/*.ss'],
  theme: {
    extend: {
      screens: {
        'sm-down': { max: '767px' },
      },
      zIndex: {
        1: 1,
        2: 2,
      },
    },
  },
  plugins: [],
}

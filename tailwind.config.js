/** @type {import('tailwindcss').Config} */
const plugin = require('tailwindcss/plugin')

module.exports = {
  content: ['themes/app/src/scripts/**/*.js', 'themes/app/templates/**/*.ss'],
  blocklist: ['container'],
  theme: {
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      white: '#ffffff',
      black: '#000000',
      error: '#dc2626',
    },
    extend: {
      screens: {
        'sm-down': { max: '767px' },
      },
      zIndex: {
        1: 1,
        2: 2,
        100: 100,
      },
    },
  },
  plugins: [
    plugin(function ({ addUtilities }) {
      addUtilities({
        '.overflow-scroll-touch': {
          webkitOverflowScrolling: 'touch',
        },
        '.pull-left': {
          width: 'calc(100% + (50vw - 50%))',
          marginLeft: 'calc(50% - 50vw)',
        },
        '.pull-right': {
          width: 'calc(100% + (50vw - 50%))',
          marginRight: 'calc(50% - 50vw)',
        },
        '.force-hidden': {
          display: 'none !important',
        },
      })
    }),
  ],
}

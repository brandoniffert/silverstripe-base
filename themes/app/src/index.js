// Theme
import '@styles/index.css'

import mobileBar from '@scripts/components/mobile-bar'
mobileBar.init()

document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    document.documentElement.classList.remove('_preload')
  }, 250)
})

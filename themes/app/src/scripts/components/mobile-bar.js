export default {
  init: () => {
    const mobileBar = document.querySelector('.mobile-bar')

    if (mobileBar) {
      const observer = new IntersectionObserver(([e]) => mobileBar.classList.toggle('is-hidden', !e.isIntersecting))

      observer.observe(document.querySelector('.scroll-pixel'))
    }
  },
}

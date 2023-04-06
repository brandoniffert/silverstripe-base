;(function ($) {
  $.entwine('ss', function ($) {
    $('.iconpickerfield-wrapper').entwine({
      onmatch: function () {
        const wrapper = $(this)[0]
        const ssField = wrapper.querySelector('select')
        const selectedPreview = wrapper.querySelector('.iconpickerfield-selected')
        const chooseBtn = wrapper.querySelector('.iconpickerfield-choose')
        const changeBtn = wrapper.querySelector('.iconpickerfield-change')
        const removeBtn = wrapper.querySelector('.iconpickerfield-remove')
        const filterField = wrapper.querySelector('.iconpickerfield-search input')
        const clearFilterButton = wrapper.querySelector('.iconpickerfield-search-clear')
        const icons = wrapper.querySelectorAll('.iconpickerfield-icon-list li')

        const updateUi = () => {
          chooseBtn.style.display = 'none'
          changeBtn.style.display = 'none'
          removeBtn.style.display = 'none'
          selectedPreview.style.display = 'none'

          if (ssField.value) {
            changeBtn.style.display = 'block'
            removeBtn.style.display = 'block'
            selectedPreview.style.display = 'block'
          } else {
            chooseBtn.style.display = 'block'
          }

          selectedPreview.style.visibility = 'visible'

          filterField.value = ''
          filterField.dispatchEvent(new Event('keyup'))
        }

        filterField.addEventListener('keyup', function (e) {
          const term = e.target.value.toLowerCase()

          icons.forEach((el) => {
            if (el.dataset.title.indexOf(term) !== -1) {
              el.style.display = 'block'
            } else {
              el.style.display = 'none'
            }
          })

          if (term === '') {
            icons.forEach((el) => (el.style.display = 'block'))
            clearFilterButton.style.display = 'none'
          } else {
            clearFilterButton.style.display = 'inline-block'
          }
        })

        clearFilterButton.addEventListener('click', function () {
          filterField.value = ''
          filterField.dispatchEvent(new Event('keyup'))
        })

        window.addEventListener('click', function (e) {
          const target = e.target

          if (
            wrapper.classList.contains('is-active') &&
            !target.closest('.iconpickerfield-holder') &&
            !target.closest('.iconpickerfield-trigger')
          ) {
            wrapper.classList.remove('is-active')
            filterField.value = ''
            filterField.dispatchEvent(new Event('keyup'))
          }

          if (target.closest('.iconpickerfield-trigger')) {
            wrapper.classList.toggle('is-active')
          }

          if (target.closest('.iconpickerfield-option')) {
            const option = target.closest('.iconpickerfield-option')

            ssField.value = option.dataset.id
            wrapper.classList.remove('is-active')
            selectedPreview.innerHTML = option.innerHTML
            updateUi()
          }

          if (target.closest('.iconpickerfield-remove')) {
            ssField.value = null
            selectedPreview.innerHTML = null
            wrapper.classList.remove('is-active')
            updateUi()
          }
        })

        wrapper.style.visibility = 'visible'
        updateUi()
      },
    })
  })
})(window.jQuery)

import wretch from 'wretch'

wretch.options({
  headers: { 'X-Requested-With': 'XMLHttpRequest' },
})

export default wretch()

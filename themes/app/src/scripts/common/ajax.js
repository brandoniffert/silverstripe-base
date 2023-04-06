import wretch from 'wretch'
import QueryStringAddon from 'wretch/addons/queryString'

wretch.options({
  headers: { 'X-Requested-With': 'XMLHttpRequest' },
})

export default wretch().addon(QueryStringAddon)

 import util from '../utils'
export function formatDate (time) {
    let date = new Date(time)
    return util.formatDateTimeFil(date, 'yyyy-MM-dd')
  }
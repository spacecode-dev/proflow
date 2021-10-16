const moment = require('moment-timezone')
export default function (value, format) {
  if (value) {
    return moment(value).format(format)
  }
  return ''
}

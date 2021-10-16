const moment = require('moment-timezone')

export default function (date) {
  const tommorow = moment().add(1, 'day')
  const dayaftertom = moment().add(2, 'day')
  const today = moment().utc()
  const dueDate = moment(date).utc()
  if (moment(date).isSame(today, 'day') || moment(dueDate).isBefore(today)) {
    return 'text-danger'
  } else if (moment(date).isSame(tommorow, 'day') || moment(dueDate).isSame(dayaftertom, 'day')) {
    return 'text-warning'
  } else if (moment(date).isAfter(dayaftertom, 'day')) {
    return 'text-success'
  }
  return 'text-danger'
}

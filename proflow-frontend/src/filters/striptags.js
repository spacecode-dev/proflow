export default function (value) {
  if (value) {
    return value.replace(/<\/?[^>]+(>|$)/g, '').replace(/<[^>]*(>|$)|&nbsp;|&zwnj;|&raquo;|&laquo;|&gt;/g, ' ')
  }
  return ''
}

const host = window.location.host
export const subdomain = host.split('.')[1] ? host.split('.')[0] : false
export const domain = subdomain ? host.replace(`${subdomain}.`, '') : host

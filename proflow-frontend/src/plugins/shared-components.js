import Vue from 'vue'

// filters
import Capitalize from '@/filters/capitalize'
import DateFormat from '@/filters/dateformat'
import StripTags from '@/filters/striptags'
import GetVariantByDate from '@/filters/getvariantbydate'
import GetColorByDate from '@/filters/getcolorbydate'
// components
import AppIcon from '@/components/general/AppIcon'
import AppAvatar from '@/components/general/AppAvatar'

// register filters globally
Vue.filter('capitalize', Capitalize)
Vue.filter('formatDate', DateFormat)
Vue.filter('stripTags', StripTags)
Vue.filter('getVariantByDate', GetVariantByDate)
Vue.filter('getColorByDate', GetColorByDate)
// register components globally
Vue.component('AppIcon', AppIcon)
Vue.component('AppAvatar', AppAvatar)

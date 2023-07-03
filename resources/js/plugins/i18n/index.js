import axios from '@axios'
import { createI18n } from 'vue-i18n'

const { data: messages } = await axios.get('/localization')

let locale = ['ar', 'en'].includes(localStorage.getItem('lang')) ? localStorage.getItem('lang') : 'ar'

axios.defaults.headers.common = {
  "Accept-Language": locale,
}
export default createI18n({
  legacy: false,
  locale,
  fallbackLocale: 'ar',
  messages,
})

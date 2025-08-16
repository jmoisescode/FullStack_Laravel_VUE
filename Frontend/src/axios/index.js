import axios from 'axios'
import { VueCookieNext } from 'vue-cookie-next'

let baseURL = import.meta.env.VITE_APP_ROOTAPI  
let ACCCTKN = import.meta.env.VITE_APP_ACCESS_TOKEN 
  
const request = axios.create({
  baseURL,
})
request.interceptors.request.use(
  (config) => { 
    const acccesTokenCookieName = import.meta.env.VITE_APP_ACCESS_TOKEN 
    if (VueCookieNext.isCookieAvailable(acccesTokenCookieName)) {
      const csrfToken = VueCookieNext.getCookie(acccesTokenCookieName)
  
        config.headers['Authorization'] = `Bearer ${csrfToken}`  
    }  
    return config
  },
  (error) => {
    return Promise.reject(error)
  },
)

request.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {  
    if (error.response.status === 401) {
      VueCookieNext.removeCookie(ACCCTKN)
      // location.reload();
    }
    return Promise.reject(error.response.data)
  },
)

export default request

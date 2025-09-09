import axios from 'axios'

// Mobile check
const isMobile = window.Capacitor && window.Capacitor.isNativePlatform()

// Base URL
const baseURL = isMobile
  ? 'http://10.0.2.2:8001'
  : import.meta.env.VITE_APP_URL

axios.defaults.baseURL = baseURL
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

// Token interceptor
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
}, error => Promise.reject(error))

// Handle 401
axios.interceptors.response.use(response => response, error => {
  if (error.response?.status === 401) {
    localStorage.removeItem('auth_token')
    window.location.href = '/login'
  }
  return Promise.reject(error)
})

export default axios
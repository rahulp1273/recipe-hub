import axios from 'axios'

// Check if it's mobile or web
const isMobile = window.Capacitor && window.Capacitor.isNativePlatform()

// For mobile, use local dev IP; for web, use APP_URL from env
const baseURL = isMobile
  ? 'http://10.0.2.2:8001'
  : import.meta.env.VITE_APP_URL // <-- use the env variable here

axios.defaults.baseURL = baseURL

// Set default headers
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

// Add token to every request
axios.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

// Handle 401 globally
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default axios
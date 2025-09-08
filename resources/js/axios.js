import axios from 'axios'

// Set base URL for API calls
// In development (web), use relative URLs
// In production (mobile), use the full URL
const isMobile = window.Capacitor && window.Capacitor.isNativePlatform()
const baseURL = isMobile ? 'http://10.0.2.2:8001' : ''

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
  (error) => {
    return Promise.reject(error)
  }
)

// Handle 401 responses globally
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

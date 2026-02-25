<template>
  <div class="min-h-screen bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="text-white text-2xl font-bold">🍳</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">RecipeHub</h1>
        <p class="text-gray-600 mt-2">Welcome back! Please sign in.</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="login" class="space-y-6">
        <!-- Email Input -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input
            type="email"
            v-model="form.email"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200"
            placeholder="Enter your email"
          />
        </div>

        <!-- Password Input -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <div class="relative">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="form.password"
              required
              class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200"
              placeholder="Enter your password"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
            >
              {{ showPassword ? '👁️' : '👁️‍🗨️' }}
            </button>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
          {{ error }}
        </div>

        <!-- Login Button -->
        <button
          type="submit"
          :disabled="isLoading"
          class="w-full bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105"
        >
          {{ isLoading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>

      <!-- Register Link -->
      <div class="text-center mt-6">
        <p class="text-gray-600">
          Don't have an account?
          <router-link to="/register" class="text-orange-500 hover:text-orange-600 font-semibold">
            Sign up
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

const form = ref({
  email: '',
  password: ''
})

const isLoading = ref(false)
const error = ref('')
const showPassword = ref(false)

const login = async () => {
  isLoading.value = true
  error.value = ''

  try {
    const response = await axios.post('/api/login', {
      email: form.value.email,
      password: form.value.password
    }, {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    const data = response.data

    if (data.requires_otp) {
      localStorage.setItem('pending_otp', JSON.stringify({
        email: form.value.email,
        type: 'login'
      }))

      router.push({ name: 'VerifyOtp' })
    } else if (data.token) {
      localStorage.setItem('auth_token', data.token)
      router.push('/dashboard')
    }

  } catch (err) {
    console.error('Login error:', err)

    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else if (err.response?.data?.errors) {
      error.value = Object.values(err.response.data.errors).flat().join(', ')
    } else {
      error.value = 'Login failed. Please try again.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
      <div class="text-center mb-6">
        <div class="w-16 h-16 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="text-white text-2xl font-bold">🔐</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Verify Your Email</h1>
        <p class="text-gray-600 mt-2">
          Enter the 6-digit code we sent to
          <span class="font-semibold">{{ email }}</span>
        </p>
      </div>

      <div class="flex justify-center gap-2 mb-4">
        <input
          v-for="(digit, index) in otpDigits"
          :key="index"
          type="text"
          maxlength="1"
          class="w-10 h-12 border border-gray-300 rounded-lg text-center text-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
          v-model="otpDigits[index]"
          @input="onInput(index, $event)"
          @keydown.backspace.prevent="onBackspace(index)"
          ref="otpInputs"
        />
      </div>

      <div class="text-center text-sm text-gray-600 mb-4">
        <span v-if="timeLeft > 0">
          Code expires in
          <span class="font-semibold">
            {{ minutes }}:{{ seconds }}
          </span>
        </span>
        <span v-else class="text-red-600 font-semibold">
          Code expired. Please request a new one.
        </span>
      </div>

      <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        {{ error }}
      </div>

      <button
        class="w-full bg-orange-500 hover:bg-orange-600 disabled:bg-orange-300 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 mb-3"
        :disabled="isVerifying || otpValue.length !== 6 || timeLeft <= 0"
        @click="verifyOtp"
      >
        {{ isVerifying ? 'Verifying...' : 'Verify Code' }}
      </button>

      <button
        class="w-full border border-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg hover:bg-gray-50 disabled:opacity-50 text-sm"
        :disabled="isResending"
        @click="resendOtp"
      >
        {{ isResending ? 'Resending...' : 'Resend Code' }}
      </button>

      <p class="text-center text-xs text-gray-500 mt-4">
        Having trouble? Make sure to check your spam or promotions folder.
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const pendingFromStorage = (() => {
  try {
    const raw = localStorage.getItem('pending_otp')
    return raw ? JSON.parse(raw) : null
  } catch {
    return null
  }
})()

const email = ref(route.query.email || pendingFromStorage?.email || '')
const type = ref(route.query.type || pendingFromStorage?.type || 'login')

const otpDigits = ref(['', '', '', '', '', ''])
const otpInputs = ref([])

const isVerifying = ref(false)
const isResending = ref(false)
const error = ref('')

const timeLeft = ref(300)
let timerId = null

const otpValue = computed(() => otpDigits.value.join(''))

const minutes = computed(() => String(Math.floor(timeLeft.value / 60)).padStart(2, '0'))
const seconds = computed(() => String(timeLeft.value % 60).padStart(2, '0'))

const focusInput = (index) => {
  const el = otpInputs.value[index]
  if (el) el.focus()
}

const onInput = (index, event) => {
  const value = event.target.value.replace(/\D/g, '')

  if (!value) {
    otpDigits.value[index] = ''
    return
  }

  otpDigits.value[index] = value.charAt(value.length - 1)

  if (index < otpDigits.value.length - 1) {
    focusInput(index + 1)
  }
}

const onBackspace = (index) => {
  if (otpDigits.value[index]) {
    otpDigits.value[index] = ''
    return
  }

  if (index > 0) {
    focusInput(index - 1)
  }
}

const startTimer = () => {
  timerId = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value -= 1
    } else if (timerId) {
      clearInterval(timerId)
      timerId = null
    }
  }, 1000)
}

const verifyOtp = async () => {
  if (otpValue.value.length !== 6 || !email.value) return

  isVerifying.value = true
  error.value = ''

  try {
    const response = await axios.post('/api/verify-otp', {
      email: email.value,
      otp: otpValue.value,
      type: type.value
    })

    const token = response.data.token
    localStorage.setItem('auth_token', token)
    localStorage.removeItem('pending_otp')

    router.push('/dashboard')
  } catch (err) {
    if (err.response?.data?.errors) {
      error.value = Object.values(err.response.data.errors).flat().join(', ')
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Verification failed. Please try again.'
    }
  } finally {
    isVerifying.value = false
  }
}

const resendOtp = async () => {
  if (!email.value) return

  isResending.value = true
  error.value = ''

  try {
    await axios.post('/api/resend-otp', {
      email: email.value,
      type: type.value
    })

    timeLeft.value = 300
    if (!timerId) {
      startTimer()
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      error.value = Object.values(err.response.data.errors).flat().join(', ')
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Failed to resend code. Please try again.'
    }
  } finally {
    isResending.value = false
  }
}

onMounted(() => {
  if (!email.value || !type.value) {
    router.push('/login')
    return
  }

  focusInput(0)
  startTimer()
})

onBeforeUnmount(() => {
  if (timerId) {
    clearInterval(timerId)
  }
})
</script>


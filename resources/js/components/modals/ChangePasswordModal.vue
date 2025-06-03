<template>
  <!-- Change Password Modal -->
  <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
      <h3 class="text-xl font-bold text-gray-900 mb-4">🔒 Change Password</h3>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- Current Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
          <input type="password" v-model="form.current_password" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
            placeholder="Enter current password" />
        </div>

        <!-- New Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
          <input type="password" v-model="form.new_password" required minlength="8"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
            placeholder="Enter new password" />
        </div>

        <!-- Confirm New Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
          <input type="password" v-model="form.new_password_confirmation" required minlength="8"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
            placeholder="Confirm new password" />
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
          {{ error }}
        </div>

        <!-- Success Message -->
        <div v-if="success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">
          {{ success }}
        </div>

        <!-- Action Buttons field-->
        <div class="flex gap-3 pt-4">
          <button type="button" @click="handleClose"
            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors">
            Cancel
          </button>
          <button type="submit" :disabled="isLoading"
            class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-4 py-2 rounded-lg font-medium transition-all disabled:opacity-50">
            {{ isLoading ? 'Changing...' : 'Change Password' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

// Props
const props = defineProps({
  showModal: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['close', 'success'])

// State
const isLoading = ref(false)
const error = ref('')
const success = ref('')

// Form data
const form = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

// Methods
const handleSubmit = async () => {
  // Clear previous messages ad
  error.value = ''
  success.value = ''

  // Client-side validation
  if (form.value.new_password.length < 8) {
    error.value = 'Password must be at least 8 characters long'
    return
  }

  if (form.value.new_password !== form.value.new_password_confirmation) {
    error.value = 'New passwords do not match'
    return
  }

  try {
    isLoading.value = true
    const token = localStorage.getItem('auth_token')

    const payload = {
      current_password: form.value.current_password,
      new_password: form.value.new_password,
      new_password_confirmation: form.value.new_password_confirmation
    }

    await axios.post('/api/user/change-password', payload, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    success.value = 'Password changed successfully!'

    // Emit success event
    emit('success', 'Password changed successfully!')

    // Auto-close after success
    setTimeout(() => {
      handleClose()
    }, 2000)

  } catch (err) {
    console.error('Error changing password:', err)

    if (err.response?.status === 422) {
      const errors = err.response.data.errors
      if (errors.current_password) {
        error.value = 'Current password is incorrect'
      } else if (errors.password) {
        error.value = errors.password[0]
      } else {
        error.value = 'Validation failed. Please check your inputs.'
      }
    } else if (err.response?.status === 401) {
      error.value = 'Current password is incorrect'
    } else {
      error.value = err.response?.data?.message || 'Failed to change password'
    }
  } finally {
    isLoading.value = false
  }
}

const handleClose = () => {
  // Reset form and messages
  form.value = {
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
  }
  error.value = ''
  success.value = ''

  // Emit close event
  emit('close')
}

// Watch for modal close to reset form
watch(() => props.showModal, (newVal) => {
  if (!newVal) {
    handleClose()
  }
})
</script>

<style scoped>
/* Modal backdrop blur */
.fixed.inset-0 {
  backdrop-filter: blur(4px);
}

/* Focus styles */
input:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

/* Button hover effects */
button:hover:not(:disabled) {
  transform: translateY(-1px);
}

button:active:not(:disabled) {
  transform: translateY(0);
}

/* Success/Error message animations */
.bg-red-100,
.bg-green-100 {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

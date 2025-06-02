<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 shadow-lg">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center space-x-4">
            <button @click="goBack" class="text-white hover:text-orange-100 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            <div>
              <h1 class="text-3xl font-bold text-white">My Profile</h1>
              <p class="text-orange-100 text-sm">Manage your account settings</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-16">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
          <span class="text-2xl">👤</span>
        </div>
        <p class="text-xl text-gray-600">Loading your profile...</p>
      </div>

      <!-- Profile Content -->
      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Avatar & Stats -->
        <div class="lg:col-span-1">
          <!-- Avatar Section -->
          <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="text-center">
              <div class="relative inline-block">
                <img
                  :src="profile.avatar_url"
                  :alt="profile.user.name"
                  class="w-32 h-32 rounded-full object-cover border-4 border-orange-200 shadow-lg"
                />
                <button
                  @click="triggerFileUpload"
                  class="absolute bottom-0 right-0 bg-orange-500 hover:bg-orange-600 text-white p-2 rounded-full shadow-lg transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </button>
              </div>

              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                @change="uploadAvatar"
                class="hidden"
              />

              <h3 class="text-xl font-bold text-gray-900 mt-4">{{ profile.user.name }}</h3>
              <p class="text-gray-600">{{ profile.user.email }}</p>

              <div class="flex justify-center space-x-2 mt-4">
                <button
                  @click="triggerFileUpload"
                  class="text-sm bg-orange-100 text-orange-700 px-3 py-1 rounded-full hover:bg-orange-200 transition-colors"
                >
                  📷 Change Photo
                </button>
                <button
                  v-if="profile.user.avatar_path"
                  @click="removeAvatar"
                  class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded-full hover:bg-red-200 transition-colors"
                >
                  🗑️ Remove
                </button>
              </div>
            </div>
          </div>

          <!-- Stats Section -->
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h4 class="text-lg font-bold text-gray-900 mb-4">📊 Your Statistics</h4>
            <div class="space-y-4">
              <div class="flex justify-between items-center">
                <span class="text-gray-600">📝 Total Recipes</span>
                <span class="font-bold text-orange-600">{{ profile.stats.total_recipes }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-600">👀 Total Views</span>
                <span class="font-bold text-blue-600">{{ profile.stats.total_views }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-600">⭐ Average Rating</span>
                <span class="font-bold text-green-600">{{ profile.stats.average_rating }}/5</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-600">📅 Member Since</span>
                <span class="font-bold text-purple-600">{{ profile.stats.account_created }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Profile Form -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl shadow-lg p-6">
            <h4 class="text-xl font-bold text-gray-900 mb-6">✏️ Edit Profile Information</h4>

            <form @submit.prevent="updateProfile" class="space-y-6">
              <!-- Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input
                  type="text"
                  v-model="form.name"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                  placeholder="Enter your full name"
                />
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input
                  type="email"
                  v-model="form.email"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                  placeholder="Enter your email"
                />
              </div>

              <!-- Phone -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input
                  type="tel"
                  v-model="form.phone"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                  placeholder="Enter your phone number"
                />
              </div>

              <!-- Bio -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                <textarea
                  v-model="form.bio"
                  rows="3"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                  placeholder="Tell us about yourself..."
                ></textarea>
              </div>

              <!-- Location -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input
                  type="text"
                  v-model="form.location"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
                  placeholder="Enter your location"
                />
              </div>

              <!-- Action Buttons -->
              <div class="flex flex-col sm:flex-row gap-4 pt-6">
                <button
                  type="submit"
                  :disabled="isUpdating"
                  class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50"
                >
                  {{ isUpdating ? '💾 Saving...' : '💾 Save Changes' }}
                </button>

                <button
                  type="button"
                  @click="showPasswordModal = true"
                  class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl"
                >
                  🔒 Change Password
                </button>
              </div>
            </form>

            <!-- Success/Error Messages -->
            <div v-if="message" class="mt-6">
              <div :class="messageType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'" class="border px-4 py-3 rounded-lg">
                {{ message }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Change Password Modal Component -->
    <ChangePasswordModal
      :showModal="showPasswordModal"
      @close="showPasswordModal = false"
      @success="handlePasswordChangeSuccess"
    />
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import ChangePasswordModal from '@/Components/modals/ChangePasswordModal.vue'

const router = useRouter()

// State
const loading = ref(true)
const isUpdating = ref(false)
const showPasswordModal = ref(false)
const message = ref('')
const messageType = ref('success')

// Profile data
const profile = ref({
  user: {},
  stats: {},
  avatar_url: ''
})

// Forms
const form = ref({
  name: '',
  email: '',
  phone: '',
  bio: '',
  location: ''
})

// File input ref
const fileInput = ref(null)

// Methods
const fetchProfile = async () => {
  try {
    loading.value = true
    const token = localStorage.getItem('auth_token')

    const response = await axios.get('/api/user/profile', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })

    profile.value = response.data.data

    // Populate form
    form.value = {
      name: profile.value.user.name || '',
      email: profile.value.user.email || '',
      phone: profile.value.user.phone || '',
      bio: profile.value.user.bio || '',
      location: profile.value.user.location || ''
    }

  } catch (error) {
    console.error('Error fetching profile:', error)
    if (error.response?.status === 401) {
      router.push('/login')
    }
    showMessage('Failed to load profile', 'error')
  } finally {
    loading.value = false
  }
}

const updateProfile = async () => {
  try {
    isUpdating.value = true
    const token = localStorage.getItem('auth_token')

    const response = await axios.put('/api/user/profile', form.value, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    })

    // Update profile data field
    profile.value.user = response.data.data
    showMessage('Profile updated successfully!', 'success')

  } catch (error) {
    console.error('Error updating profile:', error)
    const errorMsg = error.response?.data?.message || 'Failed to update profile'
    showMessage(errorMsg, 'error')
  } finally {
    isUpdating.value = false
  }
}

const triggerFileUpload = () => {
  fileInput.value.click()
}

const uploadAvatar = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate file
  if (!file.type.startsWith('image/')) {
    showMessage('Please select an image file', 'error')
    return
  }

  if (file.size > 2 * 1024 * 1024) { // 2MB
    showMessage('Image size should be less than 2MB', 'error')
    return
  }

  try {
    const token = localStorage.getItem('auth_token')
    const formData = new FormData()
    formData.append('avatar', file)

    const response = await axios.post('/api/user/profile/avatar', formData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'multipart/form-data'
      }
    })

    // Update avatar URL
    profile.value.avatar_url = response.data.data.avatar_url
    showMessage('Avatar updated successfully!', 'success')

  } catch (error) {
    console.error('Error uploading avatar:', error)
    const errorMsg = error.response?.data?.message || 'Failed to upload avatar'
    showMessage(errorMsg, 'error')
  }

  // Reset file input
  event.target.value = ''
}

const removeAvatar = async () => {
  if (!confirm('Are you sure you want to remove your profile picture?')) {
    return
  }

  try {
    const token = localStorage.getItem('auth_token')

    await axios.delete('/api/user/profile/avatar', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })

    // Update avatar URL to default
    profile.value.avatar_url = `https://ui-avatars.com/api/?name=${encodeURIComponent(profile.value.user.name)}&background=f97316&color=fff&size=200`
    profile.value.user.avatar_path = null
    showMessage('Avatar removed successfully!', 'success')

  } catch (error) {
    console.error('Error removing avatar:', error)
    showMessage('Failed to remove avatar', 'error')
  }
}

// New method for password change success
const handlePasswordChangeSuccess = (message) => {
  showMessage(message, 'success')
  showPasswordModal.value = false
}

const showMessage = (msg, type = 'success') => {
  message.value = msg
  messageType.value = type

  // Auto hide after 5 seconds
  setTimeout(() => {
    message.value = ''
  }, 5000)
}

const goBack = () => {
  router.push('/dashboard')
}

// Lifecycle
onMounted(() => {
  fetchProfile()
})
</script>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Custom scrollbar for textarea */
textarea::-webkit-scrollbar {
  width: 6px;
}

textarea::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Focus styles */
input:focus, textarea:focus {
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

/* Modal backdrop blur */
.fixed.inset-0 {
  backdrop-filter: blur(4px);
}
</style>

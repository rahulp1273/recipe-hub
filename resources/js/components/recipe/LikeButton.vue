<template>
  <div class="relative">
    <button
      @click="toggleLike"
      :disabled="isLoading"
      class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-gray-50 disabled:opacity-50"
      :class="[
        isLiked
          ? 'text-red-500 hover:text-red-600'
          : 'text-gray-500 hover:text-red-500'
      ]"
    >
      <!-- Heart Icon -->
      <div class="relative">
        <span
          class="text-lg transition-transform duration-200"
          :class="{ 'scale-110': isLiked }"
        >
          {{ isLiked ? '❤️' : '🤍' }}
        </span>

        <!-- Loading spinner -->
        <div
          v-if="isLoading"
          class="absolute inset-0 flex items-center justify-center"
        >
          <div class="w-4 h-4 border-2 border-gray-300 border-t-red-500 rounded-full animate-spin"></div>
        </div>
      </div>

      <!-- Like Count -->
      <span class="text-sm font-medium">
        {{ likesCount }}
      </span>

      <!-- Like Text -->
      <span class="text-sm hidden sm:inline">
        {{ isLiked ? 'Liked' : 'Like' }}
      </span>
    </button>

    <!-- Login Prompt Modal -->
    <div v-if="showLoginPrompt" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="text-2xl">❤️</span>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Login to Like</h3>
        <p class="text-gray-600 mb-6">Please login to like recipes and show your appreciation!</p>
        <div class="flex gap-3">
          <button
            @click="closeLoginPrompt"
            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors"
          >
            Cancel
          </button>
          <button
            @click="goToLogin"
            class="flex-1 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white px-4 py-2 rounded-lg font-medium transition-all"
          >
            Login Now
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

// Props
const props = defineProps({
  recipeId: {
    type: Number,
    required: true
  },
  isLiked: {
    type: Boolean,
    default: false
  },
  likesCount: {
    type: Number,
    default: 0
  }
})

// Emits
const emit = defineEmits(['like-toggled'])

// Router
const router = useRouter()

// State
const isLoading = ref(false)
const showLoginPrompt = ref(false)

// Methods
const toggleLike = async () => {
  // Check if user is logged in
  const token = localStorage.getItem('auth_token')
  if (!token) {
    // Show login modal instead of alert
    showLoginPrompt.value = true
    return
  }

  try {
    isLoading.value = true

    const response = await axios.post(`/api/social/recipes/${props.recipeId}/like`, {}, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })

    if (response.data.success) {
      // Emit the new state to parent
      emit('like-toggled', response.data.is_liked, response.data.likes_count)

      // Optional: Show feedback
      if (response.data.is_liked) {
        console.log('Recipe liked! ❤️')
      } else {
        console.log('Recipe unliked! 💔')
      }
    }

  } catch (error) {
    console.error('Error toggling like:', error)

    if (error.response?.status === 401) {
      showLoginPrompt.value = true
    } else {
      console.log('Failed to like recipe. Please try again.')
    }
  } finally {
    isLoading.value = false
  }
}

// Close login prompt
const closeLoginPrompt = () => {
  showLoginPrompt.value = false
}

// Go to login page
const goToLogin = () => {
  showLoginPrompt.value = false
  router.push('/login')
}
</script>

<style scoped>
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Heart animation */
button:active .scale-110 {
  transform: scale(1.2);
}
</style>

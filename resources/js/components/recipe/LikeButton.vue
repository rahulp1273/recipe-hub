<template>
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
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue'
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

// State
const isLoading = ref(false)

// Methods
const toggleLike = async () => {
  // Check if user is logged in
  const token = localStorage.getItem('auth_token')
  if (!token) {
    alert('Please login to like recipes!')
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
      alert('Please login to like recipes!')
    } else {
      alert('Failed to like recipe. Please try again.')
    }
  } finally {
    isLoading.value = false
  }
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

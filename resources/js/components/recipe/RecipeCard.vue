<template>
  <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">

    <!-- Login Prompt Modal -->
    <div v-if="showLoginPrompt" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-center">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="text-2xl">🔐</span>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Login Required</h3>
        <p class="text-gray-600 mb-6">Please login to view full recipe details and access more features!</p>
        <div class="flex gap-3">
          <button
            @click="closeLoginPrompt"
            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors"
          >
            Cancel
          </button>
          <button
            @click="goToLogin"
            class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-4 py-2 rounded-lg font-medium transition-all"
          >
            Login Now
          </button>
        </div>
      </div>
    </div>

    <!-- Recipe Header -->
    <div class="p-6 pb-4">
      <!-- User Info -->
      <div class="flex items-center space-x-3 mb-4">
        <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-red-400 rounded-full flex items-center justify-center text-white font-bold">
          {{ getUserInitials(recipe.user.name) }}
        </div>
        <div>
          <h4 class="font-semibold text-gray-900">{{ recipe.user.name }}</h4>
          <p class="text-sm text-gray-500">{{ formatDate(recipe.created_at) }}</p>
        </div>
      </div>

      <!-- Recipe Title -->
      <h3 class="text-xl font-bold text-gray-900 mb-2 cursor-pointer hover:text-orange-600 transition-colors" @click="viewRecipe">
        {{ recipe.title }}
      </h3>

      <!-- Recipe Description -->
      <p class="text-gray-600 mb-4 line-clamp-2">
        {{ recipe.description || 'No description available' }}
      </p>

      <!-- Category Badge -->
      <div v-if="recipe.category" class="mb-4">
        <span class="inline-flex items-center bg-gradient-to-r from-orange-100 to-red-100 text-orange-800 text-xs font-medium px-3 py-1 rounded-full">
          {{ getCategoryEmoji(recipe.category) }} {{ recipe.category }}
        </span>
      </div>
    </div>

    <!-- Recipe Image Placeholder -->
    <div class="h-48 bg-gradient-to-br from-orange-200 via-red-200 to-pink-200 flex items-center justify-center cursor-pointer overflow-hidden" @click="viewRecipe">
      <template v-if="recipe.image">
        <img :src="`/storage/${recipe.image}`" alt="Recipe Image" class="object-cover w-full h-full" />
      </template>
      <template v-else>
        <span class="text-6xl">🍽️</span>
      </template>
    </div>

    <!-- Recipe Stats -->
    <div class="p-6 pt-4">
      <!-- Time & Servings -->
      <div class="grid grid-cols-3 gap-2 mb-4 text-xs">
        <div class="bg-gray-50 rounded-lg p-2 text-center">
          <div class="text-orange-500 font-bold">⏱️</div>
          <div class="text-gray-600">{{ recipe.prep_time || 0 }}min prep</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-2 text-center">
          <div class="text-red-500 font-bold">🔥</div>
          <div class="text-gray-600">{{ recipe.cook_time || 0 }}min cook</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-2 text-center">
          <div class="text-blue-500 font-bold">👥</div>
          <div class="text-gray-600">{{ recipe.servings || 1 }} serves</div>
        </div>
      </div>

      <!-- Ingredients Count -->
      <div class="flex items-center space-x-1 text-sm text-gray-500 mb-4">
        <span>🥕</span>
        <span>{{ getIngredientsCount(recipe.ingredients) }} ingredients</span>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-between pt-4 border-t border-gray-100">
        <!-- Like Button -->
        <LikeButton
          :recipe-id="recipe.id"
          :is-liked="recipe.is_liked"
          :likes-count="recipe.likes_count"
          @like-toggled="handleLikeToggled"
        />

        <!-- View Count -->
        <div class="flex items-center space-x-1 text-gray-500">
          <span>👁️</span>
          <span class="text-sm">{{ recipe.views_count || 0 }}</span>
        </div>

        <!-- View Recipe Button -->
        <button
          @click="viewRecipe"
          class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg"
        >
          👁️ View Recipe
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import LikeButton from './LikeButton.vue'

const router = useRouter()

// Props
const props = defineProps({
  recipe: {
    type: Object,
    required: true
  }
})

// Emits
const emit = defineEmits(['like-toggled', 'view-recorded'])

// State for login prompt
const showLoginPrompt = ref(false)

// Check if user is logged in
const isLoggedIn = () => {
  return !!localStorage.getItem('auth_token')
}

// Methods
const getUserInitials = (name) => {
  return name
    .split(' ')
    .map(word => word.charAt(0))
    .join('')
    .toUpperCase()
    .substring(0, 2)
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

  if (diffDays === 1) {
    return 'Yesterday'
  } else if (diffDays < 7) {
    return `${diffDays} days ago`
  } else {
    return date.toLocaleDateString()
  }
}

const getCategoryEmoji = (category) => {
  const emojiMap = {
    'breakfast': '🌅',
    'lunch': '🌞',
    'dinner': '🌙',
    'snack': '🍿',
    'dessert': '🍰',
    'vegetarian': '🥗',
    'non-vegetarian': '🍖'
  }
  return emojiMap[category] || '🍽️'
}

const getIngredientsCount = (ingredients) => {
  if (Array.isArray(ingredients)) {
    return ingredients.length
  }
  if (typeof ingredients === 'string') {
    try {
      const parsed = JSON.parse(ingredients)
      return Array.isArray(parsed) ? parsed.length : 0
    } catch {
      return 0
    }
  }
  return 0
}

// UPDATED viewRecipe function with login check
const viewRecipe = async () => {
  // Check if user is logged in
  if (!isLoggedIn()) {
    showLoginPrompt.value = true
    return // Don't navigate, just show popup
  }

  // User is logged in, proceed with view
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.post(`/api/recipes/${props.recipe.id}/view`, {}, {
      headers: { 'Authorization': `Bearer ${token}` }
    })

    if (response.data.success) {
      emit('view-recorded', props.recipe.id, response.data.views_count)
    }
  } catch (error) {
    console.error('Error recording view:', error)
  }

  // Navigate to PUBLIC recipe view
  router.push(`/public/recipe/${props.recipe.id}`)
}

// Close login prompt
const closeLoginPrompt = () => {
  showLoginPrompt.value = false
}

// Go to login page
const goToLogin = () => {
  router.push('/login')
}

const handleLikeToggled = (isLiked, likesCount) => {
  emit('like-toggled', props.recipe.id, isLiked, likesCount)
}
</script>

<style scoped>
@supports (-webkit-line-clamp: 2) {
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
}

/* Modal backdrop blur */
.fixed.inset-0 {
  backdrop-filter: blur(4px);
}
</style>

<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">

    <!-- Login Prompt Modal for Logged Out Users -->
    <div v-if="showLoginPrompt" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-center">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="text-2xl">🔐</span>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Login Required</h3>
        <p class="text-gray-600 mb-6">Please login to like recipes and access more features!</p>
        <div class="flex gap-3">
          <button
            @click="showLoginPrompt = false"
            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors"
          >
            Continue Browsing
          </button>
          <router-link
            to="/login"
            class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-4 py-2 rounded-lg font-medium transition-all text-center"
          >
            Login Now
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
          <span class="text-2xl">🍳</span>
        </div>
        <p class="text-xl text-gray-600">Loading delicious recipe...</p>
      </div>
    </div>

    <!-- Recipe Content -->
    <div v-else-if="recipe" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Header with Back Button -->
      <div class="flex items-center justify-between mb-8">
        <button
          @click="goBack"
          class="flex items-center space-x-2 text-gray-600 hover:text-orange-500 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
          <span>Back to Feed</span>
        </button>

        <!-- Share & Actions -->
        <div class="flex items-center space-x-4">
          <!-- Like Button with Login Check -->
          <button
            @click="handleLikeClick"
            class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-all duration-200 hover:bg-gray-50"
            :class="[
              recipe.is_liked
                ? 'text-red-500 hover:text-red-600'
                : 'text-gray-500 hover:text-red-500'
            ]"
          >
            <span class="text-lg">{{ recipe.is_liked ? '❤️' : '🤍' }}</span>
            <span class="text-sm font-medium">{{ recipe.likes_count || 0 }}</span>
            <span class="text-sm hidden sm:inline">{{ recipe.is_liked ? 'Liked' : 'Like' }}</span>
          </button>

          <!-- View Count -->
          <div class="flex items-center space-x-1 text-gray-500">
            <span>👁️</span>
            <span class="text-sm">{{ recipe.views_count || 0 }} views</span>
          </div>
        </div>
      </div>

      <!-- Recipe Header -->
      <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">

        <!-- Recipe Image -->
        <div class="h-64 md:h-80 bg-gradient-to-br from-orange-200 via-red-200 to-pink-200 flex items-center justify-center overflow-hidden">
          <template v-if="recipe.image">
            <img :src="`/storage/${recipe.image}`" alt="Recipe Image" class="object-cover w-full h-full" />
          </template>
          <template v-else>
            <span class="text-8xl">🍽️</span>
          </template>
        </div>

        <!-- Recipe Info -->
        <div class="p-8">

          <!-- Title & Category -->
          <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
              <h1 class="text-4xl font-bold text-gray-900">{{ recipe.title }}</h1>
              <span v-if="recipe.category" class="inline-flex items-center bg-gradient-to-r from-orange-100 to-red-100 text-orange-800 text-sm font-medium px-4 py-2 rounded-full">
                {{ getCategoryEmoji(recipe.category) }} {{ recipe.category }}
              </span>
            </div>
            <p class="text-xl text-gray-600 leading-relaxed">{{ recipe.description }}</p>
          </div>

          <!-- Author Info -->
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl mb-6">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-red-400 rounded-full flex items-center justify-center text-white font-bold">
                {{ getUserInitials(recipe.user.name) }}
              </div>
              <div>
                <h4 class="font-semibold text-gray-900">{{ recipe.user.name }}</h4>
                <p class="text-sm text-gray-500">Created {{ formatDate(recipe.created_at) }}</p>
              </div>
            </div>

            <!-- Follow/Connect Button -->
            <button
              @click="handleFollowClick"
              class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
            >
              👤 View Profile
            </button>
          </div>

          <!-- Recipe Stats -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-orange-50 rounded-xl p-4 text-center">
              <div class="text-orange-500 text-2xl mb-2">⏱️</div>
              <div class="text-sm text-gray-600">Prep Time</div>
              <div class="font-bold text-gray-900">{{ recipe.prep_time || 0 }} min</div>
            </div>
            <div class="bg-red-50 rounded-xl p-4 text-center">
              <div class="text-red-500 text-2xl mb-2">🔥</div>
              <div class="text-sm text-gray-600">Cook Time</div>
              <div class="font-bold text-gray-900">{{ recipe.cook_time || 0 }} min</div>
            </div>
            <div class="bg-blue-50 rounded-xl p-4 text-center">
              <div class="text-blue-500 text-2xl mb-2">👥</div>
              <div class="text-sm text-gray-600">Servings</div>
              <div class="font-bold text-gray-900">{{ recipe.servings || 1 }}</div>
            </div>
            <div class="bg-green-50 rounded-xl p-4 text-center">
              <div class="text-green-500 text-2xl mb-2">🥕</div>
              <div class="text-sm text-gray-600">Ingredients</div>
              <div class="font-bold text-gray-900">{{ getIngredientsCount(recipe.ingredients) }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ingredients & Instructions -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Ingredients -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="mr-3">🥕</span>
            Ingredients
          </h2>
          <ul class="space-y-3">
            <li
              v-for="(ingredient, index) in parsedIngredients"
              :key="index"
              class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <div class="w-6 h-6 bg-orange-500 text-white rounded-full flex items-center justify-center text-xs font-bold">
                {{ index + 1 }}
              </div>
              <span class="text-gray-700">{{ ingredient }}</span>
            </li>
          </ul>
        </div>

        <!-- Instructions -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="mr-3">📝</span>
            Instructions
          </h2>
          <ol class="space-y-4">
            <li
              v-for="(instruction, index) in parsedInstructions"
              :key="index"
              class="flex space-x-4"
            >
              <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 mt-1">
                {{ index + 1 }}
              </div>
              <div class="flex-1 p-4 bg-gray-50 rounded-lg">
                <p class="text-gray-700 leading-relaxed">{{ instruction }}</p>
              </div>
            </li>
          </ol>
        </div>
      </div>

      <!-- CONDITIONAL Call to Action -->
      <!-- For Logout Users -->
      <div v-if="!isLoggedIn" class="mt-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl p-8 text-white text-center">
        <h3 class="text-2xl font-bold mb-4">Love this recipe? 😍</h3>
        <p class="text-orange-100 mb-6">Join RecipeHub to create your own recipes and connect with fellow food lovers!</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link
            to="/register"
            class="bg-white text-orange-500 px-8 py-3 rounded-xl font-bold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
          >
            🚀 Join RecipeHub
          </router-link>
          <router-link
            to="/login"
            class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-8 py-3 rounded-xl font-bold transition-all duration-200 border border-white border-opacity-30"
          >
            🔑 Login
          </router-link>
        </div>
      </div>

      <!-- For Login Users -->
      <div v-else class="mt-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-2xl p-8 text-white text-center">
        <h3 class="text-2xl font-bold mb-4">Enjoyed this recipe? ✨</h3>
        <p class="text-green-100 mb-6">Create your own amazing recipes and share with the community!</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link
            to="/recipes/create"
            class="bg-white text-green-500 px-8 py-3 rounded-xl font-bold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
          >
            ✨ Create Recipe
          </router-link>
          <router-link
            to="/dashboard"
            class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-8 py-3 rounded-xl font-bold transition-all duration-200 border border-white border-opacity-30"
          >
            📊 My Dashboard
          </router-link>
        </div>
      </div>

      <!-- Back to Feed -->
      <div class="mt-8 text-center">
        <button
          @click="goBack"
          class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl"
        >
          ← Back to Community Feed
        </button>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <div class="w-32 h-32 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-8">
          <span class="text-6xl">😞</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-800 mb-4">Recipe Not Found</h3>
        <p class="text-xl text-gray-600 mb-8">The recipe you're looking for doesn't exist or has been removed.</p>
        <button
          @click="goBack"
          class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-200 shadow-lg hover:shadow-xl"
        >
          ← Go Back to Feed
        </button>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

// State
const loading = ref(true)
const recipe = ref(null)
const showLoginPrompt = ref(false)

// Check if user is logged in
const isLoggedIn = computed(() => {
  return !!localStorage.getItem('auth_token')
})

// Computed
const parsedIngredients = computed(() => {
  if (!recipe.value?.ingredients) return []

  if (Array.isArray(recipe.value.ingredients)) {
    return recipe.value.ingredients
  }

  if (typeof recipe.value.ingredients === 'string') {
    try {
      return JSON.parse(recipe.value.ingredients)
    } catch {
      return recipe.value.ingredients.split('\n').filter(item => item.trim())
    }
  }

  return []
})

const parsedInstructions = computed(() => {
  if (!recipe.value?.instructions) return []

  if (Array.isArray(recipe.value.instructions)) {
    return recipe.value.instructions
  }

  if (typeof recipe.value.instructions === 'string') {
    try {
      return JSON.parse(recipe.value.instructions)
    } catch {
      return recipe.value.instructions.split('\n').filter(item => item.trim())
    }
  }

  return []
})

// Methods
const fetchRecipe = async () => {
  try {
    loading.value = true
    const recipeId = route.params.id

    // Try authenticated request first (to get like status)
    const token = localStorage.getItem('auth_token')
    const headers = token ? { 'Authorization': `Bearer ${token}` } : {}

    const response = await axios.get(`/api/recipes/${recipeId}`, { headers })
    recipe.value = response.data.data || response.data

    console.log('Recipe loaded:', recipe.value)

  } catch (error) {
    console.error('Error fetching recipe:', error)

    // If authenticated request fails, try public request
    if (error.response?.status === 401) {
      try {
        const response = await axios.get(`/api/public/recipes/${route.params.id}`)
        recipe.value = response.data.data || response.data
        console.log('Public recipe loaded:', recipe.value)
      } catch (publicError) {
        console.error('Public recipe fetch failed:', publicError)
        recipe.value = null
      }
    } else {
      recipe.value = null
    }
  } finally {
    loading.value = false
  }
}

const handleLikeClick = async () => {
  if (!isLoggedIn.value) {
    showLoginPrompt.value = true
    return
  }

  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.post(`/api/recipes/${recipe.value.id}/like`, {}, {
      headers: { 'Authorization': `Bearer ${token}` }
    })

    if (response.data.success) {
      recipe.value.is_liked = response.data.is_liked
      recipe.value.likes_count = response.data.likes_count
    }
  } catch (error) {
    console.error('Error toggling like:', error)
    if (error.response?.status === 401) {
      showLoginPrompt.value = true
    }
  }
}

const handleFollowClick = () => {
  if (!isLoggedIn.value) {
    showLoginPrompt.value = true
    return
  }

  // TODO: Implement user profile view
  console.log('View user profile:', recipe.value.user.id)
  alert('User profile feature coming soon! 👤')
}

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
    return 'yesterday'
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
      return ingredients.split('\n').filter(item => item.trim()).length
    }
  }

  return 0
}

const goBack = () => {
  // Try to go back, fallback to feed
  if (window.history.length > 1) {
    router.go(-1)
  } else {
    router.push('/feed')
  }
}

// Lifecycle
onMounted(() => {
  fetchRecipe()
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

/* Modal backdrop blur */
.fixed.inset-0 {
  backdrop-filter: blur(4px);
}

/* Smooth transitions */
button {
  transition: all 0.2s ease;
}

button:hover:not(:disabled) {
  transform: translateY(-1px);
}

button:active:not(:disabled) {
  transform: translateY(0);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>

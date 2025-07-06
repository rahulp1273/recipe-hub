<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 shadow-lg">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-orange-500 text-2xl">🍳</span>
            </div>
            <div>
              <h1 class="text-3xl font-bold text-white">Recipe Feed</h1>
              <p class="text-orange-100 text-sm">Discover amazing recipes from our community</p>
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <router-link
              to="/recipes/create"
              class="bg-white bg-opacity-20 hover:bg-white hover:bg-opacity-30 text-white px-4 py-2 rounded-xl font-medium transition-all duration-200 border border-white border-opacity-30"
            >
              ➕ Add Recipe
            </router-link>
            <router-link
              to="/dashboard"
              class="bg-white bg-opacity-20 hover:bg-white hover:bg-opacity-30 text-white px-4 py-2 rounded-xl font-medium transition-all duration-200 border border-white border-opacity-30"
            >
              📊 Dashboard
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-16">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
          <span class="text-2xl">🍳</span>
        </div>
        <p class="text-xl text-gray-600">Loading delicious recipes...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="recipes.length === 0" class="text-center py-16">
        <div class="w-32 h-32 bg-gradient-to-br from-orange-100 to-red-100 rounded-full flex items-center justify-center mx-auto mb-8">
          <span class="text-6xl">🍽️</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-800 mb-4">No recipes yet!</h3>
        <p class="text-xl text-gray-600 mb-8 max-w-md mx-auto">
          Be the first to share a recipe with the community!
        </p>
        <router-link
          to="/recipes/create"
          class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 inline-flex items-center space-x-2"
        >
          <span>✨</span>
          <span>Create First Recipe</span>
        </router-link>
      </div>

      <!-- Recipe Feed -->
      <div v-else class="space-y-6">
        <RecipeCard
          v-for="recipe in recipes"
          :key="recipe.id"
          :recipe="recipe"
          @like-toggled="handleLikeToggled"
          @view-recorded="handleViewRecorded"
        />

        <!-- Load More Button -->
        <div v-if="hasMorePages" class="text-center py-8">
          <button
            @click="loadMore"
            :disabled="loadingMore"
            class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 disabled:opacity-50 disabled:transform-none"
          >
            {{ loadingMore ? '⏳ Loading...' : '📖 Load More Recipes' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import RecipeCard from '@/components/recipe/RecipeCard.vue'

const router = useRouter()

// State
const loading = ref(true)
const loadingMore = ref(false)
const recipes = ref([])
const currentPage = ref(1)
const hasMorePages = ref(false)

// Methods
const fetchFeed = async (page = 1) => {
  try {
    console.log('Fetching feed, page:', page)
    
    if (page === 1) {
      loading.value = true
    } else {
      loadingMore.value = true
    }

    const token = localStorage.getItem('auth_token')
    const headers = token ? { 'Authorization': `Bearer ${token}` } : {}
    
    console.log('Making API request to /api/feed')
    const response = await axios.get(`/api/feed?page=${page}`, { headers })
    console.log('API response received')

    const data = response.data.data

    if (page === 1) {
      recipes.value = data.data
    } else {
      recipes.value.push(...data.data)
    }

    currentPage.value = data.current_page
    hasMorePages.value = data.current_page < data.last_page

    console.log('Feed loaded:', data)

  } catch (error) {
    console.error('Error fetching feed:', error)
    if (error.response?.status === 401) {
      // Not logged in - still show public feed
      console.log('Showing public feed')
    }
  } finally {
    loading.value = false
    loadingMore.value = false
  }
}

const loadMore = () => {
  if (!loadingMore.value && hasMorePages.value) {
    fetchFeed(currentPage.value + 1)
  }
}

const handleLikeToggled = (recipeId, isLiked, likesCount) => {
  const recipe = recipes.value.find(r => r.id === recipeId)
  if (recipe) {
    recipe.is_liked = isLiked
    recipe.likes_count = likesCount
  }
}

const handleViewRecorded = (recipeId, viewsCount) => {
  const recipe = recipes.value.find(r => r.id === recipeId)
  if (recipe) {
    recipe.views_count = viewsCount
  }
}

// Lifecycle
onMounted(() => {
  console.log('HomeFeed component mounted')
  console.log('Current route:', router.currentRoute.value.path)
  console.log('Auth token:', localStorage.getItem('auth_token') ? 'Present' : 'Not present')
  
  // Check if we're actually on the feed route
  if (router.currentRoute.value.path !== '/feed') {
    console.error('Not on feed route! Current route:', router.currentRoute.value.path)
    return
  }
  
  fetchFeed()
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
</style>

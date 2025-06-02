<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <!-- Title with Icon -->
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-orange-500 text-2xl">🍳</span>
            </div>
            <div>
              <h1 class="text-3xl font-bold text-white">My Recipes</h1>
              <p class="text-orange-100 text-sm">{{ recipes.length }} {{ recipes.length === 1 ? 'recipe' : 'recipes' }} in your collection</p>
            </div>
          </div>

          <!-- Add Recipe Button -->
          <router-link
            to="/recipes/create"
            class="bg-white bg-opacity-20 hover:bg-white hover:bg-opacity-30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 border border-white border-opacity-30 flex items-center space-x-2 shadow-lg hover:shadow-xl"
          >
            <span class="text-lg">➕</span>
            <span>Add New Recipe</span>
          </router-link>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-16">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
          <span class="text-2xl">🍳</span>
        </div>
        <p class="text-xl text-gray-600">Loading your delicious recipes...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="recipes.length === 0" class="text-center py-16">
        <div class="w-32 h-32 bg-gradient-to-br from-orange-100 to-red-100 rounded-full flex items-center justify-center mx-auto mb-8">
          <span class="text-6xl">🍽️</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-800 mb-4">No recipes yet!</h3>
        <p class="text-xl text-gray-600 mb-8 max-w-md mx-auto">
          Start building your personal cookbook by creating your first recipe.
        </p>
        <router-link
          to="/recipes/create"
          class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 inline-flex items-center space-x-2"
        >
          <span>✨</span>
          <span>Create Your First Recipe</span>
        </router-link>
      </div>

      <!-- Recipes Grid -->
      <div v-else>
        <!-- Filter/Sort Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 bg-white rounded-xl p-4 shadow-md">
          <div class="flex items-center space-x-2 mb-4 sm:mb-0">
            <span class="text-lg">🔍</span>
            <span class="font-medium text-gray-700">{{ recipes.length }} recipes found</span>
          </div>
          <div class="flex items-center space-x-4">
            <router-link
              to="/dashboard"
              class="text-gray-600 hover:text-orange-500 font-medium transition-colors"
            >
              ← Back to Dashboard
            </router-link>
          </div>
        </div>

        <!-- Recipe Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="recipe in recipes"
            :key="recipe.id"
            class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-orange-200 transform hover:-translate-y-1"
          >
            <!-- Recipe Image Placeholder -->
            <div class="h-48 bg-gradient-to-br from-orange-200 via-red-200 to-pink-200 flex items-center justify-center">
              <span class="text-5xl">🍽️</span>
            </div>

            <!-- Recipe Content -->
            <div class="p-6">
              <!-- Title -->
              <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-1">
                {{ recipe.title || 'Untitled Recipe' }}
              </h3>

              <!-- Description -->
              <p class="text-gray-600 mb-4 line-clamp-2 text-sm leading-relaxed">
                {{ recipe.description || 'No description available' }}
              </p>

              <!-- Category Badge -->
              <div v-if="recipe.category" class="mb-4">
                <span class="inline-flex items-center bg-gradient-to-r from-orange-100 to-red-100 text-orange-800 text-xs font-medium px-3 py-1 rounded-full">
                  {{ getCategoryEmoji(recipe.category) }} {{ recipe.category }}
                </span>
              </div>

              <!-- Recipe Stats -->
              <div class="grid grid-cols-3 gap-2 mb-4 text-xs">
                <div class="bg-gray-50 rounded-lg p-2 text-center">
                  <div class="text-orange-500 font-bold">⏱️</div>
                  <div class="text-gray-600">{{ recipe.prep_time || 0 }}min</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-2 text-center">
                  <div class="text-red-500 font-bold">🔥</div>
                  <div class="text-gray-600">{{ recipe.cook_time || 0 }}min</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-2 text-center">
                  <div class="text-blue-500 font-bold">👥</div>
                  <div class="text-gray-600">{{ recipe.servings || 1 }}</div>
                </div>
              </div>

              <!-- Ingredients Count -->
              <div class="flex items-center space-x-1 text-sm text-gray-500 mb-4">
                <span>🥕</span>
                <span>{{ getIngredientsCount(recipe.ingredients) }} ingredients</span>
              </div>

              <!-- Action Buttons -->
              <div class="flex space-x-2">
                <button class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                  👁️ View
                </button>
                <button class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                  ✏️ Edit
                </button>
                <button
                  @click="deleteRecipe(recipe.id)"
                  class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg"
                >
                  🗑️ Delete
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Load More Button (if needed) -->
        <div class="text-center mt-12">
          <router-link
            to="/recipes/create"
            class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 inline-flex items-center space-x-2"
          >
            <span>➕</span>
            <span>Create Another Recipe</span>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const recipes = ref([])
const loading = ref(true)

const fetchRecipes = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/my-recipes')

    // Handle different response structures
    if (response.data.data && response.data.data.data) {
      recipes.value = response.data.data.data // Paginated response
    } else if (response.data.data) {
      recipes.value = response.data.data // Direct data
    } else {
      recipes.value = response.data // Raw data
    }

  } catch (error) {
    console.error('Error fetching recipes:', error)
    if (error.response?.status === 401) {
      router.push('/login')
    }
  } finally {
    loading.value = false
  }
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

const deleteRecipe = async (id) => {
  if (confirm('Are you sure you want to delete this recipe? This action cannot be undone.')) {
    try {
      const token = localStorage.getItem('auth_token')
      await axios.delete(`/api/recipes/${id}`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
      fetchRecipes() // Refresh list
    } catch (error) {
      console.error('Error deleting recipe:', error)
      alert('Failed to delete recipe. Please try again.')
    }
  }
}

onMounted(() => {
  fetchRecipes()
})
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

@supports (-webkit-line-clamp: 1) {
  .line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
}

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

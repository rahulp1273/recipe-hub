<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo & Brand -->
          <div class="flex items-center space-x-4">
            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
              <span class="text-white text-xl font-bold">🍳</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">RecipeHub</h1>
          </div>

          <!-- Navigation Links -->
          <div class="hidden md:flex items-center space-x-8">
            <router-link to="/dashboard" class="text-gray-700 hover:text-orange-500 font-medium">
              Home
            </router-link>
            <router-link to="/recipes" class="text-gray-700 hover:text-orange-500 font-medium">
              My Recipes
            </router-link>
            <router-link to="/recipes/create" class="text-gray-700 hover:text-orange-500 font-medium">
              Create Recipe
            </router-link>
          </div>

          <!-- User Menu -->
          <div class="flex items-center space-x-4">
            <div class="hidden md:flex items-center space-x-3">
              <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                <span class="text-sm font-medium text-gray-600">{{ userInitials }}</span>
              </div>
              <span class="text-sm text-gray-700">{{ userName }}</span>
            </div>
            <button
              @click="logout"
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Welcome Section -->
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Dashboard</h2>
        <p class="text-gray-600">Manage your recipes and create new ones</p>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
        <!-- My Recipes -->
        <div class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-lg transition-shadow">
          <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-6">
            <span class="text-3xl">📝</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ myRecipesCount }}</h3>
          <p class="text-gray-600 mb-6">My Recipes</p>
          <router-link
            to="/recipes"
            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors inline-block"
          >
            View My Recipes
          </router-link>
        </div>

        <!-- Create Recipe -->
        <div class="bg-white rounded-xl shadow-md p-8 text-center hover:shadow-lg transition-shadow">
          <div class="w-16 h-16 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-6">
            <span class="text-3xl">➕</span>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">Create</h3>
          <p class="text-gray-600 mb-6">Add New Recipe</p>
          <router-link
            to="/recipes/create"
            class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors inline-block"
          >
            Create Recipe
          </router-link>
        </div>
      </div>

      <!-- Recent Recipes (if any) -->
      <div v-if="recentRecipes.length > 0" class="mt-16">
        <h3 class="text-2xl font-bold text-gray-900 mb-8 text-center">Recent Recipes</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
          <div v-for="recipe in recentRecipes" :key="recipe.id" class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <div class="h-48 bg-gradient-to-br from-orange-200 to-red-200 flex items-center justify-center">
              <span class="text-4xl">🍽️</span>
            </div>
            <div class="p-6">
              <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ recipe.title }}</h4>
              <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ recipe.description }}</p>
              <div class="flex justify-between text-sm text-gray-500">
                <span>{{ recipe.prep_time }}min prep</span>
                <span>{{ recipe.servings }} servings</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="mt-16 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <span class="text-4xl">🍳</span>
        </div>
        <h4 class="text-xl font-semibold text-gray-900 mb-2">No recipes yet</h4>
        <p class="text-gray-600 mb-8">Start creating your first recipe!</p>
        <router-link
          to="/recipes/create"
          class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg font-semibold transition-colors text-lg"
        >
          Create Your First Recipe
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

// User data
const userName = ref('Test User')
const userInitials = computed(() => {
  return userName.value.split(' ').map(n => n[0]).join('').toUpperCase()
})

// Recipes
const myRecipesCount = ref(0)
const recentRecipes = ref([])

const fetchDashboardData = async () => {
  try {
    const recipesResponse = await axios.get('/api/my-recipes')
    const recipes = recipesResponse.data.data?.data || recipesResponse.data.data || []

    myRecipesCount.value = recipes.length
    recentRecipes.value = recipes.slice(0, 3) // Show only 3 recent

  } catch (error) {
    console.error('Error fetching dashboard data:', error)
  }
}

const logout = () => {
  localStorage.removeItem('auth_token')
  router.push('/login')
}

onMounted(() => {
  fetchDashboardData()
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
}</style>

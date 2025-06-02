<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-900">My Recipes</h1>
          <router-link to="/recipes/create" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
            Add New Recipe
          </router-link>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8">
          <p class="text-gray-600">Loading recipes...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="recipes.length === 0" class="text-center py-8">
          <p class="text-gray-600 mb-4">No recipes found</p>
          <router-link to="/recipes/create" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
            Create Your First Recipe
          </router-link>
        </div>

        <!-- Recipes Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="recipe in recipes" :key="recipe.id" class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <!-- Recipe Card -->
            <div class="p-6">
              <!-- Title -->
              <h3 class="text-xl font-semibold text-gray-900 mb-2">
                {{ recipe.title || 'Untitled Recipe' }}
              </h3>

              <!-- Description -->
              <p class="text-gray-600 mb-4 line-clamp-2">
                {{ recipe.description || 'No description available' }}
              </p>

              <!-- Category -->
              <div v-if="recipe.category" class="mb-3">
                <span class="inline-block bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">
                  {{ recipe.category }}
                </span>
              </div>

              <!-- Time & Servings Info -->
              <div class="flex justify-between text-sm text-gray-500 mb-4">
                <span>Prep: {{ recipe.prep_time || 0 }}min</span>
                <span>Cook: {{ recipe.cook_time || 0 }}min</span>
                <span>Serves: {{ recipe.servings || 1 }}</span>
              </div>

              <!-- Ingredients Count -->
              <div class="text-sm text-gray-500 mb-4">
                <span>{{ getIngredientsCount(recipe.ingredients) }} ingredients</span>
              </div>

              <!-- Action Buttons -->
              <div class="flex space-x-2">
                <button class="flex-1 bg-blue-500 text-white px-3 py-2 rounded text-sm hover:bg-blue-600">
                  View
                </button>
                <button class="flex-1 bg-green-500 text-white px-3 py-2 rounded text-sm hover:bg-green-600">
                  Edit
                </button>
                <button @click="deleteRecipe(recipe.id)" class="flex-1 bg-red-500 text-white px-3 py-2 rounded text-sm hover:bg-red-600">
                  Delete
                </button>
              </div>
            </div>
          </div>
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

    console.log('API Response:', response.data) // Debug

    // Handle different response structures
    if (response.data.data && response.data.data.data) {
      recipes.value = response.data.data.data // Paginated response
    } else if (response.data.data) {
      recipes.value = response.data.data // Direct data
    } else {
      recipes.value = response.data // Raw data
    }

    console.log('Recipes:', recipes.value) // Debug

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

const deleteRecipe = async (id) => {
  if (confirm('Are you sure you want to delete this recipe?')) {
    try {
      await axios.delete(`/api/recipes/${id}`)
      fetchRecipes() // Refresh list
    } catch (error) {
      console.error('Error deleting recipe:', error)
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
}</style>

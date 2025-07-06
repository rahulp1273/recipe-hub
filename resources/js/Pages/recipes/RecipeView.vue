<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <!-- Title with Icon -->
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-orange-500 text-2xl">👁️</span>
            </div>
            <h1 class="text-3xl font-bold text-white">Recipe Details</h1>
          </div>

          <!-- Back Button -->
          <router-link
            to="/recipes"
            class="bg-white bg-opacity-20 hover:bg-white hover:bg-opacity-30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 border border-white border-opacity-30 flex items-center space-x-2"
          >
            <span>←</span>
            <span>Back to Recipes</span>
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="max-w-4xl mx-auto px-4 py-8">
      <div class="text-center py-16">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
          <span class="text-2xl">🍳</span>
        </div>
        <p class="text-xl text-gray-600">Loading recipe...</p>
      </div>
    </div>

    <!-- Recipe Content -->
    <div v-else-if="recipe" class="max-w-4xl mx-auto px-4 py-8">
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Recipe Image -->
        <div class="h-64 bg-gradient-to-br from-orange-200 via-red-200 to-pink-200 flex items-center justify-center">
          <span class="text-8xl">🍽️</span>
        </div>

        <!-- Recipe Info -->
        <div class="p-8">
          <!-- Title and Category -->
          <div class="mb-6">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ recipe.title }}</h1>
            <div v-if="recipe.category" class="inline-flex items-center bg-gradient-to-r from-orange-100 to-red-100 text-orange-800 text-sm font-medium px-4 py-2 rounded-full">
              {{ getCategoryEmoji(recipe.category) }} {{ recipe.category }}
            </div>
          </div>

          <!-- Description -->
          <div v-if="recipe.description" class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-3">📝 Description</h3>
            <p class="text-gray-600 leading-relaxed">{{ recipe.description }}</p>
          </div>

          <!-- Recipe Stats -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-orange-50 rounded-xl p-4 text-center">
              <div class="text-2xl mb-2">⏱️</div>
              <div class="text-sm text-gray-600">Prep Time</div>
              <div class="text-xl font-bold text-orange-600">{{ recipe.prep_time || 0 }} min</div>
            </div>
            <div class="bg-red-50 rounded-xl p-4 text-center">
              <div class="text-2xl mb-2">🔥</div>
              <div class="text-sm text-gray-600">Cook Time</div>
              <div class="text-xl font-bold text-red-600">{{ recipe.cook_time || 0 }} min</div>
            </div>
            <div class="bg-blue-50 rounded-xl p-4 text-center">
              <div class="text-2xl mb-2">👥</div>
              <div class="text-sm text-gray-600">Servings</div>
              <div class="text-xl font-bold text-blue-600">{{ recipe.servings || 1 }}</div>
            </div>
          </div>

          <!-- Ingredients -->
          <div class="mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
              <span class="mr-2">🥕</span>
              Ingredients
            </h3>
            <div class="bg-gray-50 rounded-xl p-6">
              <ul class="space-y-3">
                <li v-for="(ingredient, index) in getIngredientsList(recipe.ingredients)" :key="index" class="flex items-center space-x-3">
                  <span class="w-6 h-6 bg-orange-500 text-white rounded-full flex items-center justify-center text-sm font-bold">{{ index + 1 }}</span>
                  <span class="text-gray-700">{{ ingredient }}</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Instructions -->
          <div class="mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
              <span class="mr-2">📋</span>
              Instructions
            </h3>
            <div class="space-y-4">
              <div v-for="(instruction, index) in getInstructionsList(recipe.instructions)" :key="index" class="flex space-x-4">
                <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0">
                  {{ index + 1 }}
                </div>
                <div class="bg-gray-50 rounded-xl p-4 flex-1">
                  <p class="text-gray-700 leading-relaxed">{{ instruction }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
            <router-link
              :to="`/recipes/${recipe.id}/edit`"
              class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 text-center flex items-center justify-center space-x-2"
            >
              <span>✏️</span>
              <span>Edit Recipe</span>
            </router-link>
            <button
              @click="deleteRecipe"
              class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 flex items-center justify-center space-x-2"
            >
              <span>🗑️</span>
              <span>Delete Recipe</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Comments Section -->
      <div class="mt-8">
        <CommentSection :recipe-id="recipe.id" />
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="max-w-4xl mx-auto px-4 py-8">
      <div class="text-center py-16">
        <div class="w-32 h-32 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-8">
          <span class="text-6xl">❌</span>
        </div>
        <h3 class="text-3xl font-bold text-gray-800 mb-4">Recipe not found!</h3>
        <p class="text-xl text-gray-600 mb-8">The recipe you're looking for doesn't exist.</p>
        <router-link
          to="/recipes"
          class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200"
        >
          Back to Recipes
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import CommentSection from '../../components/recipe/CommentSection.vue'

const route = useRoute()
const router = useRouter()

const recipe = ref(null)
const loading = ref(true)

const fetchRecipe = async () => {
  try {
    loading.value = true
    const token = localStorage.getItem('auth_token')

    if (!token) {
      router.push('/login')
      return
    }

    const response = await axios.get(`/api/recipes/${route.params.id}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    recipe.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching recipe:', error)
    if (error.response?.status === 401) {
      router.push('/login')
    } else if (error.response?.status === 404) {
      recipe.value = null
    }
  } finally {
    loading.value = false
  }
}

const getIngredientsList = (ingredients) => {
  if (Array.isArray(ingredients)) {
    return ingredients
  }
  if (typeof ingredients === 'string') {
    try {
      return JSON.parse(ingredients)
    } catch {
      return ingredients.split('\n').filter(item => item.trim())
    }
  }
  return []
}

const getInstructionsList = (instructions) => {
  if (Array.isArray(instructions)) {
    return instructions
  }
  if (typeof instructions === 'string') {
    try {
      return JSON.parse(instructions)
    } catch {
      return instructions.split('\n').filter(item => item.trim())
    }
  }
  return []
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

const deleteRecipe = async () => {
  if (confirm('Are you sure you want to delete this recipe? This action cannot be undone.')) {
    try {
      const token = localStorage.getItem('auth_token')
      await axios.delete(`/api/recipes/${recipe.value.id}`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
      router.push('/recipes')
    } catch (error) {
      console.error('Error deleting recipe:', error)
      alert('Failed to delete recipe. Please try again.')
    }
  }
}

onMounted(() => {
  fetchRecipe()
})
</script>

<style scoped>
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: .5; }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>

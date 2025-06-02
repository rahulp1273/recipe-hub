<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <!-- Title with Icon -->
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-orange-500 text-2xl">✏️</span>
            </div>
            <h1 class="text-3xl font-bold text-white">Edit Recipe</h1>
          </div>

          <!-- Back Button -->
          <router-link
            :to="`/recipes/${$route.params.id}`"
            class="bg-white bg-opacity-20 hover:bg-white hover:bg-opacity-30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 border border-white border-opacity-30 flex items-center space-x-2"
          >
            <span>←</span>
            <span>Back to Recipe</span>
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

    <!-- Form Container -->
    <div v-else class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
        <form @submit.prevent="updateRecipe" class="p-8 space-y-8">

          <!-- Recipe Title -->
          <div class="space-y-2">
            <label for="title" class="block text-sm font-semibold text-gray-800">Recipe Title</label>
            <input
              type="text"
              id="title"
              v-model="form.title"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
              placeholder="Enter an amazing recipe title..."
            />
          </div>

          <!-- Description -->
          <div class="space-y-2">
            <label for="description" class="block text-sm font-semibold text-gray-800">Description</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
              placeholder="Tell us what makes this recipe special..."
            ></textarea>
          </div>

          <!-- Category & Servings Row -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Category -->
            <div class="space-y-2">
              <label for="category" class="block text-sm font-semibold text-gray-800">Category</label>
              <select
                id="category"
                v-model="form.category"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
              >
                <option value="">Select Category</option>
                <option value="breakfast">🌅 Breakfast</option>
                <option value="lunch">🌞 Lunch</option>
                <option value="dinner">🌙 Dinner</option>
                <option value="snack">🍿 Snack</option>
                <option value="dessert">🍰 Dessert</option>
                <option value="vegetarian">🥗 Vegetarian</option>
                <option value="non-vegetarian">🍖 Non-Vegetarian</option>
              </select>
            </div>

            <!-- Servings -->
            <div class="space-y-2">
              <label for="servings" class="block text-sm font-semibold text-gray-800">Servings</label>
              <input
                type="number"
                id="servings"
                v-model="form.servings"
                min="1"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                placeholder="4"
              />
            </div>
          </div>

          <!-- Cooking Time -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label for="prep_time" class="block text-sm font-semibold text-gray-800">⏱️ Prep Time (minutes)</label>
              <input
                type="number"
                id="prep_time"
                v-model="form.prep_time"
                min="0"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                placeholder="15"
              />
            </div>
            <div class="space-y-2">
              <label for="cook_time" class="block text-sm font-semibold text-gray-800">🔥 Cook Time (minutes)</label>
              <input
                type="number"
                id="cook_time"
                v-model="form.cook_time"
                min="0"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                placeholder="30"
              />
            </div>
          </div>

          <!-- Ingredients Section -->
          <div class="space-y-4">
            <div class="flex items-center space-x-2">
              <span class="text-2xl">🥕</span>
              <label class="text-lg font-bold text-gray-800">Ingredients</label>
            </div>

            <div class="space-y-3">
              <div v-for="(ingredient, index) in form.ingredients" :key="index" class="flex gap-3 items-center">
                <span class="text-sm font-medium text-gray-500 w-8">{{ index + 1 }}.</span>
                <input
                  type="text"
                  v-model="form.ingredients[index]"
                  class="flex-1 px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                  :placeholder="`Ingredient ${index + 1}`"
                />
                <button
                  type="button"
                  @click="removeIngredient(index)"
                  class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg"
                  v-if="form.ingredients.length > 1"
                >
                  🗑️
                </button>
              </div>
            </div>

            <button
              type="button"
              @click="addIngredient"
              class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center space-x-2"
            >
              <span>➕</span>
              <span>Add Ingredient</span>
            </button>
          </div>

          <!-- Instructions Section -->
          <div class="space-y-4">
            <div class="flex items-center space-x-2">
              <span class="text-2xl">📝</span>
              <label class="text-lg font-bold text-gray-800">Instructions</label>
            </div>

            <div class="space-y-4">
              <div v-for="(instruction, index) in form.instructions" :key="index" class="flex gap-3">
                <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-2 rounded-xl font-bold text-sm flex items-center justify-center min-w-[3rem] h-fit">
                  {{ index + 1 }}
                </div>
                <textarea
                  v-model="form.instructions[index]"
                  rows="3"
                  class="flex-1 px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                  :placeholder="`Describe step ${index + 1} in detail...`"
                ></textarea>
                <button
                  type="button"
                  @click="removeInstruction(index)"
                  class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg h-fit"
                  v-if="form.instructions.length > 1"
                >
                  🗑️
                </button>
              </div>
            </div>

            <button
              type="button"
              @click="addInstruction"
              class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center space-x-2"
            >
              <span>➕</span>
              <span>Add Step</span>
            </button>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
            <router-link
              :to="`/recipes/${$route.params.id}`"
              class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-3 rounded-xl font-medium transition-all duration-200 text-center border border-gray-300"
            >
              Cancel
            </router-link>
            <button
              type="submit"
              :disabled="isLoading"
              class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-3 rounded-xl font-bold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
            >
              {{ isLoading ? '✨ Updating...' : '💾 Update Recipe' }}
            </button>
          </div>

          <!-- Messages -->
          <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl flex items-center space-x-2">
            <span>❌</span>
            <span>{{ error }}</span>
          </div>

          <div v-if="success" class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl flex items-center space-x-2">
            <span>✅</span>
            <span>{{ success }}</span>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

// Form data
const form = ref({
  title: '',
  description: '',
  category: '',
  prep_time: '',
  cook_time: '',
  servings: '',
  ingredients: [''],
  instructions: ['']
})

const loading = ref(true)
const isLoading = ref(false)
const error = ref('')
const success = ref('')

// Fetch recipe data for editing
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

    const recipe = response.data.data || response.data

    // Populate form with existing data
    form.value = {
      title: recipe.title || '',
      description: recipe.description || '',
      category: recipe.category || '',
      prep_time: recipe.prep_time || '',
      cook_time: recipe.cook_time || '',
      servings: recipe.servings || '',
      ingredients: parseArrayField(recipe.ingredients) || [''],
      instructions: parseArrayField(recipe.instructions) || ['']
    }

  } catch (error) {
    console.error('Error fetching recipe:', error)
    if (error.response?.status === 401) {
      router.push('/login')
    } else if (error.response?.status === 404) {
      router.push('/recipes')
    }
  } finally {
    loading.value = false
  }
}

// Parse array fields (ingredients/instructions)
const parseArrayField = (field) => {
  if (Array.isArray(field)) {
    return field.filter(item => item && item.trim())
  }
  if (typeof field === 'string') {
    try {
      const parsed = JSON.parse(field)
      return Array.isArray(parsed) ? parsed.filter(item => item && item.trim()) : []
    } catch {
      return field.split('\n').filter(item => item && item.trim())
    }
  }
  return []
}

// Add/Remove ingredients
const addIngredient = () => {
  form.value.ingredients.push('')
}

const removeIngredient = (index) => {
  form.value.ingredients.splice(index, 1)
}

// Add/Remove instructions
const addInstruction = () => {
  form.value.instructions.push('')
}

const removeInstruction = (index) => {
  form.value.instructions.splice(index, 1)
}

// Update recipe
const updateRecipe = async () => {
  isLoading.value = true
  error.value = ''
  success.value = ''

  try {
    const token = localStorage.getItem('auth_token')

    if (!token) {
      error.value = 'Please login first'
      router.push('/login')
      return
    }

    const response = await axios.put(`/api/recipes/${route.params.id}`, {
      title: form.value.title,
      description: form.value.description,
      category: form.value.category,
      prep_time: parseInt(form.value.prep_time) || null,
      cook_time: parseInt(form.value.cook_time) || null,
      servings: parseInt(form.value.servings) || null,
      ingredients: form.value.ingredients.filter(ingredient => ingredient.trim() !== ''),
      instructions: form.value.instructions.filter(instruction => instruction.trim() !== '')
    }, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    })

    success.value = 'Recipe updated successfully!'

    setTimeout(() => {
      router.push(`/recipes/${route.params.id}`)
    }, 2000)

  } catch (err) {
    console.error('Error:', err)

    if (err.response?.status === 401) {
      error.value = 'Session expired. Please login again.'
      localStorage.removeItem('auth_token')
      router.push('/login')
    } else {
      error.value = err.response?.data?.message || 'Failed to update recipe'
    }
  } finally {
    isLoading.value = false
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


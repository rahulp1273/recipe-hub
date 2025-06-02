<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <h1 class="text-3xl font-bold text-gray-900">Create New Recipe</h1>
          <router-link to="/dashboard" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Back to Dashboard
          </router-link>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="bg-white shadow rounded-lg">
        <form @submit.prevent="createRecipe" class="p-6 space-y-6">
          <!-- Recipe Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Recipe Title</label>
            <input
              type="text"
              id="title"
              v-model="form.title"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
              placeholder="Enter recipe title"
            />
          </div>

          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
              placeholder="Brief description of your recipe"
            ></textarea>
          </div>

          <!-- Category -->
          <div>
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <select
              id="category"
              v-model="form.category"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
            >
              <option value="">Select Category</option>
              <option value="breakfast">Breakfast</option>
              <option value="lunch">Lunch</option>
              <option value="dinner">Dinner</option>
              <option value="snack">Snack</option>
              <option value="dessert">Dessert</option>
              <option value="vegetarian">Vegetarian</option>
              <option value="non-vegetarian">Non-Vegetarian</option>
            </select>
          </div>

          <!-- Cooking Time -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label for="prep_time" class="block text-sm font-medium text-gray-700">Prep Time (minutes)</label>
              <input
                type="number"
                id="prep_time"
                v-model="form.prep_time"
                min="0"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                placeholder="15"
              />
            </div>
            <div>
              <label for="cook_time" class="block text-sm font-medium text-gray-700">Cook Time (minutes)</label>
              <input
                type="number"
                id="cook_time"
                v-model="form.cook_time"
                min="0"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                placeholder="30"
              />
            </div>
          </div>

          <!-- Servings -->
          <div>
            <label for="servings" class="block text-sm font-medium text-gray-700">Servings</label>
            <input
              type="number"
              id="servings"
              v-model="form.servings"
              min="1"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
              placeholder="4"
            />
          </div>

          <!-- Ingredients -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ingredients</label>
            <div class="space-y-2">
              <div v-for="(ingredient, index) in form.ingredients" :key="index" class="flex gap-2">
                <input
                  type="text"
                  v-model="form.ingredients[index]"
                  class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                  :placeholder="`Ingredient ${index + 1}`"
                />
                <button
                  type="button"
                  @click="removeIngredient(index)"
                  class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600"
                  v-if="form.ingredients.length > 1"
                >
                  Remove
                </button>
              </div>
            </div>
            <button
              type="button"
              @click="addIngredient"
              class="mt-2 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
            >
              Add Ingredient
            </button>
          </div>

          <!-- Instructions -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
            <div class="space-y-2">
              <div v-for="(instruction, index) in form.instructions" :key="index" class="flex gap-2">
                <span class="bg-orange-500 text-white px-3 py-2 rounded font-medium">{{ index + 1 }}</span>
                <textarea
                  v-model="form.instructions[index]"
                  rows="2"
                  class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                  :placeholder="`Step ${index + 1}`"
                ></textarea>
                <button
                  type="button"
                  @click="removeInstruction(index)"
                  class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600"
                  v-if="form.instructions.length > 1"
                >
                  Remove
                </button>
              </div>
            </div>
            <button
              type="button"
              @click="addInstruction"
              class="mt-2 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
            >
              Add Step
            </button>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end space-x-4">
            <router-link
              to="/dashboard"
              class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400"
            >
              Cancel
            </router-link>
            <button
              type="submit"
              :disabled="isLoading"
              class="bg-orange-500 text-white px-6 py-2 rounded hover:bg-orange-600 disabled:opacity-50"
            >
              {{ isLoading ? 'Creating...' : 'Create Recipe' }}
            </button>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ error }}
          </div>

          <!-- Success Message -->
          <div v-if="success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ success }}
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

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

const isLoading = ref(false)
const error = ref('')
const success = ref('')

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

// Create recipe - FIXED VERSION
const createRecipe = async () => {
  isLoading.value = true
  error.value = ''
  success.value = ''

  try {
    const token = localStorage.getItem('auth_token')

    // Check if token exists
    if (!token) {
      error.value = 'Please login first'
      router.push('/login')
      return
    }

    console.log('Token:', token) // Debug

    const response = await axios.post('/api/recipes', {
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

    success.value = 'Recipe created successfully!'
    console.log('Recipe created:', response.data)

    // Redirect to dashboard after 2 seconds
    setTimeout(() => {
      router.push('/dashboard')
    }, 2000)

  } catch (err) {
    console.error('Full error:', err)
    console.error('Response:', err.response)

    if (err.response?.status === 401) {
      error.value = 'Session expired. Please login again.'
      localStorage.removeItem('auth_token')
      router.push('/login')
    } else {
      error.value = err.response?.data?.message || 'Failed to create recipe'
    }
  } finally {
    isLoading.value = false
  }
}
</script>

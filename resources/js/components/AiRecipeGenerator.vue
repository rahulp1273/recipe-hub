<template>
  <div class="ai-recipe-generator bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
    <!-- Header -->
    <div class="text-center mb-6">
      <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
        <span class="text-3xl">🤖</span>
      </div>
      <h3 class="text-2xl font-bold text-gray-900 mb-2">AI Recipe Generator</h3>
      <p class="text-gray-600">Describe what you want to cook and let AI create a recipe for you!</p>
    </div>

    <!-- Generation Form -->
    <div class="space-y-4">
      <!-- Prompt Input -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          What would you like to cook?
        </label>
        <textarea
          v-model="prompt"
          @keydown.enter.prevent="generateRecipe"
          placeholder="e.g., 'A chocolate cake recipe' or 'Quick pasta dinner with chicken'"
          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors resize-none"
          rows="3"
          :disabled="isGenerating"
        ></textarea>
      </div>

      <!-- Quick Prompts -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Quick Suggestions:
        </label>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="suggestion in quickPrompts"
            :key="suggestion"
            @click="prompt = suggestion"
            class="px-3 py-1 bg-gray-100 hover:bg-purple-100 text-gray-700 hover:text-purple-700 rounded-lg text-sm font-medium transition-colors"
            :disabled="isGenerating"
          >
            {{ suggestion }}
          </button>
        </div>
      </div>

      <!-- Generate Button -->
      <button
        @click="generateRecipe"
        :disabled="!prompt.trim() || isGenerating"
        class="w-full bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 disabled:opacity-50 disabled:transform-none disabled:cursor-not-allowed"
      >
        <span v-if="isGenerating" class="flex items-center justify-center space-x-2">
          <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>Generating Recipe...</span>
        </span>
        <span v-else class="flex items-center justify-center space-x-2">
          <span>✨</span>
          <span>Generate Recipe</span>
        </span>
      </button>
    </div>

    <!-- Success Message -->
    <div v-if="generatedRecipe" class="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl">
      <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
          <span class="text-green-600">✅</span>
        </div>
        <div>
          <h4 class="font-semibold text-green-800">Recipe Generated Successfully!</h4>
          <p class="text-green-600 text-sm">{{ generatedRecipe.title }}</p>
        </div>
      </div>
      <div class="mt-3 flex space-x-2">
        <button
          @click="viewRecipe"
          class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
          View Recipe
        </button>
        <button
          @click="generateAnother"
          class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
          Generate Another
        </button>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="mt-6 p-4 bg-red-50 border border-red-200 rounded-xl">
      <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
          <span class="text-red-600">❌</span>
        </div>
        <div>
          <h4 class="font-semibold text-red-800">Generation Failed</h4>
          <p class="text-red-600 text-sm">{{ error }}</p>
        </div>
      </div>
    </div>

    <!-- AI Generated Recipes History -->
    <div v-if="aiRecipes.length > 0" class="mt-8">
      <h4 class="text-lg font-semibold text-gray-900 mb-4">Your AI Generated Recipes</h4>
      <div class="space-y-3">
        <div
          v-for="recipe in aiRecipes.slice(0, 3)"
          :key="recipe.id"
          class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
          @click="viewRecipeById(recipe.id)"
        >
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
              <span class="text-purple-600 text-sm">🤖</span>
            </div>
            <div>
              <h5 class="font-medium text-gray-900">{{ recipe.title }}</h5>
              <p class="text-sm text-gray-500">{{ formatDate(recipe.created_at) }}</p>
            </div>
          </div>
          <span class="text-gray-400">→</span>
        </div>
      </div>
      <div v-if="aiRecipes.length > 3" class="mt-3 text-center">
        <button
          @click="viewAllAiRecipes"
          class="text-purple-600 hover:text-purple-700 text-sm font-medium"
        >
          View all {{ aiRecipes.length }} AI recipes
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()

// State
const prompt = ref('')
const isGenerating = ref(false)
const generatedRecipe = ref(null)
const error = ref('')
const aiRecipes = ref([])

// Quick prompt suggestions
const quickPrompts = [
  'Chocolate cake recipe',
  'Quick pasta dinner',
  'Healthy salad',
  'Breakfast smoothie',
  'Vegetarian curry',
  'Easy dessert'
]

// Methods
const generateRecipe = async () => {
  if (!prompt.value.trim() || isGenerating.value) return

  isGenerating.value = true
  error.value = ''
  generatedRecipe.value = null

  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.post('/api/ai/generate-recipe', {
      prompt: prompt.value
    }, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    if (response.data.success) {
      generatedRecipe.value = response.data.data
      prompt.value = '' // Clear the prompt
      await fetchAiRecipes() // Refresh the list
    }
  } catch (err) {
    console.error('Error generating recipe:', err)
    error.value = err.response?.data?.message || 'Failed to generate recipe. Please try again.'
  } finally {
    isGenerating.value = false
  }
}

const fetchAiRecipes = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.get('/api/ai/generated-recipes', {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })

    if (response.data.success) {
      aiRecipes.value = response.data.data.data || []
    }
  } catch (err) {
    console.error('Error fetching AI recipes:', err)
  }
}

const viewRecipe = () => {
  if (generatedRecipe.value) {
    router.push(`/recipes/${generatedRecipe.value.id}`)
  }
}

const viewRecipeById = (recipeId) => {
  router.push(`/recipes/${recipeId}`)
}

const viewAllAiRecipes = () => {
  router.push('/recipes?ai_generated=true')
}

const generateAnother = () => {
  generatedRecipe.value = null
  error.value = ''
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString()
}

// Lifecycle
onMounted(() => {
  fetchAiRecipes()
})
</script> 
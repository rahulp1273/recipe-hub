<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">My Recipes</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="recipe in recipes" :key="recipe.id" class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-semibold mb-2">{{ recipe.title }}</h3>
            <p class="text-gray-600 mb-4">{{ recipe.description }}</p>
            <div class="flex justify-between text-sm text-gray-500">
              <span>Prep: {{ recipe.prep_time }}min</span>
              <span>Cook: {{ recipe.cook_time }}min</span>
              <span>Serves: {{ recipe.servings }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const recipes = ref([])

const fetchRecipes = async () => {
  try {
    const response = await axios.get('/api/my-recipes')
    recipes.value = response.data.data.data
    console.log('My Recipes:', recipes.value)
  } catch (error) {
    console.error('Error fetching recipes:', error)
  }
}

onMounted(() => {
  fetchRecipes()
})
</script>

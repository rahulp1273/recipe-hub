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

      <!-- Search & Filters -->
      <div
        class="relative z-10 bg-white rounded-2xl shadow-xl p-4 mb-8 flex flex-wrap gap-3 sm:gap-4 items-center justify-between"
        style="backdrop-filter: blur(2px);"
      >
        <input
          v-model="searchQuery"
          @input="onFilterChange"
          type="text"
          placeholder="Search recipes, ingredients, or users..."
          class="flex-1 min-w-[180px] max-w-xs px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all shadow-sm"
        />
        <!-- All Types Dropdown -->
        <Listbox v-model="selectedType" @update:modelValue="onFilterChange">
          <div class="relative w-44">
            <ListboxButton class="relative w-full cursor-pointer rounded-xl bg-white border border-gray-200 py-2 pl-4 pr-10 text-left shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
              <span class="block truncate">{{ selectedType.label }}</span>
              <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
              </span>
            </ListboxButton>
            <ListboxOptions class="absolute left-0 w-full z-[9999] mt-1 overflow-auto rounded-xl bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm max-h-60">
              <ListboxOption v-for="option in typeOptions" :key="option.value" :value="option" class="cursor-pointer select-none relative py-2 pl-4 pr-10 hover:bg-orange-50">
                <span :class="[option.value === selectedType.value ? 'font-semibold text-orange-600' : 'font-normal', 'block truncate']">{{ option.label }}</span>
                <span v-if="option.value === selectedType.value" class="absolute inset-y-0 right-0 flex items-center pr-3 text-orange-600">✔</span>
              </ListboxOption>
            </ListboxOptions>
          </div>
        </Listbox>
        <!-- All Categories Dropdown -->
        <Listbox v-model="selectedCategory" @update:modelValue="onFilterChange">
          <div class="relative w-44">
            <ListboxButton class="relative w-full cursor-pointer rounded-xl bg-white border border-gray-200 py-2 pl-4 pr-10 text-left shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
              <span class="block truncate">{{ selectedCategory.label }}</span>
              <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
              </span>
            </ListboxButton>
            <ListboxOptions class="absolute left-0 w-full z-[9999] mt-1 overflow-auto rounded-xl bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm max-h-60">
              <ListboxOption v-for="option in categoryOptions" :key="option.value" :value="option" class="cursor-pointer select-none relative py-2 pl-4 pr-10 hover:bg-orange-50">
                <span :class="[option.value === selectedCategory.value ? 'font-semibold text-orange-600' : 'font-normal', 'block truncate']">{{ option.label }}</span>
                <span v-if="option.value === selectedCategory.value" class="absolute inset-y-0 right-0 flex items-center pr-3 text-orange-600">✔</span>
              </ListboxOption>
            </ListboxOptions>
          </div>
        </Listbox>
        <input
          v-model.number="maxPrepTime"
          @input="onFilterChange"
          type="number"
          min="0"
          placeholder="Max Prep Time (min)"
          class="w-40 max-w-xs px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 shadow-sm"
        />
        <button
          @click="clearFilters"
          class="text-sm text-orange-600 hover:text-white hover:bg-orange-500 border border-orange-200 px-4 py-2 rounded-xl font-medium transition-all duration-200 shadow-sm whitespace-nowrap"
        >
          Clear Filters
        </button>
      </div>

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
import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue'

const router = useRouter()

// State
const loading = ref(true)
const loadingMore = ref(false)
const recipes = ref([])
const currentPage = ref(1)
const hasMorePages = ref(false)

// Search & Filter State
const searchQuery = ref('')
const typeOptions = [
  { label: 'All Types', value: '' },
  { label: 'Vegetarian', value: 'vegetarian' },
  { label: 'Non-Vegetarian', value: 'non-vegetarian' }
]
const selectedType = ref(typeOptions[0])
const categoryOptions = [
  { label: 'All Categories', value: '' },
  { label: 'Breakfast', value: 'breakfast' },
  { label: 'Lunch', value: 'lunch' },
  { label: 'Dinner', value: 'dinner' },
  { label: 'Snack', value: 'snack' },
  { label: 'Dessert', value: 'dessert' }
]
const selectedCategory = ref(categoryOptions[0])
const maxPrepTime = ref('')

const onFilterChange = () => {
  fetchFeed(1)
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedType.value = typeOptions[0]
  selectedCategory.value = categoryOptions[0]
  maxPrepTime.value = ''
  fetchFeed(1)
}

// Methods
const fetchFeed = async (page = 1) => {
  try {
    if (page === 1) {
      loading.value = true
    } else {
      loadingMore.value = true
    }

    const token = localStorage.getItem('auth_token')
    const headers = token ? { 'Authorization': `Bearer ${token}` } : {}

    // Build query params
    const params = new URLSearchParams()
    params.append('page', page)
    if (searchQuery.value) params.append('search', searchQuery.value)
    if (selectedCategory.value.value) params.append('category', selectedCategory.value.value)
    if (selectedType.value.value) params.append('type', selectedType.value.value)
    if (maxPrepTime.value) params.append('max_prep_time', maxPrepTime.value)

    const response = await axios.get(`/api/feed?${params.toString()}`, { headers })
    const data = response.data.data

    if (page === 1) {
      recipes.value = data.data
    } else {
      recipes.value.push(...data.data)
    }

    currentPage.value = data.current_page
    hasMorePages.value = data.current_page < data.last_page

  } catch (error) {
    console.error('Error fetching feed:', error)
    if (error.response?.status === 401) {
      // Not logged in - still show public feed
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

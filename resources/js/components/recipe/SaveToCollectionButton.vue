<template>
  <div class="relative">
    <!-- Save Button -->
    <button
      @click="toggleDropdown"
      class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-gray-50 text-gray-600 hover:text-gray-800"
    >
      <span class="text-lg">📁</span>
      <span class="text-sm font-medium">Save</span>
    </button>

    <!-- Dropdown Menu -->
    <div v-if="showDropdown" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-200 z-[9999] max-h-[80vh] overflow-hidden">
      <div class="p-6">
        <div class="flex items-center justify-between mb-4">
          <h4 class="text-lg font-bold text-gray-800">Save to Collection</h4>
          <button @click="showDropdown = false" class="text-gray-400 hover:text-gray-600 text-xl">
            ✕
          </button>
        </div>
        
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500 mx-auto mb-3"></div>
          <p class="text-gray-500">Loading collections...</p>
        </div>

        <!-- Collections List -->
        <div v-else-if="collections.length > 0" class="space-y-3 max-h-64 overflow-y-auto">
          <button
            v-for="collection in collections"
            :key="collection.id"
            @click="addToCollection(collection.id)"
            class="w-full text-left p-4 rounded-xl hover:bg-gray-50 transition-all duration-200 flex items-center space-x-4 border border-gray-100 hover:border-orange-200"
          >
            <div 
              class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shadow-sm"
              :style="{ backgroundColor: collection.color + '20', color: collection.color }"
            >
              {{ collection.icon }}
            </div>
            <div class="flex-1">
              <div class="font-semibold text-gray-800">{{ collection.name }}</div>
              <div class="text-sm text-gray-500">{{ collection.recipes_count }} recipes</div>
            </div>
            <div v-if="isInCollection(collection.id)" class="text-green-500 text-xl">
              ✓
            </div>
          </button>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8">
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-3xl">📁</span>
          </div>
          <h5 class="font-semibold text-gray-800 mb-2">No collections yet</h5>
          <p class="text-gray-500 mb-4">Create your first collection to organize recipes</p>
          <button
            @click="createNewCollection"
            class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 shadow-lg"
          >
            Create Collection
          </button>
        </div>

        <!-- Create New Collection -->
        <div class="border-t border-gray-200 pt-4 mt-4">
          <button
            @click="createNewCollection"
            class="w-full text-left p-4 rounded-xl hover:bg-orange-50 transition-all duration-200 flex items-center space-x-4 text-orange-600 border border-orange-100 hover:border-orange-200"
          >
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
              <span class="text-xl">✨</span>
            </div>
            <span class="font-semibold">Create New Collection</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Backdrop -->
    <div v-if="showDropdown" @click="showDropdown = false" class="fixed inset-0 z-[9998] bg-black bg-opacity-50 backdrop-blur-sm"></div>

    <!-- Login Prompt Modal -->
    <div v-if="showLoginPrompt" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 text-center">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="text-2xl">📁</span>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Login to Save</h3>
        <p class="text-gray-600 mb-6">Please login to save recipes to your collections!</p>
        <div class="flex gap-3">
          <button
            @click="closeLoginPrompt"
            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors"
          >
            Cancel
          </button>
          <button
            @click="goToLogin"
            class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-4 py-2 rounded-lg font-medium transition-all"
          >
            Login Now
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const props = defineProps({
  recipeId: {
    type: [Number, String],
    required: true
  }
})

const emit = defineEmits(['collection-created'])

// Router
const router = useRouter()

const showDropdown = ref(false)
const loading = ref(false)
const collections = ref([])
const recipeCollections = ref([])
const showLoginPrompt = ref(false)

// Check if user is logged in
const isLoggedIn = () => {
  return !!localStorage.getItem('auth_token')
}

const loadCollections = async () => {
  try {
    loading.value = true
    const token = localStorage.getItem('auth_token')
    const response = await axios.get('/api/collections', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    collections.value = response.data.data
  } catch (error) {
    console.error('Error loading collections:', error)
  } finally {
    loading.value = false
  }
}

const loadRecipeCollections = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.get(`/api/recipes/${props.recipeId}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    // This will be updated when we add collections to recipe response
    recipeCollections.value = response.data.data.collections || []
  } catch (error) {
    console.error('Error loading recipe collections:', error)
  }
}

const isInCollection = (collectionId) => {
  return recipeCollections.value.some(collection => collection.id === collectionId)
}

const addToCollection = async (collectionId) => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.post(`/api/collections/${collectionId}/recipes`, {
      recipe_id: props.recipeId
    }, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    if (response.data.success) {
      // Update local state
      const collection = collections.value.find(c => c.id === collectionId)
      if (collection) {
        collection.recipes_count++
      }
      
      // Close dropdown
      showDropdown.value = false
      
      // Show success message
      console.log('Recipe added to collection successfully!')
    }
  } catch (error) {
    console.error('Error adding recipe to collection:', error)
    console.error('Failed to add recipe to collection:', error.response?.data?.message || error.message)
  }
}

const toggleDropdown = () => {
  // Check if user is logged in first
  if (!isLoggedIn()) {
    showLoginPrompt.value = true
    return
  }

  console.log('Toggle dropdown clicked, current state:', showDropdown.value)
  showDropdown.value = !showDropdown.value
  console.log('New state:', showDropdown.value)
  
  if (showDropdown.value) {
    loadCollections()
  }
}

const createNewCollection = () => {
  showDropdown.value = false
  emit('collection-created')
}

// Close login prompt
const closeLoginPrompt = () => {
  showLoginPrompt.value = false
}

// Go to login page
const goToLogin = () => {
  showLoginPrompt.value = false
  router.push('/login')
}

// Watch for recipe ID changes
watch(() => props.recipeId, () => {
  if (props.recipeId) {
    loadRecipeCollections()
  }
})

onMounted(() => {
  if (isLoggedIn()) {
    loadCollections()
    if (props.recipeId) {
      loadRecipeCollections()
    }
  }
})
</script> 
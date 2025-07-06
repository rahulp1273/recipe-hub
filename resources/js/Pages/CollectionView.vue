<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-500 shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6 flex-wrap gap-4">
          <!-- Title with Icon -->
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-green-500 text-2xl">{{ collection?.icon || '\ud83d\udcc1' }}</span>
            </div>
            <div>
              <h1 class="text-3xl font-bold text-white">{{ collection?.name || 'Collection' }}</h1>
              <p class="text-green-100 text-sm">{{ collection?.recipes_count || 0 }} recipes</p>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-4 sm:mt-0">
            <router-link
              to="/collections"
              class="bg-white bg-opacity-20 hover:bg-white hover:bg-opacity-30 text-white px-4 py-2 rounded-xl font-medium transition-all duration-200 border border-white border-opacity-30"
            >
              ← Back to Collections
            </router-link>
            <button
              v-if="collection"
              @click="editCollection"
              class="bg-white hover:bg-gray-100 text-green-500 px-4 py-2 rounded-xl font-medium transition-all duration-200 shadow-lg"
            >
              ✏️ Edit Collection
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-16">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
          <span class="text-2xl">📁</span>
        </div>
        <p class="text-xl text-gray-600">Loading collection...</p>
      </div>

      <!-- Collection Info -->
      <div v-else-if="collection" class="mb-8">
        <!-- Description -->
        <div v-if="collection.description" class="bg-white rounded-2xl shadow-lg p-6 mb-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-2">About this Collection</h3>
          <p class="text-gray-600">{{ collection.description }}</p>
        </div>

        <!-- Privacy Status -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-800">Collection Status</h3>
              <p class="text-gray-600">Manage your collection settings</p>
            </div>
            <div class="flex items-center space-x-3">
              <span 
                v-if="collection.is_public" 
                class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium"
              >
                🌍 Public Collection
              </span>
              <span 
                v-else 
                class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm font-medium"
              >
                🔒 Private Collection
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recipes Grid -->
      <div v-if="collection && collection.recipes && collection.recipes.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <RecipeCard
          v-for="recipe in collection.recipes"
          :key="recipe.id"
          :recipe="recipe"
          @like-toggled="handleLikeToggled"
          @view-recorded="handleViewRecorded"
        />
      </div>

      <!-- Empty State -->
      <div v-else-if="collection && (!collection.recipes || collection.recipes.length === 0)" class="text-center py-16">
        <div class="w-32 h-32 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-8">
          <span class="text-6xl">🍽️</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-4">No Recipes Yet</h3>
        <p class="text-xl text-gray-600 mb-8">
          This collection is empty. Start adding your favorite recipes!
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link
            to="/feed"
            class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-8 py-3 rounded-xl font-bold transition-all duration-200 shadow-lg"
          >
            🍽️ Browse Recipes
          </router-link>
          <router-link
            to="/recipes/create"
            class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-3 rounded-xl font-bold transition-all duration-200 shadow-lg"
          >
            ✨ Create Recipe
          </router-link>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-16">
        <div class="w-32 h-32 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-8">
          <span class="text-6xl">❌</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-4">Collection Not Found</h3>
        <p class="text-xl text-gray-600 mb-8">
          The collection you're looking for doesn't exist or you don't have access to it.
        </p>
        <router-link
          to="/collections"
          class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-8 py-3 rounded-xl font-bold transition-all duration-200 shadow-lg"
        >
          ← Back to Collections
        </router-link>
      </div>
    </div>

    <!-- Edit Collection Modal -->
    <CollectionModal
      v-if="showEditModal"
      :show="showEditModal"
      :collection="collection"
      @close="showEditModal = false"
      @saved="handleCollectionSaved"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import RecipeCard from '../components/recipe/RecipeCard.vue'
import CollectionModal from '../components/modals/CollectionModal.vue'

const route = useRoute()
const router = useRouter()

const collection = ref(null)
const loading = ref(true)
const error = ref(false)
const showEditModal = ref(false)

const loadCollection = async () => {
  try {
    loading.value = true
    error.value = false
    
    const token = localStorage.getItem('auth_token')
    const response = await axios.get(`/api/collections/${route.params.id}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    collection.value = response.data.data
  } catch (err) {
    console.error('Error loading collection:', err)
    error.value = true
    
    if (err.response?.status === 404) {
      error.value = true
    } else if (err.response?.status === 401) {
      router.push('/login')
    }
  } finally {
    loading.value = false
  }
}

const editCollection = () => {
  showEditModal.value = true
}

const handleCollectionSaved = (updatedCollection) => {
  collection.value = updatedCollection
  showEditModal.value = false
}

const handleLikeToggled = (recipeId, isLiked, likesCount) => {
  // Update the recipe in the collection
  if (collection.value && collection.value.recipes) {
    const recipe = collection.value.recipes.find(r => r.id === recipeId)
    if (recipe) {
      recipe.is_liked = isLiked
      recipe.likes_count = likesCount
    }
  }
}

const handleViewRecorded = (recipeId, viewsCount) => {
  // Update the recipe in the collection
  if (collection.value && collection.value.recipes) {
    const recipe = collection.value.recipes.find(r => r.id === recipeId)
    if (recipe) {
      recipe.views_count = viewsCount
    }
  }
}

onMounted(() => {
  loadCollection()
})
</script> 
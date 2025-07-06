<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6 flex-wrap gap-4">
          <!-- Title with Icon -->
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-orange-500 text-2xl">📁</span>
            </div>
            <h1 class="text-3xl font-bold text-white">My Collections</h1>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-4 sm:mt-0">
            <router-link
              to="/dashboard"
              class="bg-white bg-opacity-20 hover:bg-white hover:bg-opacity-30 text-white px-4 py-2 rounded-xl font-medium transition-all duration-200 border border-white border-opacity-30"
            >
              ← Dashboard
            </router-link>
            <button
              @click="showCreateModal = true"
              class="bg-white hover:bg-gray-100 text-orange-500 px-6 py-2 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl"
            >
              ✨ New Collection
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-16">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
          <span class="text-2xl">📁</span>
        </div>
        <p class="text-xl text-gray-600">Loading your collections...</p>
      </div>

      <!-- Collections Grid -->
      <div v-else-if="collections.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <CollectionCard
          v-for="collection in collections"
          :key="collection.id"
          :collection="collection"
          :show-edit-button="true"
          @edit-collection="editCollection"
        />
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16">
        <div class="w-32 h-32 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-8">
          <span class="text-6xl">📁</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-4">No Collections Yet</h3>
        <p class="text-xl text-gray-600 mb-8">
          Create your first collection to organize your favorite recipes!
        </p>
        <button
          @click="showCreateModal = true"
          class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-3 rounded-xl font-bold transition-all duration-200 shadow-lg hover:shadow-xl"
        >
          ✨ Create Your First Collection
        </button>
      </div>

      <!-- Stats -->
      <div v-if="collections.length > 0" class="mt-12 bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Collection Stats</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="text-center">
            <div class="text-3xl font-bold text-orange-500">{{ collections.length }}</div>
            <div class="text-gray-600">Total Collections</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-red-500">{{ totalRecipes }}</div>
            <div class="text-gray-600">Total Recipes</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-blue-500">{{ publicCollections }}</div>
            <div class="text-gray-600">Public Collections</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Collection Modal -->
    <CollectionModal
      :show="showCreateModal || showEditModal"
      :collection="editingCollection"
      @close="closeModal"
      @saved="handleCollectionSaved"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import CollectionCard from '../components/recipe/CollectionCard.vue'
import CollectionModal from '../components/modals/CollectionModal.vue'

const collections = ref([])
const loading = ref(true)
const showCreateModal = ref(false)
const showEditModal = ref(false)
const editingCollection = ref(null)

// Computed properties
const totalRecipes = computed(() => {
  return collections.value.reduce((total, collection) => total + collection.recipes_count, 0)
})

const publicCollections = computed(() => {
  return collections.value.filter(collection => collection.is_public).length
})

// Methods
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
    if (error.response?.status === 401) {
      // Redirect to login if unauthorized
      window.location.href = '/login'
    }
  } finally {
    loading.value = false
  }
}

const editCollection = (collection) => {
  editingCollection.value = collection
  showEditModal.value = true
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingCollection.value = null
}

const handleCollectionSaved = (collection) => {
  // Refresh collections list
  loadCollections()
}

// Lifecycle
onMounted(() => {
  loadCollections()
})
</script> 
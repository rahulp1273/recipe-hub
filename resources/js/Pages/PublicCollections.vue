<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-500 shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <!-- Title with Icon -->
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-green-500 text-2xl">🌍</span>
            </div>
            <h1 class="text-3xl font-bold text-white">Public Collections</h1>
          </div>

          <!-- Back Button -->
          <router-link
            to="/dashboard"
            class="bg-white bg-opacity-20 hover:bg-white hover:bg-opacity-30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 border border-white border-opacity-30 flex items-center space-x-2"
          >
            <span>←</span>
            <span>Back to Dashboard</span>
          </router-link>
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
        <p class="text-xl text-gray-600">Loading public collections...</p>
      </div>

      <!-- Collections Grid -->
      <div v-else-if="collections.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <CollectionCard
          v-for="collection in collections"
          :key="collection.id"
          :collection="collection"
          :show-edit-button="false"
        />
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16">
        <div class="w-32 h-32 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-8">
          <span class="text-6xl">📁</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-4">No Public Collections Yet</h3>
        <p class="text-xl text-gray-600 mb-8">
          Be the first to share your recipe collections with the community!
        </p>
        <router-link
          to="/collections"
          class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-8 py-3 rounded-xl font-bold transition-all duration-200"
        >
          Create Your First Collection
        </router-link>
      </div>

      <!-- Pagination -->
      <div v-if="collections.length > 0" class="mt-12 flex justify-center">
        <div class="flex space-x-2">
          <button
            v-if="currentPage > 1"
            @click="loadCollections(currentPage - 1)"
            class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
          >
            Previous
          </button>
          <span class="px-4 py-2 bg-white border border-gray-300 rounded-lg">
            Page {{ currentPage }}
          </span>
          <button
            v-if="hasMorePages"
            @click="loadCollections(currentPage + 1)"
            class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import CollectionCard from '../components/recipe/CollectionCard.vue'

const collections = ref([])
const loading = ref(true)
const currentPage = ref(1)
const hasMorePages = ref(false)

const loadCollections = async (page = 1) => {
  try {
    loading.value = true
    const response = await axios.get(`/api/public/collections?page=${page}`)
    collections.value = response.data.data.data || response.data.data
    currentPage.value = page
    hasMorePages.value = response.data.data.next_page_url ? true : false
  } catch (error) {
    console.error('Error loading public collections:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCollections()
})
</script> 
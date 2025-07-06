<template>
  <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
    
    <!-- Collection Header -->
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3">
          <div 
            class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shadow-md"
            :style="{ backgroundColor: collection.color + '20', color: collection.color }"
          >
            {{ collection.icon }}
          </div>
          <div>
            <h3 class="text-xl font-bold text-gray-900">{{ collection.name }}</h3>
            <div class="flex items-center space-x-2">
              <p class="text-sm text-gray-500">{{ collection.recipes_count }} recipes</p>
              <span v-if="collection.user" class="text-sm text-gray-400">•</span>
              <p v-if="collection.user" class="text-sm text-gray-500">by {{ collection.user.name }}</p>
            </div>
          </div>
        </div>
        
        <!-- Privacy Badge -->
        <div class="flex items-center space-x-2">
          <span 
            v-if="collection.is_public" 
            class="bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs font-medium"
          >
            🌍 Public
          </span>
          <span 
            v-else 
            class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-medium"
          >
            🔒 Private
          </span>
        </div>
      </div>

      <!-- Description -->
      <p v-if="collection.description" class="text-gray-600 mb-4 line-clamp-2">
        {{ collection.description }}
      </p>

      <!-- Recipe Preview -->
      <div v-if="collection.recipes && collection.recipes.length > 0" class="mb-4">
        <div class="flex space-x-2">
          <div 
            v-for="(recipe, index) in collection.recipes.slice(0, 3)" 
            :key="recipe.id"
            class="w-16 h-16 bg-gradient-to-br from-orange-200 to-red-200 rounded-lg flex items-center justify-center text-2xl overflow-hidden"
          >
            <template v-if="recipe.image">
              <img :src="`/storage/${recipe.image}`" alt="Recipe" class="w-full h-full object-cover" />
            </template>
            <template v-else>
              🍽️
            </template>
          </div>
          <div 
            v-if="collection.recipes_count > 3"
            class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 font-bold"
          >
            +{{ collection.recipes_count - 3 }}
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="mb-4 p-4 bg-gray-50 rounded-xl text-center">
        <span class="text-2xl mb-2 block">📁</span>
        <p class="text-sm text-gray-500">No recipes yet</p>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="px-6 pb-6">
      <div class="flex space-x-3">
        <button
          @click="viewCollection"
          class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-xl font-medium transition-all duration-200 text-center"
        >
          👁️ View Collection
        </button>
        
        <button
          v-if="showEditButton"
          @click="editCollection"
          class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-medium transition-all duration-200"
        >
          ✏️ Edit
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const props = defineProps({
  collection: {
    type: Object,
    required: true
  },
  showEditButton: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['edit-collection'])

const viewCollection = () => {
  router.push(`/collections/${props.collection.id}`)
}

const editCollection = () => {
  emit('edit-collection', props.collection)
}
</script>

<style scoped>
@supports (-webkit-line-clamp: 2) {
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
}
</style> 
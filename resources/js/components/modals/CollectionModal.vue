<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
      
      <!-- Header -->
      <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h3 class="text-xl font-bold text-gray-900">
            {{ isEditing ? 'Edit Collection' : 'Create New Collection' }}
          </h3>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <span class="text-2xl">×</span>
          </button>
        </div>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="p-6 space-y-6">
        
        <!-- Collection Name -->
        <div class="space-y-2">
          <label for="name" class="block text-sm font-medium text-gray-700">Collection Name</label>
          <input
            type="text"
            id="name"
            v-model="form.name"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
            placeholder="e.g., My Favorites, Weekend Meals"
          />
        </div>

        <!-- Description -->
        <div class="space-y-2">
          <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
          <textarea
            id="description"
            v-model="form.description"
            rows="3"
            class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
            placeholder="Describe what this collection is about..."
          ></textarea>
        </div>

        <!-- Icon Selection -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Icon</label>
          <div class="grid grid-cols-8 gap-2">
            <button
              v-for="icon in availableIcons"
              :key="icon"
              type="button"
              @click="form.icon = icon"
              class="w-10 h-10 rounded-lg border-2 transition-all duration-200 flex items-center justify-center text-lg"
              :class="form.icon === icon ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-gray-300'"
            >
              {{ icon }}
            </button>
          </div>
        </div>

        <!-- Color Selection -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Color</label>
          <div class="grid grid-cols-6 gap-2">
            <button
              v-for="color in availableColors"
              :key="color"
              type="button"
              @click="form.color = color"
              class="w-10 h-10 rounded-lg border-2 transition-all duration-200"
              :class="form.color === color ? 'border-gray-800' : 'border-gray-200 hover:border-gray-300'"
              :style="{ backgroundColor: color }"
            ></button>
          </div>
        </div>

        <!-- Privacy Setting -->
        <div class="space-y-2">
          <label class="flex items-center space-x-3">
            <input
              type="checkbox"
              v-model="form.is_public"
              class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
            />
            <span class="text-sm font-medium text-gray-700">Make this collection public</span>
          </label>
          <p class="text-xs text-gray-500">
            Public collections can be viewed by other users
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-3 pt-4">
          <button
            type="button"
            @click="closeModal"
            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-3 rounded-xl font-medium transition-all duration-200"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-4 py-3 rounded-xl font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isSubmitting ? 'Saving...' : (isEditing ? 'Update Collection' : 'Create Collection') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  collection: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'saved'])

const form = ref({
  name: '',
  description: '',
  icon: '📁',
  color: '#FF6B35',
  is_public: false
})

const isSubmitting = ref(false)

const isEditing = computed(() => !!props.collection)

const availableIcons = [
  '📁', '❤️', '⭐', '🔥', '🍳', '🥗', '🍰', '🌮',
  '🍕', '🍜', '🍣', '🥘', '🍖', '🥩', '🍗', '🥬'
]

const availableColors = [
  '#FF6B35', '#FF8E53', '#FFB347', '#FFD700', '#32CD32', '#00CED1',
  '#4169E1', '#9370DB', '#FF69B4', '#FF1493', '#DC143C', '#8B0000'
]

const resetForm = () => {
  form.value = {
    name: '',
    description: '',
    icon: '📁',
    color: '#FF6B35',
    is_public: false
  }
}

const closeModal = () => {
  resetForm()
  emit('close')
}

const submitForm = async () => {
  isSubmitting.value = true
  
  try {
    const token = localStorage.getItem('auth_token')
    const headers = { 'Authorization': `Bearer ${token}` }
    
    let response
    
    if (isEditing.value) {
      response = await axios.put(`/api/collections/${props.collection.id}`, form.value, { headers })
    } else {
      response = await axios.post('/api/collections', form.value, { headers })
    }
    
    emit('saved', response.data.data)
    closeModal()
    
  } catch (error) {
    console.error('Error saving collection:', error)
    alert(error.response?.data?.message || 'Failed to save collection')
  } finally {
    isSubmitting.value = false
  }
}

// Watch for collection prop changes to populate form for editing
watch(() => props.collection, (newCollection) => {
  if (newCollection) {
    form.value = {
      name: newCollection.name,
      description: newCollection.description || '',
      icon: newCollection.icon || '📁',
      color: newCollection.color || '#FF6B35',
      is_public: newCollection.is_public || false
    }
  } else {
    resetForm()
  }
}, { immediate: true })
</script> 
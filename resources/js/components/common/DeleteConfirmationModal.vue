<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60] p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all duration-300">
      <div class="text-center">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <span class="text-4xl">⚠️</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ title || 'Are you sure?' }}</h3>
        <p class="text-gray-600 mb-8">{{ message || 'This action cannot be undone. This will permanently delete the item.' }}</p>
        
        <div class="flex space-x-4">
          <button 
            @click="$emit('close')" 
            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-xl font-semibold transition-colors"
          >
            Cancel
          </button>
          <button 
            @click="$emit('confirm')" 
            :disabled="loading"
            class="flex-1 bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50"
          >
            {{ loading ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: Boolean,
  title: String,
  message: String,
  loading: Boolean
});

defineEmits(['close', 'confirm']);
</script>

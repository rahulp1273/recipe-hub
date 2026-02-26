<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300">
      <div class="p-6">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-3">
            <span class="text-2xl">✏️</span>
            <h2 class="text-2xl font-bold text-gray-900">Edit Product</h2>
          </div>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 text-2xl transition-colors">&times;</button>
        </div>

        <div v-if="product" class="mb-6 bg-orange-50 p-4 rounded-xl border border-orange-100">
          <p class="text-orange-800 font-medium">Editing: {{ product.recipe.title }}</p>
        </div>

        <form @submit.prevent="handleUpdate" class="space-y-6">
          <!-- Price -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Price ($) *</label>
            <input 
              v-model="form.price" 
              type="number" 
              step="0.01" 
              min="0.01" 
              required 
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" 
              placeholder="0.00" 
            />
          </div>

          <!-- Quantity -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Available *</label>
            <input 
              v-model="form.quantity_available" 
              type="number" 
              min="0" 
              required 
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" 
              placeholder="0" 
            />
          </div>

          <!-- Preparation Time -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Preparation Time</label>
            <input 
              v-model="form.preparation_time" 
              type="text" 
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" 
              placeholder="e.g., 30 minutes, 1 hour" 
            />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Product Description</label>
            <textarea 
              v-model="form.description" 
              rows="3" 
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" 
              placeholder="Describe this product..."
            ></textarea>
          </div>

          <!-- Submit Buttons -->
          <div class="flex space-x-4 pt-4">
            <button 
              type="button" 
              @click="$emit('close')" 
              class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-xl font-semibold transition-colors"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              :disabled="loading"
              class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50"
            >
              {{ loading ? 'Saving...' : 'Update Product' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  show: Boolean,
  product: Object
});

const emit = defineEmits(['close', 'updated']);

const form = ref({
  price: '',
  quantity_available: '',
  preparation_time: '',
  description: ''
});

const loading = ref(false);

watch(() => props.show, (newVal) => {
  if (newVal && props.product) {
    form.value = {
      price: props.product.price,
      quantity_available: props.product.quantity_available,
      preparation_time: props.product.preparation_time || '',
      description: props.product.description || ''
    };
  }
});

const handleUpdate = async () => {
  loading.value = true;
  try {
    const response = await axios.put(`/api/store-products/${props.product.id}`, form.value);

    if (response.data.success) {
      alert("Product updated successfully!");
      emit('updated');
      emit('close');
    }
  } catch (error) {
    console.error("Error updating product:", error);
    alert(error.response?.data?.message || "Failed to update product");
  } finally {
    loading.value = false;
  }
};
</script>

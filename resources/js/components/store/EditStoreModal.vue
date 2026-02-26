<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300">
      <div class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold text-gray-900">Edit Store Details</h2>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 text-2xl transition-colors">&times;</button>
        </div>

        <form @submit.prevent="handleUpdate" class="space-y-6">
          <!-- Store Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Store Name *</label>
            <input 
              v-model="form.name" 
              type="text" 
              required 
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" 
              placeholder="Enter your store name" 
            />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea 
              v-model="form.description" 
              rows="3" 
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" 
              placeholder="Describe your store..."
            ></textarea>
          </div>

          <!-- Address -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
            <input 
              v-model="form.address" 
              type="text" 
              required 
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" 
              placeholder="Enter your store address" 
            />
          </div>

          <!-- Location -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Latitude *</label>
              <input 
                v-model="form.latitude" 
                type="number" 
                step="any" 
                required 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" 
                placeholder="e.g., 40.7128" 
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Longitude *</label>
              <input 
                v-model="form.longitude" 
                type="number" 
                step="any" 
                required 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" 
                placeholder="e.g., -74.0060" 
              />
            </div>
          </div>

          <!-- Get Current Location Button -->
          <div>
            <button 
              type="button" 
              @click="getCurrentLocation" 
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center space-x-2"
            >
              <span>📍</span>
              <span>Update to Current Location</span>
            </button>
          </div>

          <!-- Contact Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
              <input 
                v-model="form.phone" 
                type="tel" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" 
                placeholder="Your phone number" 
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <input 
                v-model="form.email" 
                type="email" 
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all" 
                placeholder="Your email" 
              />
            </div>
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
              class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50"
            >
              {{ loading ? 'Saving...' : 'Save Changes' }}
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
  store: Object
});

const emit = defineEmits(['close', 'updated']);

const form = ref({
  name: '',
  description: '',
  address: '',
  latitude: null,
  longitude: null,
  phone: '',
  email: '',
  image_url: ''
});

const loading = ref(false);

// Initialize form when store prop changes or modal opens
watch(() => props.show, (newVal) => {
  if (newVal && props.store) {
    form.value = {
      name: props.store.name,
      description: props.store.description || '',
      address: props.store.address,
      latitude: props.store.latitude,
      longitude: props.store.longitude,
      phone: props.store.phone || '',
      email: props.store.email || '',
      image_url: props.store.image_url || ''
    };
  }
});

const getCurrentLocation = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        form.value.latitude = position.coords.latitude;
        form.value.longitude = position.coords.longitude;
      },
      (error) => {
        console.error("Error getting location:", error);
        alert("Unable to get your location. Please enter it manually.");
      }
    );
  } else {
    alert("Geolocation is not supported by this browser.");
  }
};

const handleUpdate = async () => {
  loading.value = true;
  try {
    const response = await axios.put(`/api/stores/${props.store.id}`, form.value);

    if (response.data.success) {
      emit('updated');
      emit('close');
    }
  } catch (error) {
    console.error("Error updating store:", error);
    // Let parent handle errors too if needed, but for now we'll just keep it quiet 
    // or we could emit an error event
  } finally {
    loading.value = false;
  }
};
</script>

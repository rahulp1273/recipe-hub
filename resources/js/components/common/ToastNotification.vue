<template>
  <Transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="show" class="fixed top-5 right-5 z-[100] max-w-sm w-full bg-white shadow-2xl rounded-2xl pointer-events-auto overflow-hidden border border-gray-100">
      <div class="p-4">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <!-- Success Icon -->
            <div v-if="type === 'success'" class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
              <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <!-- Error Icon -->
            <div v-else-if="type === 'error'" class="h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
              <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </div>
          </div>
          <div class="ml-4 w-0 flex-1 pt-0.5">
            <p class="text-sm font-bold text-gray-900">
              {{ type === 'success' ? 'Success!' : 'Error' }}
            </p>
            <p class="mt-1 text-sm text-gray-500">
              {{ message }}
            </p>
          </div>
          <div class="ml-4 flex-shrink-0 flex">
            <button @click="$emit('close')" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
              <span class="sr-only">Close</span>
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>
      <!-- Progress bar -->
      <div class="h-1 bg-gray-100 w-full">
        <div 
          class="h-full transition-all duration-[3000ms] ease-linear"
          :class="type === 'success' ? 'bg-green-500' : 'bg-red-500'"
          :style="{ width: progress + '%' }"
        ></div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
  show: Boolean,
  message: String,
  type: {
    type: String,
    default: 'success'
  },
  duration: {
    type: Number,
    default: 3000
  }
});

const emit = defineEmits(['close']);
const progress = ref(100);

watch(() => props.show, (newVal) => {
  if (newVal) {
    progress.value = 100;
    setTimeout(() => {
      progress.value = 0;
    }, 10);
    
    setTimeout(() => {
      emit('close');
    }, props.duration);
  }
});
</script>

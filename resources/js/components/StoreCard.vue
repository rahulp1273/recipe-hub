<template>
    <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-yellow-200">
        <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
            <span class="text-3xl">🏪</span>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">
            My Store
        </h3>
        <p class="text-gray-600 mb-4 text-sm leading-relaxed">
            Open your store and sell your recipes
        </p>
        <div class="space-y-2">
            <button
                @click="openStoreModal"
                class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-6 py-2 rounded-xl font-semibold transition-all duration-200 inline-block shadow-lg hover:shadow-xl transform hover:-translate-y-1 text-sm"
            >
                Open Your Store
            </button>
            <br>
            <router-link
                to="/stores"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-xl font-semibold transition-all duration-200 inline-block shadow-md hover:shadow-lg text-sm"
            >
                Browse Stores
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const openStoreModal = () => {
    // For now, just redirect to the store page
    router.push('/my-store');
};
</script>

<template>
  <div class="home min-h-screen">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
              <span class="text-white text-xl font-bold">🍳</span>
            </div>
            <router-link to="/" class="text-2xl font-bold text-gray-900 hover:text-orange-500 transition-colors">
              RecipeHub
            </router-link>
          </div>

          <!-- Navigation Links -->
          <div class="hidden md:flex items-center space-x-8">
            <router-link to="/login" class="text-gray-700 hover:text-orange-500 font-medium transition-colors">
              Login
            </router-link>
            <router-link to="/register" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
              Get Started
            </router-link>
          </div>

          <!-- Mobile Menu Button -->
          <div class="md:hidden">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-orange-500">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-200 py-4">
          <div class="flex flex-col space-y-4">
            <router-link to="/" class="text-gray-700 hover:text-orange-500 font-medium">Home</router-link>
            <router-link to="/login" class="text-gray-700 hover:text-orange-500 font-medium">Login</router-link>
            <router-link to="/register" class="bg-orange-500 text-white px-4 py-2 rounded-lg font-medium text-center">Get Started</router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-orange-400 via-red-500 to-pink-500 text-white overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 bg-black opacity-10"></div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
        <div class="text-center">
          <!-- Main Heading -->
          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
            Welcome to
            <span class="block text-yellow-300">RecipeHub</span>
          </h1>

          <!-- Subtitle -->
          <p class="text-xl sm:text-2xl mb-8 opacity-90 max-w-3xl mx-auto leading-relaxed">
            Discover, create, and share amazing recipes with food lovers around the world
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <router-link to="/register"
              class="bg-white text-orange-500 px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transform hover:scale-105 transition-all duration-200 shadow-lg">
              Start Cooking Today
            </router-link>
            <router-link to="/login"
              class="border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-orange-500 transform hover:scale-105 transition-all duration-200">
              Sign In
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
            Why Choose RecipeHub?
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Everything you need to become a better cook and share your culinary journey
          </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 text-center">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <span class="text-3xl">📝</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Create & Share</h3>
            <p class="text-gray-600 leading-relaxed">
              Share your favorite recipes with our community and inspire others to cook amazing dishes
            </p>
          </div>

          <!-- Feature 2 -->
          <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <span class="text-3xl">🔍</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Discover New</h3>
            <p class="text-gray-600 leading-relaxed">
              Find amazing recipes from around the world and expand your culinary horizons
            </p>
          </div>

          <!-- Feature 3 -->
          <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <span class="text-3xl">❤️</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Save Favorites</h3>
            <p class="text-gray-600 leading-relaxed">
              Keep track of your favorite recipes and build your personal cookbook collection
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <div class="flex items-center justify-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
              <span class="text-white text-xl font-bold">🍳</span>
            </div>
            <span class="text-2xl font-bold">RecipeHub</span>
          </div>
          <p class="text-gray-400 mb-4">
            Bringing food lovers together, one recipe at a time
          </p>
          <div class="text-gray-400 text-sm">
            © 2024 RecipeHub. All rights reserved.
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const mobileMenuOpen = ref(false)
</script>

<style scoped>
/* Custom animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.home {
  animation: fadeInUp 0.6s ease-out;
}
</style>

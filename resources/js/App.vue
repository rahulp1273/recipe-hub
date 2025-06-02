<template>
  <div id="app">
    <nav class="bg-orange-500 shadow-lg">
      <div class="max-w-6xl mx-auto px-4">
        <div class="flex justify-between items-center py-4">
          <router-link to="/" class="text-white text-2xl font-bold hover:text-orange-100">
            RecipeHub
          </router-link>
          <div class="flex space-x-6">
            <router-link to="/" class="text-white hover:text-orange-100 px-3 py-2 rounded transition">
              Home
            </router-link>
            <router-link to="/login" class="text-white hover:text-orange-100 px-3 py-2 rounded transition">
              Login
            </router-link>
            <router-link to="/register" class="text-white hover:text-orange-100 px-3 py-2 rounded transition">
              Register
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <main class="min-h-screen bg-gray-50">
      <router-view />
    </main>
  </div>
</template>

<script setup>
// Main App Component
</script>

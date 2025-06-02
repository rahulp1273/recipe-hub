<template>
  <div class="home">
    <div class="hero bg-gradient-to-br from-orange-400 to-orange-600 text-white py-20">
      <div class="container mx-auto text-center px-4">
        <h1 class="text-5xl font-bold mb-4">Welcome to RecipeHub</h1>
        <p class="text-xl mb-8 opacity-90">Discover and share amazing recipes</p>
        <div class="flex gap-4 justify-center">
          <router-link to="/login"
            class="bg-white text-orange-500 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transform hover:-translate-y-1 transition">
            Login
          </router-link>
          <router-link to="/register"
            class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-orange-500 transform hover:-translate-y-1 transition">
            Register
          </router-link>
        </div>
      </div>
    </div>

    <div class="py-16">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Features</h2>
        <div class="grid md:grid-cols-3 gap-8">
          <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4 text-orange-500">Share Recipes</h3>
            <p class="text-gray-600">Share your favorite recipes with the community</p>
          </div>
          <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4 text-orange-500">Discover New</h3>
            <p class="text-gray-600">Find amazing recipes from around the world</p>
          </div>
          <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4 text-orange-500">Save Favorites</h3>
            <p class="text-gray-600">Keep track of your favorite recipes</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Home page component
</script>

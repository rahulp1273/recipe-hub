<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
          <button @click="logout" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
            Logout
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="border-4 border-dashed border-gray-200 rounded-lg p-8">
          <h2 class="text-2xl font-semibold text-gray-900 mb-4">Welcome to RecipeHub!</h2>
          <p class="text-gray-600 mb-6">Start creating and managing your recipes.</p>

          <!-- Quick Actions -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow">
              <h3 class="text-lg font-medium text-gray-900 mb-2">Create Recipe</h3>
              <p class="text-gray-600 mb-4">Add a new recipe to your collection</p>
              <router-link to="/recipes/create" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 inline-block">
                New Recipe
              </router-link>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
              <h3 class="text-lg font-medium text-gray-900 mb-2">My Recipes</h3>
              <p class="text-gray-600 mb-4">View and manage your recipes</p>
              <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                View Recipes
              </button>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
              <h3 class="text-lg font-medium text-gray-900 mb-2">Profile</h3>
              <p class="text-gray-600 mb-4">Update your profile settings</p>
              <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Edit Profile
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const logout = () => {
  localStorage.removeItem('auth_token')
  router.push('/login')
}
</script>

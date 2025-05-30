<template>
    <GuestLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">All Recipes</h1>
                    <a href="/login" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Login to Add Recipe
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="recipe in recipes" :key="recipe.id" class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">{{ recipe.title }}</h3>
                            <p class="text-gray-600 mb-4">{{ recipe.description }}</p>

                            <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                                <span>⏱️ {{ recipe.prep_time + recipe.cook_time }} mins</span>
                                <span>🍽️ {{ recipe.servings }} servings</span>
                                <span class="capitalize">{{ recipe.difficulty }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <a :href="`/recipes/${recipe.id}`" class="text-blue-500 hover:underline">
                                    View Recipe
                                </a>
                                <span class="text-xs text-gray-400">Category: {{ recipe.category }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="recipes.length === 0" class="text-center py-12">
                    <p class="text-gray-500 text-lg">No recipes found.</p>
                    <a href="/login" class="text-blue-500 hover:underline">
                        Login to create your first recipe!
                    </a>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    recipes: Array
});
</script>

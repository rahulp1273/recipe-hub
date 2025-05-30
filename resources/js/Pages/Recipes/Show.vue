<template>
    <GuestLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Recipe Header -->
                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ recipe.title }}</h1>
                            <p class="text-gray-600 text-lg">{{ recipe.description }}</p>
                        </div>

                        <!-- Recipe Meta Info -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ recipe.prep_time }}</div>
                                <div class="text-sm text-gray-600">Prep Time (min)</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ recipe.cook_time }}</div>
                                <div class="text-sm text-gray-600">Cook Time (min)</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ recipe.servings }}</div>
                                <div class="text-sm text-gray-600">Servings</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-orange-600 capitalize">{{ recipe.difficulty }}</div>
                                <div class="text-sm text-gray-600">Difficulty</div>
                            </div>
                        </div>

                        <!-- Category & Vegetarian -->
                        <div class="flex gap-4 mb-6">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium capitalize">
                                {{ recipe.category }}
                            </span>
                            <span v-if="recipe.is_vegetarian" class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                🌱 Vegetarian
                            </span>
                        </div>

                        <!-- Ingredients -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Ingredients</h2>
                            <ul class="space-y-2">
                                <li v-for="(ingredient, index) in recipe.ingredients" :key="index"
                                    class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                    <span>{{ ingredient }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Instructions -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Instructions</h2>
                            <div class="prose max-w-none">
                                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ recipe.instructions }}</p>
                            </div>
                        </div>

                        <!-- Recipe Author -->
                        <div class="border-t pt-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Recipe by</p>
                                    <p class="font-medium text-gray-900">{{ recipe.user?.name || 'Anonymous' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Created</p>
                                    <p class="font-medium text-gray-900">{{ formatDate(recipe.created_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Back Button -->
                        <div class="mt-8">
                            <a href="/recipes" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                                ← Back to All Recipes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    recipe: Object
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add New Recipe
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <!-- Title -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Recipe Title</label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2"
                                    required
                                />
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2"
                                    required
                                ></textarea>
                            </div>

                            <!-- Ingredients -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ingredients</label>
                                <div v-for="(ingredient, index) in form.ingredients" :key="index" class="flex mb-2">
                                    <input
                                        v-model="form.ingredients[index]"
                                        type="text"
                                        class="flex-1 border border-gray-300 rounded-md px-3 py-2 mr-2"
                                        placeholder="e.g., 2 cups flour"
                                    />
                                    <button
                                        type="button"
                                        @click="removeIngredient(index)"
                                        class="bg-red-500 text-white px-3 py-2 rounded"
                                    >
                                        Remove
                                    </button>
                                </div>
                                <button
                                    type="button"
                                    @click="addIngredient"
                                    class="bg-green-500 text-white px-3 py-2 rounded"
                                >
                                    Add Ingredient
                                </button>
                            </div>

                            <!-- Instructions -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                                <textarea
                                    v-model="form.instructions"
                                    rows="6"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2"
                                    required
                                ></textarea>
                            </div>

                            <!-- Time & Servings -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Prep Time (minutes)</label>
                                    <input
                                        v-model="form.prep_time"
                                        type="number"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cook Time (minutes)</label>
                                    <input
                                        v-model="form.cook_time"
                                        type="number"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Servings</label>
                                    <input
                                        v-model="form.servings"
                                        type="number"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2"
                                        required
                                    />
                                </div>
                            </div>

                            <!-- Category & Difficulty -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                    <select
                                        v-model="form.category"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2"
                                        required
                                    >
                                        <option value="">Select Category</option>
                                        <option value="breakfast">Breakfast</option>
                                        <option value="lunch">Lunch</option>
                                        <option value="dinner">Dinner</option>
                                        <option value="snack">Snack</option>
                                        <option value="dessert">Dessert</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                                    <select
                                        v-model="form.difficulty"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2"
                                        required
                                    >
                                        <option value="">Select Difficulty</option>
                                        <option value="easy">Easy</option>
                                        <option value="medium">Medium</option>
                                        <option value="hard">Hard</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Vegetarian -->
                            <div class="mb-6">
                                <label class="flex items-center">
                                    <input
                                        v-model="form.is_vegetarian"
                                        type="checkbox"
                                        class="mr-2"
                                    />
                                    <span class="text-sm font-medium text-gray-700">Vegetarian Recipe</span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <Link :href="route('recipes.index')" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="bg-blue-500 text-white px-4 py-2 rounded"
                                    :disabled="processing"
                                >
                                    {{ processing ? 'Creating...' : 'Create Recipe' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    description: '',
    ingredients: [''],
    instructions: '',
    prep_time: '',
    cook_time: '',
    servings: '',
    category: '',
    difficulty: '',
    is_vegetarian: false
});

const addIngredient = () => {
    form.ingredients.push('');
};

const removeIngredient = (index) => {
    form.ingredients.splice(index, 1);
};

const submit = () => {
    form.post(route('recipes.store'));
};

const { processing } = form;
</script>

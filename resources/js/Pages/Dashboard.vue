<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Navigation Header -->
        <nav class="bg-gradient-to-r from-orange-500 to-red-500 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo & Brand -->
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-md"
                        >
                            <span class="text-orange-500 text-xl font-bold"
                                >🍳</span
                            >
                        </div>
                        <h1 class="text-2xl font-bold text-white">RecipeHub</h1>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-8">
                        <router-link
                            to="/dashboard"
                            class="text-white hover:text-orange-200 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white hover:bg-opacity-10"
                        >
                            Dashboard
                        </router-link>
                        <router-link
                            to="/recipes"
                            class="text-white hover:text-orange-200 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white hover:bg-opacity-10"
                        >
                            My Recipes
                        </router-link>
                        <router-link
                            to="/recipes/create"
                            class="text-white hover:text-orange-200 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white hover:bg-opacity-10"
                        >
                            Create Recipe
                        </router-link>
                    </div>

                    <!-- User Menu - UPDATED DESIGN -->
                    <div class="flex items-center space-x-3">
                        <!-- Profile Link -->
                        <router-link
                            to="/profile"
                            class="flex items-center space-x-2 text-white hover:text-orange-200 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white hover:bg-opacity-10"
                        >
                            <div
                                class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center"
                            >
                                <span class="text-sm">👤</span>
                            </div>
                            <span class="hidden sm:block">Profile</span>
                        </router-link>

                        <!-- Divider -->
                        <div class="w-px h-6 bg-white bg-opacity-30"></div>

                        <!-- Logout Button -->
                        <button
                            @click="logout"
                            class="flex items-center space-x-2 text-white hover:text-red-200 font-medium transition-all duration-200 px-3 py-2 rounded-lg hover:bg-red-500 hover:bg-opacity-20 group"
                        >
                            <div
                                class="w-8 h-8 bg-red-500 bg-opacity-20 group-hover:bg-opacity-30 rounded-full flex items-center justify-center transition-colors"
                            >
                                <span class="text-sm">🚪</span>
                            </div>
                            <span class="hidden sm:block">Logout</span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Welcome Section -->
            <div class="text-center mb-10">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">
                    Welcome back,
                    <span class="text-orange-500">{{
                        userName.split(" ")[0]
                    }}</span
                    >! 👋
                </h2>
                <p class="text-xl text-gray-600">
                    Ready to create something delicious today?
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <!-- Total Recipes -->
                <div
                    class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">
                                Total Recipes
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ myRecipesCount }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center"
                        >
                            <span class="text-2xl">📚</span>
                        </div>
                    </div>
                </div>

                <!-- This Week -->
                <div
                    class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">
                                This Week
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ weeklyRecipes }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center"
                        >
                            <span class="text-2xl">📈</span>
                        </div>
                    </div>
                </div>

                <!-- Favorites -->
                <div
                    class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">
                                Favorites
                            </p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ favoriteCount }}
                            </p>
                        </div>
                        <div
                            class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center"
                        >
                            <span class="text-2xl">❤️</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions - NOW 3 COLUMNS -->
            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto mb-12"
            >
                <!-- My Recipes -->
                <div
                    class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-blue-200"
                >
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg"
                    >
                        <span class="text-3xl">📝</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        My Recipe Collection
                    </h3>
                    <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                        View and manage all your created recipes
                    </p>
                    <router-link
                        to="/recipes"
                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-2 rounded-xl font-semibold transition-all duration-200 inline-block shadow-lg hover:shadow-xl transform hover:-translate-y-1 text-sm"
                    >
                        View My Recipes
                    </router-link>
                </div>

                <!-- Create Recipe -->
                <div
                    class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-orange-200"
                >
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg"
                    >
                        <span class="text-3xl">✨</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        Create New Recipe
                    </h3>
                    <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                        Share your culinary creativity with the world
                    </p>
                    <router-link
                        to="/recipes/create"
                        class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-2 rounded-xl font-semibold transition-all duration-200 inline-block shadow-lg hover:shadow-xl transform hover:-translate-y-1 text-sm"
                    >
                        Create Recipe
                    </router-link>
                </div>

                <!-- NEW: Community Feed -->
                <div
                    class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-purple-200"
                >
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg"
                    >
                        <span class="text-3xl">🌍</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        Community Feed
                    </h3>
                    <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                        Discover amazing recipes from other chefs
                    </p>
                    <router-link
                        to="/feed"
                        class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-6 py-2 rounded-xl font-semibold transition-all duration-200 inline-block shadow-lg hover:shadow-xl transform hover:-translate-y-1 text-sm"
                    >
                        Explore Feed
                    </router-link>
                </div>
            </div>

            <!-- Recent Recipes -->
            <div v-if="recentRecipes.length > 0" class="mb-12">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-3xl font-bold text-gray-800">
                        Recent Recipes
                    </h3>
                </div>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="recipe in recentRecipes"
                        :key="recipe.id"
                        class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-orange-200 transform hover:-translate-y-1"
                    >
                        <div
                            class="h-48 bg-gradient-to-br from-orange-200 via-red-200 to-pink-200 flex items-center justify-center overflow-hidden"
                        >
                            <template v-if="recipe.image">
                                <img :src="`/storage/${recipe.image}`" alt="Recipe Image" class="object-cover w-full h-full" />
                            </template>
                            <template v-else>
                                <span class="text-5xl">🍽️</span>
                            </template>
                        </div>
                        <div class="p-6">
                            <h4 class="text-xl font-bold text-gray-900 mb-2">
                                {{ recipe.title }}
                            </h4>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ recipe.description }}
                            </p>
                            <div
                                class="flex justify-between items-center text-sm text-gray-500"
                            >
                                <div class="flex items-center space-x-1">
                                    <span>⏱️</span>
                                    <span>{{ recipe.prep_time }}min</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <span>👥</span>
                                    <span>{{ recipe.servings }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16">
                <div
                    class="w-32 h-32 bg-gradient-to-br from-orange-100 to-red-100 rounded-full flex items-center justify-center mx-auto mb-8"
                >
                    <span class="text-6xl">🍳</span>
                </div>
                <h4 class="text-3xl font-bold text-gray-800 mb-4">
                    Ready to start cooking?
                </h4>
                <p class="text-xl text-gray-600 mb-10 max-w-md mx-auto">
                    Create your first recipe and start building your personal
                    cookbook!
                </p>
                <router-link
                    to="/recipes/create"
                    class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-10 py-4 rounded-xl font-bold text-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 inline-block"
                >
                    Create Your First Recipe
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

// User data - Remove hardcoded value
const userName = ref(""); // Empty initially
const userEmail = ref("");
const userId = ref(null);

const userInitials = computed(() => {
    if (!userName.value) return "U";
    return userName.value
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase();
});

// Recipes
const myRecipesCount = ref(0);
const weeklyRecipes = ref(0);
const favoriteCount = ref(0);
const recentRecipes = ref([]);

// Fetch user profile data
const fetchUserProfile = async () => {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) {
            router.push("/login");
            return;
        }

        const response = await axios.get("/api/user", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        console.log("User data:", response.data); // Debug

        // Set user data
        userName.value =
            response.data.name || response.data.user?.name || "User";
        userEmail.value =
            response.data.email || response.data.user?.email || "";
        userId.value = response.data.id || response.data.user?.id || null;
    } catch (error) {
        console.error("Error fetching user profile:", error);
        if (error.response?.status === 401) {
            localStorage.removeItem("auth_token");
            router.push("/login");
        }
    }
};

const fetchDashboardData = async () => {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) {
            router.push("/login");
            return;
        }

        const recipesResponse = await axios.get("/api/my-recipes", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        const recipes =
            recipesResponse.data.data?.data || recipesResponse.data.data || [];

        myRecipesCount.value = recipes.length;
        weeklyRecipes.value = Math.floor(recipes.length / 4);
        favoriteCount.value = Math.floor(recipes.length / 2);
        recentRecipes.value = recipes.slice(0, 3);
    } catch (error) {
        console.error("Error fetching dashboard data:", error);
        myRecipesCount.value = 0;
        weeklyRecipes.value = 0;
        favoriteCount.value = 0;
        recentRecipes.value = [];
    }
};

const logout = () => {
    localStorage.removeItem("auth_token");
    router.push("/");
};

onMounted(async () => {
    await fetchUserProfile(); // Fetch user data first
    await fetchDashboardData(); // Then fetch recipes
});
</script>

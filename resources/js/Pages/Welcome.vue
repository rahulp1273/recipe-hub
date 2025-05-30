<template>
    <div class="min-h-screen bg-white">
        <!-- Navigation Header -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <span class="text-2xl font-bold text-orange-600">🍳</span>
                            <span class="ml-2 text-xl font-bold text-gray-900">RecipeHub</span>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="/recipes" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                Browse Recipes
                            </a>
                            <a href="#features" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                Features
                            </a>
                            <a href="#about" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                About
                            </a>
                        </div>
                    </div>

                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-4">
                        <a href="/login" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            Login
                        </a>
                        <a href="/register" class="bg-orange-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-orange-700 transition duration-200">
                            Sign Up
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-orange-50 to-red-50 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                        Discover Amazing
                        <span class="text-orange-600">Recipes</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                        Share your favorite recipes, discover new dishes, and connect with food lovers from around the world.
                        Cooking made simple and delicious.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/recipes" class="bg-orange-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-orange-700 transition duration-200 shadow-lg">
                            Browse Recipes
                        </a>
                        <a href="/register" class="bg-white text-orange-600 px-8 py-3 rounded-lg text-lg font-semibold border-2 border-orange-600 hover:bg-orange-50 transition duration-200">
                            Join Community
                        </a>
                    </div>
                </div>

                <!-- Hero Image/Stats -->
                <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="text-3xl font-bold text-orange-600 mb-2">1000+</div>
                        <div class="text-gray-600">Delicious Recipes</div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="text-3xl font-bold text-orange-600 mb-2">500+</div>
                        <div class="text-gray-600">Happy Cooks</div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="text-3xl font-bold text-orange-600 mb-2">50+</div>
                        <div class="text-gray-600">Cuisines</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Why Choose RecipeHub?
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        Everything you need to discover, create, and share amazing recipes
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="text-center p-6">
                        <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">📝</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Easy Recipe Creation</h3>
                        <p class="text-gray-600">
                            Create and share your recipes with our simple, intuitive recipe builder. Add ingredients, steps, and photos effortlessly.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="text-center p-6">
                        <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">🔍</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Smart Search</h3>
                        <p class="text-gray-600">
                            Find recipes by ingredients, cuisine, difficulty level, or dietary preferences. Discover your next favorite dish.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="text-center p-6">
                        <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">👥</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Community Driven</h3>
                        <p class="text-gray-600">
                            Connect with fellow food enthusiasts, rate recipes, and get inspired by the community's creations.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular Categories -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Popular Categories
                    </h2>
                    <p class="text-xl text-gray-600">
                        Explore recipes by your favorite meal types
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                    <a href="/recipes?category=breakfast" class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition duration-200 text-center group">
                        <div class="text-4xl mb-3">🥞</div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-orange-600">Breakfast</h3>
                    </a>
                    <a href="/recipes?category=lunch" class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition duration-200 text-center group">
                        <div class="text-4xl mb-3">🥗</div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-orange-600">Lunch</h3>
                    </a>
                    <a href="/recipes?category=dinner" class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition duration-200 text-center group">
                        <div class="text-4xl mb-3">🍝</div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-orange-600">Dinner</h3>
                    </a>
                    <a href="/recipes?category=snack" class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition duration-200 text-center group">
                        <div class="text-4xl mb-3">🍿</div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-orange-600">Snacks</h3>
                    </a>
                    <a href="/recipes?category=dessert" class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition duration-200 text-center group">
                        <div class="text-4xl mb-3">🍰</div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-orange-600">Desserts</h3>
                    </a>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-orange-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Ready to Start Cooking?
                </h2>
                <p class="text-xl text-orange-100 mb-8 max-w-2xl mx-auto">
                    Join thousands of home cooks sharing their favorite recipes and discovering new ones every day.
                </p>
                <a href="/register" class="bg-white text-orange-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition duration-200 shadow-lg">
                    Get Started Free
                </a>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Logo & Description -->
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center mb-4">
                            <span class="text-2xl font-bold text-orange-600">🍳</span>
                            <span class="ml-2 text-xl font-bold">RecipeHub</span>
                        </div>
                        <p class="text-gray-400 mb-4">
                            Your ultimate destination for discovering, creating, and sharing delicious recipes with a passionate community of food lovers.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <ul class="space-y-2">
                            <li><a href="/recipes" class="text-gray-400 hover:text-white">Browse Recipes</a></li>
                            <li><a href="/register" class="text-gray-400 hover:text-white">Join Community</a></li>
                            <li><a href="#about" class="text-gray-400 hover:text-white">About Us</a></li>
                            <li><a href="#contact" class="text-gray-400 hover:text-white">Contact</a></li>
                        </ul>
                    </div>

                    <!-- Categories -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Categories</h3>
                        <ul class="space-y-2">
                            <li><a href="/recipes?category=breakfast" class="text-gray-400 hover:text-white">Breakfast</a></li>
                            <li><a href="/recipes?category=lunch" class="text-gray-400 hover:text-white">Lunch</a></li>
                            <li><a href="/recipes?category=dinner" class="text-gray-400 hover:text-white">Dinner</a></li>
                            <li><a href="/recipes?category=dessert" class="text-gray-400 hover:text-white">Desserts</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                    <p class="text-gray-400">
                        © 2024 RecipeHub. Made with ❤️ for food lovers everywhere.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
// No additional logic needed for landing page
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Navigation Header -->
        <nav class="bg-gradient-to-r from-orange-500 to-red-500 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo & Brand -->
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-md">
                            <span class="text-orange-500 text-xl font-bold">🍳</span>
                        </div>
                        <h1 class="text-2xl font-bold text-white">RecipeHub - My Store</h1>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-8">
                        <router-link to="/dashboard" class="text-white hover:text-orange-200 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                            Dashboard
                        </router-link>
                        <router-link to="/stores" class="text-white hover:text-orange-200 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                            Browse Stores
                        </router-link>
                        <button @click="logout" class="text-white hover:text-orange-200 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Store Status -->
            <div v-if="!myStore" class="text-center py-12">
                <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-4xl">🏪</span>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">No Store Found</h2>
                <p class="text-gray-600 mb-8">You haven't created a store yet. Open your store to start selling your recipes!</p>
                <button @click="openStoreModal" class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                    Open Your Store
                </button>
            </div>

            <!-- Store Dashboard -->
            <div v-else>
                <!-- Store Info Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center">
                                <span class="text-3xl">🏪</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">{{ myStore.name }}</h2>
                                <p class="text-gray-600">{{ myStore.address }}</p>
                                <div class="flex items-center space-x-4 mt-2">
                                    <span class="text-sm text-gray-500">📞 {{ myStore.phone || 'No phone' }}</span>
                                    <span class="text-sm text-gray-500">✉️ {{ myStore.email || 'No email' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <button @click="openEditStoreModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Edit Store
                            </button>
                            <button @click="toggleStoreStatus" :class="myStore.is_active ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600'" class="text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                {{ myStore.is_active ? 'Close Store' : 'Open Store' }}
                            </button>
                        </div>
                    </div>
                    <p v-if="myStore.description" class="text-gray-700">{{ myStore.description }}</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Total Products</p>
                                <p class="text-3xl font-bold text-gray-900">{{ storeProducts.length }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <span class="text-2xl">📦</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
                                <p class="text-3xl font-bold text-gray-900">{{ orders.length }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <span class="text-2xl">📋</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
                                <p class="text-3xl font-bold text-gray-900">${{ totalRevenue.toFixed(2) }}</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <span class="text-2xl">💰</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="bg-white rounded-2xl shadow-lg">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8 px-6">
                            <button @click="activeTab = 'products'" :class="activeTab === 'products' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 px-1 border-b-2 font-medium text-sm">
                                My Products
                            </button>
                            <button @click="activeTab = 'orders'" :class="activeTab === 'orders' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="py-4 px-1 border-b-2 font-medium text-sm">
                                Orders
                            </button>
                        </nav>
                    </div>

                    <div class="p-6">
                        <!-- Products Tab -->
                        <div v-if="activeTab === 'products'">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-900">My Products</h3>
                                <button @click="openAddProductModal" class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-4 py-2 rounded-lg font-semibold transition-all duration-200">
                                    Add Product
                                </button>
                            </div>

                            <div v-if="storeProducts.length === 0" class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-2xl">📦</span>
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No Products Yet</h4>
                                <p class="text-gray-600 mb-4">Add your recipes as products to start selling!</p>
                                <button @click="openAddProductModal" class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200">
                                    Add Your First Product
                                </button>
                            </div>

                            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div v-for="product in storeProducts" :key="product.id" class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-semibold text-gray-900">{{ product.recipe.title }}</h4>
                                        <span class="text-lg font-bold text-green-600">${{ product.price }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">{{ product.description || 'No description' }}</p>
                                    <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                                        <span>Stock: {{ product.quantity_available }}</span>
                                        <span v-if="product.preparation_time">{{ product.preparation_time }}</span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button @click="editProduct(product)" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                            Edit
                                        </button>
                                        <button @click="deleteProduct(product.id)" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Tab -->
                        <div v-if="activeTab === 'orders'">
                            <h3 class="text-xl font-bold text-gray-900 mb-6">Recent Orders</h3>
                            
                            <div v-if="orders.length === 0" class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-2xl">📋</span>
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No Orders Yet</h4>
                                <p class="text-gray-600">Orders will appear here when customers place them.</p>
                            </div>

                            <div v-else class="space-y-4">
                                <div v-for="order in orders" :key="order.id" class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ order.store_product.recipe.title }}</h4>
                                            <p class="text-sm text-gray-600">Order #{{ order.id }} - {{ order.customer.name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-gray-900">${{ order.total_price }}</p>
                                            <span :class="getStatusColor(order.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                                                {{ order.status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600 mb-3">
                                        <p>Quantity: {{ order.quantity }}</p>
                                        <p>Delivery: {{ order.delivery_address }}</p>
                                        <p>Payment: {{ order.payment_method.replace('_', ' ').toUpperCase() }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <select v-model="order.status" @change="updateOrderStatus(order)" class="flex-1 px-3 py-1 border border-gray-300 rounded text-sm">
                                            <option value="pending">Pending</option>
                                            <option value="confirmed">Confirmed</option>
                                            <option value="preparing">Preparing</option>
                                            <option value="ready">Ready</option>
                                            <option value="delivered">Delivered</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Store Creation Modal -->
        <div v-if="showStoreModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Open Your Store</h2>
                        <button @click="closeStoreModal" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
                    </div>

                    <form @submit.prevent="createStore" class="space-y-6">
                        <!-- Store Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Store Name *</label>
                            <input v-model="storeForm.name" type="text" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Enter your store name" />
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea v-model="storeForm.description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Describe your store..."></textarea>
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                            <input v-model="storeForm.address" type="text" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Enter your store address" />
                        </div>

                        <!-- Location -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Latitude *</label>
                                <input v-model="storeForm.latitude" type="number" step="any" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="e.g., 40.7128" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Longitude *</label>
                                <input v-model="storeForm.longitude" type="number" step="any" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="e.g., -74.0060" />
                            </div>
                        </div>

                        <!-- Get Current Location Button -->
                        <div>
                            <button type="button" @click="getCurrentLocation" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                📍 Use Current Location
                            </button>
                        </div>

                        <!-- Contact Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                <input v-model="storeForm.phone" type="tel" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Your phone number" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input v-model="storeForm.email" type="email" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Your email" />
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex space-x-4 pt-4">
                            <button type="button" @click="closeStoreModal" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-semibold transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                                Create Store
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Product Modal -->
        <div v-if="showAddProductModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Add Product to Store</h2>
                        <button @click="closeAddProductModal" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
                    </div>

                    <form @submit.prevent="addProduct" class="space-y-6">
                        <!-- Recipe Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Recipe *</label>
                            <select v-model="productForm.recipe_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                <option value="">Choose a recipe...</option>
                                <option v-for="recipe in myRecipes" :key="recipe.id" :value="recipe.id">{{ recipe.title }}</option>
                            </select>
                            <p v-if="myRecipes.length === 0" class="text-sm text-red-600 mt-1">
                                No recipes found. <router-link to="/recipes/create" class="text-blue-600 hover:underline">Create a recipe first</router-link>
                            </p>
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price ($) *</label>
                            <input v-model="productForm.price" type="number" step="0.01" min="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="0.00" />
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Available *</label>
                            <input v-model="productForm.quantity_available" type="number" min="0" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="0" />
                        </div>

                        <!-- Preparation Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preparation Time</label>
                            <input v-model="productForm.preparation_time" type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="e.g., 30 minutes, 1 hour" />
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Description</label>
                            <textarea v-model="productForm.description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Describe this product..."></textarea>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex space-x-4 pt-4">
                            <button type="button" @click="closeAddProductModal" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-semibold transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                                Add Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

// Data
const myStore = ref(null);
const storeProducts = ref([]);
const orders = ref([]);
const myRecipes = ref([]);
const activeTab = ref('products');

// Modals
const showStoreModal = ref(false);
const showAddProductModal = ref(false);

// Forms
const storeForm = ref({
    name: '',
    description: '',
    address: '',
    latitude: null,
    longitude: null,
    phone: '',
    email: '',
    image_url: ''
});

const productForm = ref({
    recipe_id: '',
    price: '',
    quantity_available: '',
    preparation_time: '',
    description: ''
});

// Computed
const totalRevenue = computed(() => {
    return orders.value.reduce((total, order) => {
        return total + parseFloat(order.total_price);
    }, 0);
});

// Methods
const fetchMyStore = async () => {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) return;

        const response = await axios.get("/api/stores/my-store", {
            headers: { Authorization: `Bearer ${token}` }
        });

        if (response.data.success) {
            myStore.value = response.data.data;
            await fetchStoreProducts();
            await fetchStoreOrders();
        }
    } catch (error) {
        console.error("Error fetching store:", error);
    }
};

const fetchStoreProducts = async () => {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) return;

        const response = await axios.get("/api/store-products/my-products", {
            headers: { Authorization: `Bearer ${token}` }
        });

        if (response.data.success) {
            storeProducts.value = response.data.data;
        }
    } catch (error) {
        console.error("Error fetching products:", error);
    }
};

const fetchStoreOrders = async () => {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) return;

        const response = await axios.get("/api/stores/my-store/orders", {
            headers: { Authorization: `Bearer ${token}` }
        });

        if (response.data.success) {
            orders.value = response.data.data;
        }
    } catch (error) {
        console.error("Error fetching orders:", error);
    }
};

const fetchMyRecipes = async () => {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) return;

        const response = await axios.get("/api/my-recipes", {
            headers: { Authorization: `Bearer ${token}` }
        });

        console.log("Recipes API response:", response.data); // Debug log

        if (response.data.success) {
            myRecipes.value = response.data.data.data; // Access the actual recipes array from paginated response
            console.log("Loaded recipes:", myRecipes.value); // Debug log
        }
    } catch (error) {
        console.error("Error fetching recipes:", error);
        alert("Failed to load recipes. Please try again.");
    }
};

const openStoreModal = () => {
    showStoreModal.value = true;
};

const closeStoreModal = () => {
    showStoreModal.value = false;
    storeForm.value = {
        name: '',
        description: '',
        address: '',
        latitude: null,
        longitude: null,
        phone: '',
        email: '',
        image_url: ''
    };
};

const createStore = async () => {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) return;

        const response = await axios.post("/api/stores", storeForm.value, {
            headers: { Authorization: `Bearer ${token}` }
        });

        if (response.data.success) {
            alert("Store created successfully!");
            closeStoreModal();
            await fetchMyStore();
        }
    } catch (error) {
        console.error("Error creating store:", error);
        alert(error.response?.data?.message || "Failed to create store");
    }
};

const openAddProductModal = async () => {
    await fetchMyRecipes();
    showAddProductModal.value = true;
};

const closeAddProductModal = () => {
    showAddProductModal.value = false;
    productForm.value = {
        recipe_id: '',
        price: '',
        quantity_available: '',
        preparation_time: '',
        description: ''
    };
};

const addProduct = async () => {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) return;

        // Validate that a recipe is selected
        if (!productForm.value.recipe_id) {
            alert("Please select a recipe");
            return;
        }

        const response = await axios.post("/api/store-products", productForm.value, {
            headers: { Authorization: `Bearer ${token}` }
        });

        if (response.data.success) {
            alert("Product added successfully!");
            closeAddProductModal();
            await fetchStoreProducts();
        }
    } catch (error) {
        console.error("Error adding product:", error);
        alert(error.response?.data?.message || "Failed to add product");
    }
};

const getCurrentLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                storeForm.value.latitude = position.coords.latitude;
                storeForm.value.longitude = position.coords.longitude;
            },
            (error) => {
                console.error("Error getting location:", error);
                alert("Unable to get your location. Please enter it manually.");
            }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-blue-100 text-blue-800',
        preparing: 'bg-orange-100 text-orange-800',
        ready: 'bg-green-100 text-green-800',
        delivered: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const updateOrderStatus = async (order) => {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) return;

        await axios.put(`/api/orders/${order.id}`, {
            status: order.status
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });

        alert("Order status updated!");
    } catch (error) {
        console.error("Error updating order:", error);
        alert("Failed to update order status");
    }
};

const logout = () => {
    localStorage.removeItem("auth_token");
    router.push("/");
};

onMounted(() => {
    fetchMyStore();
});
</script>

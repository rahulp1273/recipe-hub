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
                        <h3 class="text-small font-semibold text-white">Browse Stores</h3>
                        <router-link to="/dashboard" class="text-white hover:text-orange-200 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                            Dashboard
                        </router-link>
                        <router-link to="/my-store" class="text-white hover:text-orange-200 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                            My Store
                        </router-link>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-8">    
                        <button @click="logout" class="text-white hover:text-orange-200 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white hover:bg-opacity-10">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Discover Local Recipe Stores</h1>
                <p class="text-xl text-gray-600 mb-6">Find amazing homemade recipes from local chefs near you</p>
                
                <!-- Location Input -->
                <div class="max-w-md mx-auto">
                    <div class="flex space-x-3">
                        <input
                            v-model="userLocation.address"
                            type="text"
                            placeholder="Enter your address or city"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        />
                        <button @click="getCurrentLocation" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-xl font-semibold transition-colors">
                            📍 Use Current Location
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">We'll show stores within 10km of your location</p>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-12">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl animate-spin">⏳</span>
                </div>
                <p class="text-gray-600">Finding stores near you...</p>
            </div>

            <!-- No Stores Found -->
            <div v-else-if="stores.length === 0" class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-4xl">🏪</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">No Stores Found</h2>
                <p class="text-gray-600 mb-6">No stores are available within 10km of your location.</p>
                <button @click="getCurrentLocation" class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200">
                    Try Current Location
                </button>
            </div>

            <!-- Stores Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="store in stores" :key="store.id" class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <!-- Store Header -->
                    <div class="p-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center">
                                <span class="text-2xl">🏪</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ store.name }}</h3>
                                <p class="text-sm text-gray-600">by {{ store.user.name }}</p>
                            </div>
                        </div>
                        
                        <p class="text-gray-700 mb-4">{{ store.description || 'No description available' }}</p>
                        
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex items-center space-x-2">
                                <span>📍</span>
                                <span>{{ store.address }}</span>
                            </div>
                            <div v-if="store.phone" class="flex items-center space-x-2">
                                <span>📞</span>
                                <span>{{ store.phone }}</span>
                            </div>
                            <div v-if="store.email" class="flex items-center space-x-2">
                                <span>✉️</span>
                                <span>{{ store.email }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Products Preview -->
                    <div class="px-6 pb-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Available Products ({{ store.available_products?.length || 0 }})</h4>
                        
                        <div v-if="store.available_products?.length === 0" class="text-center py-4">
                            <p class="text-gray-500 text-sm">No products available</p>
                        </div>
                        
                        <div v-else class="space-y-2">
                            <div v-for="product in store.available_products?.slice(0, 3)" :key="product.id" class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                                <div>
                                    <h5 class="font-medium text-gray-900 text-sm">{{ product.recipe.title }}</h5>
                                    <p class="text-xs text-gray-600">{{ product.preparation_time || 'No prep time' }}</p>
                                </div>
                                <span class="font-bold text-green-600">${{ product.price }}</span>
                            </div>
                            
                            <div v-if="store.available_products?.length > 3" class="text-center">
                                <p class="text-sm text-gray-500">+{{ store.available_products.length - 3 }} more products</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="px-6 pb-6">
                        <button @click="viewStore(store)" class="w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-200">
                            View Store & Order
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Store Detail Modal -->
        <div v-if="selectedStore" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center">
                                <span class="text-3xl">🏪</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">{{ selectedStore.name }}</h2>
                                <p class="text-gray-600">by {{ selectedStore.user.name }}</p>
                            </div>
                        </div>
                        <button @click="closeStoreModal" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
                    </div>

                    <!-- Store Info -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <p class="text-gray-700 mb-3">{{ selectedStore.description || 'No description available' }}</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                            <div class="flex items-center space-x-2">
                                <span>📍</span>
                                <span>{{ selectedStore.address }}</span>
                            </div>
                            <div v-if="selectedStore.phone" class="flex items-center space-x-2">
                                <span>📞</span>
                                <span>{{ selectedStore.phone }}</span>
                            </div>
                            <div v-if="selectedStore.email" class="flex items-center space-x-2">
                                <span>✉️</span>
                                <span>{{ selectedStore.email }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Products -->
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Available Products</h3>
                        
                        <div v-if="selectedStore.available_products?.length === 0" class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">📦</span>
                            </div>
                            <p class="text-gray-600">No products available at this store</p>
                        </div>
                        
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="product in selectedStore.available_products" :key="product.id" class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ product.recipe.title }}</h4>
                                        <p class="text-sm text-gray-600">{{ product.description || 'No description' }}</p>
                                    </div>
                                    <span class="text-lg font-bold text-green-600">${{ product.price }}</span>
                                </div>
                                
                                <div class="text-sm text-gray-600 mb-3">
                                    <p>Stock: {{ product.quantity_available }}</p>
                                    <p v-if="product.preparation_time">Prep Time: {{ product.preparation_time }}</p>
                                </div>
                                
                                <button @click="orderProduct(product)" :disabled="product.quantity_available === 0" :class="product.quantity_available === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600'" class="w-full text-white px-4 py-2 rounded-lg font-semibold transition-all duration-200">
                                    {{ product.quantity_available === 0 ? 'Out of Stock' : 'Order Now' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Modal -->
        <div v-if="showOrderModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Place Order</h2>
                        <button @click="closeOrderModal" class="text-gray-400 hover:text-gray-600 text-2xl">×</button>
                    </div>

                    <form @submit.prevent="placeOrder" class="space-y-6">
                        <!-- Product Info -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h3 class="font-semibold text-gray-900">{{ selectedProduct?.recipe.title }}</h3>
                            <p class="text-sm text-gray-600">{{ selectedProduct?.description || 'No description' }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-lg font-bold text-green-600">${{ selectedProduct?.price }}</span>
                                <span class="text-sm text-gray-600">Stock: {{ selectedProduct?.quantity_available }}</span>
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                            <input v-model="orderForm.quantity" type="number" min="1" :max="selectedProduct?.quantity_available" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" />
                        </div>

                        <!-- Delivery Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Address *</label>
                            <textarea v-model="orderForm.delivery_address" rows="3" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Enter your delivery address"></textarea>
                        </div>

                        <!-- Location -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Latitude *</label>
                                <input v-model="orderForm.delivery_latitude" type="number" step="any" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="e.g., 40.7128" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Longitude *</label>
                                <input v-model="orderForm.delivery_longitude" type="number" step="any" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="e.g., -74.0060" />
                            </div>
                        </div>

                        <!-- Get Current Location Button -->
                        <div>
                            <button type="button" @click="getCurrentLocationForOrder" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                📍 Use Current Location
                            </button>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3">
                                    <input v-model="orderForm.payment_method" type="radio" value="cash_on_delivery" class="text-orange-500 focus:ring-orange-500" />
                                    <span>Cash on Delivery</span>
                                </label>
                                <label class="flex items-center space-x-3">
                                    <input v-model="orderForm.payment_method" type="radio" value="credit_card" class="text-orange-500 focus:ring-orange-500" />
                                    <span>Credit Card</span>
                                </label>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Order Notes</label>
                            <textarea v-model="orderForm.notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Any special instructions..."></textarea>
                        </div>

                        <!-- Total -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-semibold text-gray-900">Total:</span>
                                <span class="text-2xl font-bold text-green-600">${{ (selectedProduct?.price * orderForm.quantity).toFixed(2) }}</span>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex space-x-4 pt-4">
                            <button type="button" @click="closeOrderModal" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl font-semibold transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                                Place Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

// Data
const stores = ref([]);
const loading = ref(false);
const selectedStore = ref(null);
const selectedProduct = ref(null);
const showOrderModal = ref(false);

const userLocation = ref({
    latitude: null,
    longitude: null,
    address: ''
});

const orderForm = ref({
    quantity: 1,
    delivery_address: '',
    delivery_latitude: null,
    delivery_longitude: null,
    payment_method: 'cash_on_delivery',
    notes: ''
});

// Methods
const fetchStores = async () => {
    loading.value = true;
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) return;

        const params = new URLSearchParams();
        if (userLocation.value.latitude && userLocation.value.longitude) {
            params.append('latitude', userLocation.value.latitude);
            params.append('longitude', userLocation.value.longitude);
        }

        const response = await axios.get(`/api/stores?${params.toString()}`, {
            headers: { Authorization: `Bearer ${token}` }
        });

        if (response.data.success) {
            stores.value = response.data.data;
        }
    } catch (error) {
        console.error("Error fetching stores:", error);
        alert("Failed to fetch stores");
    } finally {
        loading.value = false;
    }
};

const getCurrentLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLocation.value.latitude = position.coords.latitude;
                userLocation.value.longitude = position.coords.longitude;
                fetchStores();
            },
            (error) => {
                console.error("Error getting location:", error);
                alert("Unable to get your location. Please enter your address manually.");
            }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
};

const viewStore = (store) => {
    selectedStore.value = store;
};

const closeStoreModal = () => {
    selectedStore.value = null;
};

const orderProduct = (product) => {
    selectedProduct.value = product;
    orderForm.value = {
        quantity: 1,
        delivery_address: userLocation.value.address,
        delivery_latitude: userLocation.value.latitude,
        delivery_longitude: userLocation.value.longitude,
        payment_method: 'cash_on_delivery',
        notes: ''
    };
    showOrderModal.value = true;
};

const closeOrderModal = () => {
    showOrderModal.value = false;
    selectedProduct.value = null;
};

const getCurrentLocationForOrder = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                orderForm.value.delivery_latitude = position.coords.latitude;
                orderForm.value.delivery_longitude = position.coords.longitude;
            },
            (error) => {
                console.error("Error getting location:", error);
                alert("Unable to get your location. Please enter coordinates manually.");
            }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
};

const placeOrder = async () => {
    try {
        const token = localStorage.getItem("auth_token");
        if (!token) return;

        const orderData = {
            store_product_id: selectedProduct.value.id,
            quantity: orderForm.value.quantity,
            payment_method: orderForm.value.payment_method,
            delivery_address: orderForm.value.delivery_address,
            delivery_latitude: orderForm.value.delivery_latitude,
            delivery_longitude: orderForm.value.delivery_longitude,
            notes: orderForm.value.notes
        };

        const response = await axios.post("/api/orders", orderData, {
            headers: { Authorization: `Bearer ${token}` }
        });

        if (response.data.success) {
            alert("Order placed successfully!");
            closeOrderModal();
            // Refresh stores to update stock
            await fetchStores();
        }
    } catch (error) {
        console.error("Error placing order:", error);
        alert(error.response?.data?.message || "Failed to place order");
    }
};

const logout = () => {
    localStorage.removeItem("auth_token");
    router.push("/");
};

onMounted(() => {
    fetchStores();
});
</script>

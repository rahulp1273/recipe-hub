<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Order Tracking</h1>
                <p class="mt-2 text-gray-600">Track your food orders and delivery status</p>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500"></div>
            </div>

            <!-- No Orders State -->
            <div v-else-if="orders.length === 0" class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h3>
                <p class="text-gray-500 mb-6">Start ordering from your favorite stores to track your deliveries here.</p>
                <router-link to="/stores" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    Browse Stores
                </router-link>
            </div>

            <!-- Orders List -->
            <div v-else class="space-y-6">
                <div v-for="order in orders" :key="order.id" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Order Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ order.store_product.recipe.title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ order.store.name }} • {{ order.store.user.name }}</p>
                                <p class="text-sm text-gray-500 mt-1">Order #{{ order.id }} • {{ formatDate(order.created_at) }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900">${{ order.total_price }}</div>
                                <div class="text-sm text-gray-500">Qty: {{ order.quantity }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Status Timeline -->
                    <div class="p-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-4">Order Status</h4>
                        <div class="relative">
                            <!-- Timeline Line -->
                            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                            
                            <!-- Status Steps -->
                            <div class="space-y-6">
                                <!-- Pending -->
                                <div class="relative flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                         :class="getStatusClass('pending', order.status)">
                                        <svg v-if="order.status === 'pending'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <svg v-else-if="isStatusCompleted('pending', order.status)" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span v-else class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="text-sm font-medium" :class="getTextClass('pending', order.status)">Order Placed</h5>
                                        <p class="text-sm text-gray-500">Your order has been received and is being processed</p>
                                        <p v-if="order.status === 'pending'" class="text-xs text-gray-400 mt-1">{{ formatDate(order.created_at) }}</p>
                                    </div>
                                </div>

                                <!-- Confirmed -->
                                <div class="relative flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                         :class="getStatusClass('confirmed', order.status)">
                                        <svg v-if="order.status === 'confirmed'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <svg v-else-if="isStatusCompleted('confirmed', order.status)" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span v-else class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="text-sm font-medium" :class="getTextClass('confirmed', order.status)">Order Confirmed</h5>
                                        <p class="text-sm text-gray-500">Store has confirmed your order and will start preparing</p>
                                    </div>
                                </div>

                                <!-- Preparing -->
                                <div class="relative flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                         :class="getStatusClass('preparing', order.status)">
                                        <svg v-if="order.status === 'preparing'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                        </svg>
                                        <svg v-else-if="isStatusCompleted('preparing', order.status)" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span v-else class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="text-sm font-medium" :class="getTextClass('preparing', order.status)">Preparing</h5>
                                        <p class="text-sm text-gray-500">Your food is being prepared by the store</p>
                                    </div>
                                </div>

                                <!-- Ready -->
                                <div class="relative flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                         :class="getStatusClass('ready', order.status)">
                                        <svg v-if="order.status === 'ready'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <svg v-else-if="isStatusCompleted('ready', order.status)" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span v-else class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="text-sm font-medium" :class="getTextClass('ready', order.status)">Ready for Delivery</h5>
                                        <p class="text-sm text-gray-500">Your order is ready and waiting for delivery</p>
                                    </div>
                                </div>

                                <!-- Delivered -->
                                <div class="relative flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                                         :class="getStatusClass('delivered', order.status)">
                                        <svg v-if="order.status === 'delivered'" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <svg v-else-if="isStatusCompleted('delivered', order.status)" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span v-else class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                    </div>
                                    <div class="ml-4">
                                        <h5 class="text-sm font-medium" :class="getTextClass('delivered', order.status)">Delivered</h5>
                                        <p class="text-sm text-gray-500">Your order has been delivered successfully</p>
                                        <p v-if="order.delivery_time" class="text-xs text-gray-400 mt-1">Delivered at {{ formatDate(order.delivery_time) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="p-6 bg-gray-50 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h5 class="text-sm font-medium text-gray-900 mb-2">Delivery Address</h5>
                                <p class="text-sm text-gray-600">{{ order.delivery_address }}</p>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-900 mb-2">Payment Method</h5>
                                <p class="text-sm text-gray-600 capitalize">{{ order.payment_method.replace('_', ' ') }}</p>
                            </div>
                            <div v-if="order.notes">
                                <h5 class="text-sm font-medium text-gray-900 mb-2">Special Notes</h5>
                                <p class="text-sm text-gray-600">{{ order.notes }}</p>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="mt-4 flex space-x-3">
                            <button v-if="order.status === 'pending'" @click="cancelOrder(order.id)" 
                                    class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                Cancel Order
                            </button>
                            <button @click="refreshOrder(order.id)" 
                                    class="px-4 py-2 text-sm font-medium text-orange-600 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                                Refresh Status
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const orders = ref([])
const loading = ref(true)

const fetchOrders = async () => {
    try {
        const token = localStorage.getItem('auth_token')
        if (!token) {
            router.push('/login')
            return
        }

        const response = await axios.get('/api/orders', {
            headers: { Authorization: `Bearer ${token}` }
        })

        if (response.data.success) {
            orders.value = response.data.data
        }
    } catch (error) {
        console.error('Error fetching orders:', error)
        alert('Failed to load orders. Please try again.')
    } finally {
        loading.value = false
    }
}

const cancelOrder = async (orderId) => {
    if (!confirm('Are you sure you want to cancel this order?')) return

    try {
        const token = localStorage.getItem('auth_token')
        const response = await axios.post(`/api/orders/${orderId}/cancel`, {}, {
            headers: { Authorization: `Bearer ${token}` }
        })

        if (response.data.success) {
            alert('Order cancelled successfully')
            await fetchOrders() // Refresh the orders list
        }
    } catch (error) {
        console.error('Error cancelling order:', error)
        alert(error.response?.data?.message || 'Failed to cancel order')
    }
}

const refreshOrder = async (orderId) => {
    try {
        const token = localStorage.getItem('auth_token')
        const response = await axios.get(`/api/orders/${orderId}`, {
            headers: { Authorization: `Bearer ${token}` }
        })

        if (response.data.success) {
            // Update the specific order in the list
            const index = orders.value.findIndex(order => order.id === orderId)
            if (index !== -1) {
                orders.value[index] = response.data.data
            }
        }
    } catch (error) {
        console.error('Error refreshing order:', error)
        alert('Failed to refresh order status')
    }
}

const getStatusClass = (status, currentStatus) => {
    if (status === currentStatus) {
        return 'bg-orange-500 text-white'
    } else if (isStatusCompleted(status, currentStatus)) {
        return 'bg-green-500 text-white'
    } else {
        return 'bg-gray-200 text-gray-400'
    }
}

const getTextClass = (status, currentStatus) => {
    if (status === currentStatus) {
        return 'text-orange-600'
    } else if (isStatusCompleted(status, currentStatus)) {
        return 'text-green-600'
    } else {
        return 'text-gray-500'
    }
}

const isStatusCompleted = (status, currentStatus) => {
    const statusOrder = ['pending', 'confirmed', 'preparing', 'ready', 'delivered']
    const currentIndex = statusOrder.indexOf(currentStatus)
    const statusIndex = statusOrder.indexOf(status)
    return statusIndex < currentIndex
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString()
}

onMounted(() => {
    fetchOrders()
})
</script>

<template>
  <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <span class="text-2xl">💬</span>
        <h3 class="text-xl font-bold text-gray-800">Comments & Reviews</h3>
        <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-sm font-medium">
          {{ comments.length }}
        </span>
      </div>
    </div>

    <!-- Add Comment Form -->
    <div v-if="!userHasCommented" class="mb-8 p-6 bg-gradient-to-r from-orange-50 to-red-50 rounded-xl border border-orange-200">
      <h4 class="text-lg font-semibold text-gray-800 mb-4">Share Your Experience</h4>
      
      <form @submit.prevent="submitComment" class="space-y-4">
        <!-- Rating -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Rating (Optional)</label>
          <div class="flex space-x-2">
            <button
              v-for="star in 5"
              :key="star"
              type="button"
              @click="form.rating = star"
              class="text-2xl transition-all duration-200 hover:scale-110"
              :class="star <= form.rating ? 'text-yellow-400' : 'text-gray-300'"
            >
              ⭐
            </button>
          </div>
        </div>

        <!-- Comment Text -->
        <div class="space-y-2">
          <label for="comment" class="block text-sm font-medium text-gray-700">Your Comment</label>
          <textarea
            id="comment"
            v-model="form.comment"
            rows="4"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
            placeholder="Share your cooking experience, tips, or thoughts about this recipe..."
          ></textarea>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
          <button
            type="submit"
            :disabled="isSubmitting"
            class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg"
          >
            {{ isSubmitting ? 'Posting...' : 'Post Comment' }}
          </button>
        </div>
      </form>
    </div>

    <!-- User's Existing Comment -->
    <div v-if="userHasCommented" class="mb-8 p-6 bg-green-50 rounded-xl border border-green-200">
      <div class="flex items-center justify-between mb-4">
        <h4 class="text-lg font-semibold text-gray-800">Your Comment</h4>
        <button
          @click="editMode = !editMode"
          class="text-blue-600 hover:text-blue-700 font-medium text-sm"
        >
          {{ editMode ? 'Cancel' : 'Edit' }}
        </button>
      </div>

      <div v-if="!editMode">
        <div class="flex items-center space-x-2 mb-3">
          <div v-if="userComment.rating" class="flex space-x-1">
            <span v-for="star in 5" :key="star" class="text-lg" :class="star <= userComment.rating ? 'text-yellow-400' : 'text-gray-300'">⭐</span>
          </div>
          <span class="text-sm text-gray-500">{{ formatDate(userComment.created_at) }}</span>
        </div>
        <p class="text-gray-700">{{ userComment.comment }}</p>
      </div>

      <!-- Edit Form -->
      <form v-else @submit.prevent="updateComment" class="space-y-4">
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Rating</label>
          <div class="flex space-x-2">
            <button
              v-for="star in 5"
              :key="star"
              type="button"
              @click="editForm.rating = star"
              class="text-2xl transition-all duration-200 hover:scale-110"
              :class="star <= editForm.rating ? 'text-yellow-400' : 'text-gray-300'"
            >
              ⭐
            </button>
          </div>
        </div>

        <div class="space-y-2">
          <label for="edit-comment" class="block text-sm font-medium text-gray-700">Comment</label>
          <textarea
            id="edit-comment"
            v-model="editForm.comment"
            rows="4"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
          ></textarea>
        </div>

        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="editMode = false"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-medium transition-all duration-200"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="isUpdating"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl font-medium transition-all duration-200 disabled:opacity-50"
          >
            {{ isUpdating ? 'Updating...' : 'Update' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Comments List -->
    <div class="space-y-6">
      <div v-if="comments.length === 0" class="text-center py-8 text-gray-500">
        <span class="text-4xl mb-4 block">💭</span>
        <p>No comments yet. Be the first to share your experience!</p>
      </div>

      <div v-for="comment in comments" :key="comment.id" class="border-b border-gray-100 pb-6 last:border-b-0">
        <div class="flex items-start space-x-4">
          <!-- User Avatar -->
          <div class="w-12 h-12 bg-gradient-to-r from-orange-400 to-red-400 rounded-full flex items-center justify-center text-white font-bold text-lg">
            {{ comment.user.avatar_path ? '🖼️' : comment.user.name.charAt(0).toUpperCase() }}
          </div>

          <!-- Comment Content -->
          <div class="flex-1">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center space-x-3">
                <h5 class="font-semibold text-gray-800">{{ comment.user.name }}</h5>
                <span class="text-sm text-gray-500">{{ formatDate(comment.created_at) }}</span>
              </div>
              
              <!-- Delete Button (for user's own comments) -->
              <button
                v-if="comment.user_id === currentUserId"
                @click="deleteComment(comment.id)"
                class="text-red-500 hover:text-red-700 text-sm font-medium"
              >
                Delete
              </button>
            </div>

            <!-- Rating -->
            <div v-if="comment.rating" class="flex items-center space-x-1 mb-3">
              <span v-for="star in 5" :key="star" class="text-lg" :class="star <= comment.rating ? 'text-yellow-400' : 'text-gray-300'">⭐</span>
            </div>

            <!-- Comment Text -->
            <p class="text-gray-700 leading-relaxed">{{ comment.comment }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500 mx-auto"></div>
      <p class="text-gray-500 mt-2">Loading comments...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  recipeId: {
    type: [Number, String],
    required: true
  }
})

const comments = ref([])
const isLoading = ref(false)
const isSubmitting = ref(false)
const isUpdating = ref(false)
const editMode = ref(false)

const form = ref({
  comment: '',
  rating: 0
})

const editForm = ref({
  comment: '',
  rating: 0
})

const currentUserId = ref(null)

// Computed properties
const userHasCommented = computed(() => {
  return comments.value.some(comment => comment.user_id === currentUserId.value)
})

const userComment = computed(() => {
  return comments.value.find(comment => comment.user_id === currentUserId.value)
})

// Methods
const loadComments = async () => {
  isLoading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.get(`/api/recipes/${props.recipeId}/comments`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    comments.value = response.data.data
  } catch (error) {
    console.error('Error loading comments:', error)
  } finally {
    isLoading.value = false
  }
}

const submitComment = async () => {
  isSubmitting.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.post(`/api/recipes/${props.recipeId}/comments`, {
      comment: form.value.comment,
      rating: form.value.rating || null
    }, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    comments.value.unshift(response.data.data)
    form.value = { comment: '', rating: 0 }
  } catch (error) {
    console.error('Error submitting comment:', error)
    alert(error.response?.data?.message || 'Failed to post comment')
  } finally {
    isSubmitting.value = false
  }
}

const updateComment = async () => {
  isUpdating.value = true
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.put(`/api/recipes/${props.recipeId}/comments/${userComment.value.id}`, {
      comment: editForm.value.comment,
      rating: editForm.value.rating || null
    }, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    const index = comments.value.findIndex(c => c.id === userComment.value.id)
    comments.value[index] = response.data.data
    editMode.value = false
  } catch (error) {
    console.error('Error updating comment:', error)
    alert(error.response?.data?.message || 'Failed to update comment')
  } finally {
    isUpdating.value = false
  }
}

const deleteComment = async (commentId) => {
  if (!confirm('Are you sure you want to delete this comment?')) return
  
  try {
    const token = localStorage.getItem('auth_token')
    await axios.delete(`/api/recipes/${props.recipeId}/comments/${commentId}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    
    comments.value = comments.value.filter(c => c.id !== commentId)
  } catch (error) {
    console.error('Error deleting comment:', error)
    alert(error.response?.data?.message || 'Failed to delete comment')
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))
  
  if (diffInHours < 1) return 'Just now'
  if (diffInHours < 24) return `${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`
  
  const diffInDays = Math.floor(diffInHours / 24)
  if (diffInDays < 7) return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`
  
  return date.toLocaleDateString()
}

const getCurrentUser = async () => {
  try {
    const token = localStorage.getItem('auth_token')
    const response = await axios.get('/api/user', {
      headers: { 'Authorization': `Bearer ${token}` }
    })
    currentUserId.value = response.data.id
  } catch (error) {
    console.error('Error getting current user:', error)
  }
}

// Lifecycle
onMounted(async () => {
  await getCurrentUser()
  await loadComments()
})
</script> 
import { createRouter, createWebHistory } from 'vue-router'
import Home from '../pages/Home.vue'
import Login from '../pages/auth/Login.vue'
import Register from '../pages/auth/Register.vue'
import Dashboard from '../pages/Dashboard.vue'
import CreateRecipe from '../pages/recipes/CreateRecipe.vue'
import RecipeList from '../pages/recipes/RecipeList.vue'
import RecipeView from '../pages/recipes/RecipeView.vue'
import RecipeEdit from '../pages/recipes/RecipeEdit.vue'
import UserProfile from '../pages/UserProfile.vue'
import HomeFeed from '@/pages/social/HomeFeed.vue'
import PublicRecipeView from '../pages/recipes/PublicRecipeView.vue'
import Collections from '../pages/Collections.vue'
import CollectionView from '../pages/CollectionView.vue'
import PublicCollections from '../pages/PublicCollections.vue'
import MyStore from '../pages/stores/MyStore.vue'
import BrowseStores from '../pages/stores/BrowseStores.vue'
import OrderTracking from '../pages/orders/OrderTracking.vue'


const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/recipes/create',
    name: 'CreateRecipe',
    component: CreateRecipe,
    meta: { requiresAuth: true }
  },
  {
    path: '/recipes',
    name: 'RecipeList',
    component: RecipeList,
    meta: { requiresAuth: true }
  },
  {
    path: '/recipes/:id',
    name: 'RecipeView',
    component: RecipeView,
    meta: { requiresAuth: true }
  },
  {
    path: '/recipes/:id/edit',
    name: 'RecipeEdit',
    component: RecipeEdit,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'UserProfile',
    component: UserProfile,
    meta: { requiresAuth: true }
  },
   // Social routes - PUBLIC ACCESS
  {
    path: '/feed',
    name: 'HomeFeed',
    component: HomeFeed,
    meta: { requiresAuth: false, public: true } // Explicitly public
  },
  // for social view more details
  {
  path: '/public/recipe/:id',
  name: 'PublicRecipeView',
  component: PublicRecipeView,
  meta: { requiresAuth: false } // Public access
},
  {
    path: '/collections',
    name: 'Collections',
    component: Collections,
    meta: { requiresAuth: true }
  },
  {
    path: '/collections/:id',
    name: 'CollectionView',
    component: CollectionView,
    meta: { requiresAuth: true }
  },
  {
    path: '/public/collections',
    name: 'PublicCollections',
    component: PublicCollections,
    meta: { requiresAuth: false }
  },
  {
    path: '/my-store',
    name: 'MyStore',
    component: MyStore,
    meta: { requiresAuth: true }
  },
  {
    path: '/stores',
    name: 'BrowseStores',
    component: BrowseStores,
    meta: { requiresAuth: true }
  },
  {
    path: '/orders',
    name: 'OrderTracking',
    component: OrderTracking,
    meta: { requiresAuth: true }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// RESTORE ROUTER GUARD WITH PUBLIC ACCESS
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')
  console.log('Router guard - To:', to.path, 'Requires auth:', to.meta.requiresAuth, 'Token:', token ? 'Present' : 'Not present')
  
  // Public routes - always allow
  if (to.meta.requiresAuth === false) {
    console.log('Public route - proceeding')
    next()
    return
  }
  
  // Auth required routes
  if (to.meta.requiresAuth && !token) {
    console.log('Auth required but no token - redirecting to login')
    next('/login')
  } else {
    console.log('Proceeding to route')
    next()
  }
})

export default router

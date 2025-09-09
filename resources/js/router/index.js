import { createRouter, createWebHistory } from 'vue-router'
import Home from "../Pages/Home.vue";
import Login from '../Pages/Auth/Login.vue';
import Register from '../Pages/Auth/Register.vue';
import Dashboard from '../Pages/Dashboard.vue';
import CreateRecipe from '../Pages/recipes/CreateRecipe.vue';
import RecipeList from '../Pages/recipes/RecipeList.vue';
import RecipeView from '../Pages/recipes/RecipeView.vue';
import RecipeEdit from '../Pages/recipes/RecipeEdit.vue';
import UserProfile from '../Pages/UserProfile.vue';
import HomeFeed from '@/Pages/social/HomeFeed.vue';
import PublicRecipeView from '../Pages/recipes/PublicRecipeView.vue';
import Collections from '../Pages/Collections.vue';
import CollectionView from '../Pages/CollectionView.vue';
import PublicCollections from '../Pages/PublicCollections.vue';
import MyStore from '../Pages/stores/MyStore.vue';
import BrowseStores from '../Pages/stores/BrowseStores.vue';
import OrderTracking from '../Pages/orders/OrderTracking.vue';


const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { requiresAuth: false }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresAuth: false }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresAuth: false }
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

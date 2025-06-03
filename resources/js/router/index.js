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
   // Social routes
  {
    path: '/feed',
    name: 'HomeFeed',
    component: HomeFeed,
    meta: { requiresAuth: false } // Public access
  },
  // for social view more details
  {
  path: '/public/recipe/:id',
  name: 'PublicRecipeView',
  component: PublicRecipeView,
  meta: { requiresAuth: false } // Public access
},
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')
  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else {
    next()
  }
})

export default router

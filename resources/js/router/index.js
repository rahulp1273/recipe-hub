import { createRouter, createWebHistory } from 'vue-router'
import Home from '../pages/Home.vue'
import Login from '../pages/auth/Login.vue'
import Register from '../pages/auth/Register.vue'
import Dashboard from '../pages/Dashboard.vue'
import CreateRecipe from '../pages/recipes/CreateRecipe.vue'
import RecipeList from '../pages/recipes/RecipeList.vue'
import RecipeView from '../pages/recipes/RecipeView.vue'  // ✅ Fixed
import RecipeEdit from '../pages/recipes/RecipeEdit.vue'


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
    component: Dashboard
  },
  {
    path: '/recipes/create',
    name: 'CreateRecipe',
    component: CreateRecipe
  },
  {
  path: '/recipes',
  name: 'RecipeList',
  component: RecipeList
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
}

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

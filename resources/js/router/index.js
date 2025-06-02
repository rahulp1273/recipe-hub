import { createRouter, createWebHistory } from 'vue-router'
import Home from '../pages/Home.vue'
import Login from '../pages/auth/Login.vue'
import Register from '../pages/auth/Register.vue'
import Dashboard from '../pages/Dashboard.vue'
import CreateRecipe from '../pages/recipes/CreateRecipe.vue'
import RecipeList from '../pages/recipes/RecipeList.vue'


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
}
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router

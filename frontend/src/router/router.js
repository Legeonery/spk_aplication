import { createRouter, createWebHistory } from 'vue-router'
import Warehouse from '../views/Warehouse.vue'
import Transport from '../views/Transport.vue'
import LoginView from '@/views/LoginView.vue'
import App from '@/App.vue'

const routes = [
  { path: '/warehouse', component: Warehouse, meta: { requiresAuth: true } },
  { path: '/transport', component: Transport, meta: { requiresAuth: true } },
  { path: '/login', component: LoginView },
  { path: '/', component: App },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  if (to.path !== '/login' && !token) {
    next('/login') // Перенаправление на страницу входа, если нет токена
  } else {
    next() // Иначе продолжаем переход
  }
})

export default router

import { createRouter, createWebHistory } from 'vue-router'
import Sklad from '../views/Sklad.vue'
import Transport from '../views/Transport.vue'

const routes = [
  { path: '/sklad', component: Sklad },
  { path: '/transport', component: Transport },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router

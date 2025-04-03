import { createRouter, createWebHistory } from 'vue-router'
import Warehouse from '../views/Warehouse.vue'
import Transport from '../views/Transport.vue'

const routes = [
  { path: '/warehouse', component: Warehouse },
  { path: '/transport', component: Transport },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router

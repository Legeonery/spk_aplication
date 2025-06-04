import { createRouter, createWebHistory } from 'vue-router'
import Warehouse from '../views/Warehouse.vue'
import Transport from '../views/Transport.vue'
import Staff from '../views/Staff.vue'
import Settings from '../views/Settings.vue'
import LoginView from '@/views/LoginView.vue'
import MainLayout from '@/layouts/MainLayout.vue' // 👈 наш layout
import WarehousePage from '@/views/WarehousePage.vue'
import WarehouseSparePartsPage from '@/views/WarehouseSparePartsPage.vue'

const routes = [
  {
    path: '/',
    component: MainLayout, // 👈 здесь оборачиваем
    meta: { requiresAuth: true },
    children: [
      { path: '', redirect: '/login' }, // 👈 перенаправление на что-то по умолчанию
      { path: 'warehouse', component: Warehouse },
      { path: 'transport', component: Transport },
      { path: 'staff', component: Staff },
      { path: 'settings', component: Settings },
      { path: 'warehouse/:id', component: WarehousePage },
      {
        path: '/warehouse-spare-parts/:id',
        component: WarehouseSparePartsPage,
      },
    ],
  },
  { path: '/login', component: LoginView },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else {
    next()
  }
})

export default router

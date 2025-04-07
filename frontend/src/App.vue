<script setup>
import { ref, onBeforeMount, nextTick } from 'vue'
import Navbar from './components/Navbar.vue'
import Sidebar from './components/Sidebar.vue'
import { useRouter } from 'vue-router'

const isSidebarOpen = ref(false)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const closeSidebar = (event) => {
  if (!event.target.closest('.sidebar')) {
    isSidebarOpen.value = false
  }
}
const isAuthenticated = ref(false)
const router = useRouter()

// Проверка на наличие токена в localStorage
onBeforeMount(() => {
  nextTick(() => {
    const token = localStorage.getItem('token')
    if (token) {
      isAuthenticated.value = true
    } else {
      isAuthenticated.value = false
      router.push('/login') // Перенаправляем на страницу входа, если нет токена
    }
  })
})
</script>

<template>
  <div @click="closeSidebar">
    <!-- Если пользователь не авторизован, показываем только страницу логина -->
    <template v-if="isAuthenticated">
      <Navbar @toggle-sidebar="toggleSidebar" />
      <Sidebar :isOpen="isSidebarOpen" @close-sidebar="toggleSidebar" class="sidebar" />
      <main class="p-4">
        <router-view></router-view>
      </main>
    </template>

    <!-- Если пользователь авторизован, показываем основной контент -->
    <template v-else>
      <router-view />
    </template>
  </div>
</template>

<style scoped>
header {
  line-height: 1.5;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

@media (min-width: 1024px) {
  header {
    display: flex;
    place-items: center;
  }

  .logo {
    margin: 0 2rem 0 0;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }
}
</style>

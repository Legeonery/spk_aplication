<script setup>
import { ref, onMounted } from 'vue'
import { Menu as MenuIcon, User as UserIcon } from 'lucide-vue-next'
import { logout } from '@/services/auth'

const emit = defineEmits(['toggle-sidebar'])

const menuOpen = ref(false)
const isAuthenticated = ref(false)

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value
}

const handleSidebarToggle = (event) => {
  event.stopPropagation()
  emit('toggle-sidebar')
}

const handleLogout = () => {
  logout()
  isAuthenticated.value = false
}
</script>

<template>
  <header class="bg-white shadow-md px-4 py-2 flex items-center justify-between h-14">
    <!-- Кнопка меню -->
    <button @click="handleSidebarToggle" class="text-gray-600 hover:text-gray-900">
      <MenuIcon class="w-8 h-8" />
    </button>

    <!-- Лого -->
    <div class="flex items-center space-x-2">
      <img src="../../public/logo-site.png" width="40" alt="Logo" />
      <h1 class="text-4xl font-black main-text-title">AgroРесурс</h1>
    </div>

    <!-- Выпадающее меню профиля -->
    <div class="relative">
      <button @click="toggleMenu" class="flex items-center space-x-2">
        <UserIcon class="w-8 h-8 text-gray-700 hover:text-gray-900" />
      </button>

      <div v-if="menuOpen" class="absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md z-10">
        <ul class="py-2">
          <li>
            <router-link
              to="/profile"
              class="block px-4 py-2 rounded hover:bg-gray-400 font-semibold second-text-button color-src"
            >
              Профиль
            </router-link>
          </li>
          <li>
            <button
              @click="handleLogout"
              class="block w-full text-left px-4 py-2 new rounded font-semibold second-text-button color-src"
            >
              Выйти
            </button>
          </li>
        </ul>
      </div>
    </div>
  </header>
</template>

<style scoped>
header {
  background: #bdbdbd;
}
.new:hover {
  background: hsl(204, 70%, 53%, 0.2);
}
</style>

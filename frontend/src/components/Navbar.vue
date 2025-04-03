<script setup>
import { ref } from 'vue'
import { Menu as MenuIcon, User as UserIcon } from 'lucide-vue-next'

const emit = defineEmits(['toggle-sidebar']) // <-- ОБЪЯВЛЯЕМ emit

const menuOpen = ref(false)

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value
}

// Обработчик клика
const handleSidebarToggle = (event) => {
  event.stopPropagation() // Остановить всплытие клика
  emit('toggle-sidebar') // <-- Используем emit правильно
}
</script>

<template>
  <header class="bg-white shadow-md px-4 py-2 flex items-center justify-between h-14">
    <!-- Кнопка открытия бокового меню -->
    <button @click="handleSidebarToggle" class="text-gray-600 hover:text-gray-900">
      <MenuIcon class="w-8 h-8" />
    </button>

    <!-- Название сайта -->
    <div class="flex items-center space-x-2">
      <img src="../../public/logo-site.png" width="40" alt="Logo" />
      <h1 class="text-4xl font-black main-text-title">AgroРесурс</h1>
    </div>
    <!-- Профиль с выпадающим меню -->
    <div class="relative">
      <button @click="toggleMenu" class="flex items-center space-x-2">
        <UserIcon class="w-8 h-8 text-gray-700 hover:text-gray-900" />
      </button>

      <div v-if="menuOpen" class="absolute right-0 mt-2 w-40 bg-white shadow-lg rounded-md">
        <ul class="py-2">
          <li>
            <router-link
              to="/sklad"
              class="block px-4 py-2 rounded hover:bg-gray-400 font-semibold second-text-button color-src"
            >
              Профиль
            </router-link>
          </li>
          <li>
            <router-link
              to="/sklad"
              class="block px-4 py-2 rounded hover:bg-gray-400 font-semibold second-text-button color-src"
            >
              Выйти
            </router-link>
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
</style>

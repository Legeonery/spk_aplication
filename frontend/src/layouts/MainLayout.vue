<!-- src/layouts/MainLayout.vue -->
<script setup>
import { ref } from 'vue'
import Navbar from '../components/Navbar.vue'
import Sidebar from '../components/Sidebar.vue'

const isSidebarOpen = ref(false)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const closeSidebar = (event) => {
  if (!event.target.closest('.sidebar')) {
    isSidebarOpen.value = false
  }
}
</script>

<template>
  <div @click="closeSidebar">
    <Navbar @toggle-sidebar="toggleSidebar" />
    <Sidebar :isOpen="isSidebarOpen" @close-sidebar="toggleSidebar" class="sidebar" />
    <main class="p-4">
      <router-view></router-view>
    </main>
  </div>
</template>

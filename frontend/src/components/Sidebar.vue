<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { X as XIcon } from 'lucide-vue-next'

const props = defineProps(['isOpen'])
const emit = defineEmits(['close-sidebar'])

const sidebarRef = ref(null)

// Закрытие при клике вне меню
const handleClickOutside = (event) => {
  if (props.isOpen && sidebarRef.value && !sidebarRef.value.contains(event.target)) {
    emit('close-sidebar')
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <aside
    ref="sidebarRef"
    class="sidebar fixed top-0 left-0 w-64 h-full bg-white shadow-lg transform transition-transform z-50"
    :class="{ 'translate-x-0': isOpen, '-translate-x-full': !isOpen }"
    @click.stop
  >
    <div class="p-2 flex justify-between items-center border-b h-14">
      <h2 class="text-lg font-black main-text-title">Меню</h2>
      <button @click="$emit('close-sidebar')">
        <XIcon class="w-6 h-6 text-gray-700 hover:text-gray-900" />
      </button>
    </div>

    <nav class="p-4">
      <ul class="space-y-2">
        <li>
          <router-link
            to="/warehouse"
            class="font-semibold block px-4 py-2 rounded hover:bg-gray-400 second-text-button color-src"
          >
            Склад
          </router-link>
        </li>
        <li>
          <router-link
            to="/transport"
            class="font-semibold block px-4 py-2 rounded hover:bg-gray-400 second-text-button color-src"
          >
            Транспорт
          </router-link>
        </li>
        <li>
          <router-link
            to="/staff"
            class="font-semibold block px-4 py-2 rounded hover:bg-gray-400 second-text-button color-src"
          >
            Сотрудники
          </router-link>
        </li>
        <li>
          <router-link
            to="/repair_facility"
            class="font-semibold block px-4 py-2 rounded hover:bg-gray-400 second-text-button color-src"
          >
            Ремонтная база
          </router-link>
        </li>
        <li>
          <router-link
            to="/settings"
            class="font-semibold block px-4 py-2 rounded hover:bg-gray-400 second-text-button color-src"
          >
            Настройки
          </router-link>
        </li>
      </ul>
    </nav>
  </aside>
</template>

<style scoped>
aside {
  background: #bdbdbd;
}
router-link:hover {
  background: #4c8fce;
}
</style>

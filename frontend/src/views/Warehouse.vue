<script setup>
import { Plus as PlusIcon } from 'lucide-vue-next'
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Складские помещения</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <!-- Карточка добавления склада -->
      <button
        class="flex flex-col items-center justify-center border-2 border-dashed rounded-md border-gray-300 rounded-lg p-8"
      >
        <PlusIcon class="w-12 h-12 text-gray-400" />
        <span class="text-gray-500 mt-2">Добавить склад</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
button {
  transition: all 0.4s ease;
}
@media (hover: hover) {
  button:hover {
    background: hsl(204, 70%, 53%, 0.2);
  }
}
</style>
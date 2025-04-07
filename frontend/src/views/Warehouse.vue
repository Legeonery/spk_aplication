<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { Plus as PlusIcon } from 'lucide-vue-next'

const warehouses = ref([])

onMounted(async () => {
  try {
    const response = await api.get('/warehouses')
    warehouses.value = response.data.data
  } catch (error) {
    console.error('Ошибка при загрузке складов:', error)
  }
})
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Складские помещения</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
      <!-- Карточка добавления склада -->
      <button
        class="flex flex-col items-center justify-center border-2 border-dashed rounded-md border-gray-300 p-8"
      >
        <PlusIcon class="w-12 h-12 text-gray-400" />
        <span class="text-gray-500 mt-2">Добавить склад</span>
      </button>

      <!-- Список складов -->
      <div
        v-for="warehouse in warehouses"
        :key="warehouse.id"
        class="p-4 flex flex-col items-center justify-center border-2 rounded-md shadow-sm"
      >
        <img src="../../public/wheat (1).png" width="120px" />
        <span class="text-gray-800 mt-2 font-medium">{{ warehouse.name }}</span>
        <span class="text-gray-500 text-sm">Тип: {{ warehouse.type }}</span>
        <span class="text-gray-500 text-sm">Площадь: {{ warehouse.area }} м²</span>
        <span class="text-gray-500 text-sm"
          >Макс. загрузка: {{ warehouse.max_historical_load }}</span
        >
      </div>
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

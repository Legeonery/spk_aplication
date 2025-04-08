<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const warehouse = ref(null)

const fetchWarehouse = async () => {
  try {
    const response = await api.get(`/warehouses/${route.params.id}`)
    warehouse.value = response.data.data
  } catch (error) {
    console.error('Ошибка при получении склада:', error)
  }
}

onMounted(fetchWarehouse)
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Информация о складе</h1>
    <div v-if="warehouse">
      <p><strong>Наименование:</strong> {{ warehouse.name }}</p>
      <p><strong>Тип:</strong> {{ warehouse.type }}</p>
      <p><strong>Площадь:</strong> {{ warehouse.area }} м²</p>
      <p><strong>Макс. загрузка:</strong> {{ warehouse.max_historical_load }} тонн</p>
    </div>
    <div v-else>
      <p>Загрузка данных...</p>
    </div>
  </div>
</template>

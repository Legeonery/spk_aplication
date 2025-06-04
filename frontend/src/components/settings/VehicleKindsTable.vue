<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const kinds = ref([])

const fetchVehicleKinds = async () => {
  const { data } = await api.get('/vehicle-kinds')
  kinds.value = data
}

onMounted(fetchVehicleKinds)
</script>

<template>
  <div>
    <div class="flex justify-between mb-4">
      <h2 class="text-lg font-semibold">Типы транспортных средств</h2>
      <!-- future button: добавить -->
    </div>

    <table class="min-w-full border text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border text-left">ID</th>
          <th class="px-4 py-2 border text-left">Название</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="kind in kinds" :key="kind.id" class="hover:bg-gray-50">
          <td class="border px-4 py-2">{{ kind.id }}</td>
          <td class="border px-4 py-2">{{ kind.name }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

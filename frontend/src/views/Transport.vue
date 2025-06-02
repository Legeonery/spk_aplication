<script setup>
import { onMounted, ref } from 'vue'
import { Plus as PlusIcon } from 'lucide-vue-next'
import TransportForm from '@/components/TransportForm.vue'
import api from '@/services/api'

const vehicles = ref([])
const showModal = ref(false)

const imageByKind = {
  трактор: '/tractor.png',
  камаз: '/truck.png',
  комбайн: '/combine.png',
  погрузчик: '/loader.png',
  'гусеничная техника': '/tractor-crawler.png',
}

const fetchVehicles = async () => {
  try {
    const response = await api.get('/vehicles')
    vehicles.value = response.data
  } catch (e) {
    console.error('Ошибка загрузки транспорта', e)
  }
}

onMounted(fetchVehicles)

const openForm = () => {
  showModal.value = true
}

const handleFormSuccess = () => {
  showModal.value = false
  fetchVehicles()
}
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Транспорт</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
      <!-- Кнопка добавления -->
      <button class="flex flex-col items-center justify-center border-2 border-dashed rounded-md border-gray-300 p-8"
        @click="openForm">
        <PlusIcon class="w-14 h-14 text-gray-400" />
        <span class="text-gray-500 mt-2 font-medium">Добавить ТС</span>
      </button>

      <!-- Карточки ТС -->
      <router-link v-for="vehicle in vehicles" :key="vehicle.id" :to="`/transport/${vehicle.id}`"
        class="p-4 flex flex-col items-center justify-center border-2 rounded-md hover:bg-blue-50 transition">
        <img :src="imageByKind[vehicle.vehicle_kind?.name] || '/default.png'" width="140" />
        <pre class="text-xs text-gray-400">{{ vehicle.vehicle_kind?.name }}</pre>
        <span class="mt-2 font-medium text-gray-700">{{ vehicle.name || 'Без названия' }}</span>
        <span class="text-gray-500">{{ vehicle.number }}</span>
        <span class="mt-1 text-sm font-semibold" :class="vehicle.is_available ? 'text-green-600' : 'text-red-500'">
          ● {{ vehicle.is_available ? 'Свободно' : 'Занято' }}
        </span>
      </router-link>
    </div>

    <!-- Модальное окно -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <TransportForm :show="showModal" @saved="handleFormSuccess" @close="showModal = false" />
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { Plus as PlusIcon } from 'lucide-vue-next'
import TransportForm from '@/components/TransportForm.vue'
import api from '@/services/api'

const vehicles = ref([])
const showModal = ref(false)
const confirmModal = ref(false)
const selectedVehicle = ref(null)

const confirmToggle = (vehicle) => {
  selectedVehicle.value = vehicle
  confirmModal.value = true
}

const toggleStatus = async () => {
  if (!selectedVehicle.value) return

  try {
    await api.patch(`/vehicles/${selectedVehicle.value.id}/toggle-availability`)
    confirmModal.value = false
    selectedVehicle.value = null
    await fetchVehicles()
  } catch (e) {
    console.error('Ошибка при смене статуса:', e)
  }
}

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
      <!-- Карточки ТС -->
      <div v-for="vehicle in vehicles" :key="vehicle.id"
        class="p-4 flex flex-col items-center justify-center border-2 rounded-md hover:bg-blue-50 transition cursor-pointer"
        @click="confirmToggle(vehicle)">
        <img :src="imageByKind[vehicle.vehicle_kind?.name] || '/default.png'" width="140" />
        <pre class="text-xs text-gray-400">{{ vehicle.vehicle_kind?.name }}</pre>
        <span class="mt-2 font-medium text-gray-700">{{ vehicle.name || 'Без названия' }}</span>
        <span class="text-gray-500">{{ vehicle.number }}</span>
        <span class="mt-1 text-sm font-semibold" :class="vehicle.is_available ? 'text-green-600' : 'text-red-500'">
          ● {{ vehicle.is_available ? 'Свободно' : 'Занято' }}
        </span>
      </div>

      <!-- Модальное подтверждение -->
      <div v-if="confirmModal" class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 shadow-xl max-w-sm w-full">
          <h2 class="text-lg font-bold mb-4">Подтверждение</h2>
          <p class="mb-4">Вы уверены, что хотите {{ selectedVehicle?.is_available ? 'занять' : 'освободить' }} ТС
            <strong>{{ selectedVehicle?.number }}</strong>?
          </p>
          <div class="flex justify-end gap-2">
            <button @click="confirmModal = false"
              class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Отмена</button>
            <button @click="toggleStatus"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Подтвердить</button>
          </div>
        </div>
      </div>

    </div>

    <!-- Модальное окно -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <TransportForm :show="showModal" @saved="handleFormSuccess" @close="showModal = false" />
    </div>
  </div>
</template>

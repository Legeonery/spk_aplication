<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import BarChart from '@/components/BarChart.vue'
import CreateGrainDelivery from '@/components/CreateGrainDelivery.vue'

const showDeliveryModal = ref(false)

const route = useRoute()
const router = useRouter()

const warehouse = ref(null)
const grains = ref([])
const deliveries = ref([])
const shipments = ref([])
const showEditModal = ref(false)

const form = ref({
  name: '',
  type: '',
  area: null,
  max_historical_load: null,
})
const error = ref('')

const fetchWarehouse = async () => {
  try {
    const response = await api.get(`/warehouses/${route.params.id}`)
    warehouse.value = response.data.data
    Object.assign(form.value, warehouse.value)
    await fetchGrains()
    await fetchDeliveries()
    await fetchShipments()
  } catch (error) {
    console.error('Ошибка при получении склада:', error)
  }
}

const fetchGrains = async () => {
  try {
    const response = await api.get(`/warehouses/${route.params.id}/grains`)
    grains.value = response.data
  } catch (error) {
    console.error('Ошибка при загрузке остатков:', error)
  }
}

const fetchDeliveries = async () => {
  const res = await api.get(`/warehouses/${route.params.id}/deliveries`)
  deliveries.value = res.data
}
const fetchShipments = async () => {
  const res = await api.get(`/warehouses/${route.params.id}/shipments`)
  shipments.value = res.data
}

const openEditModal = () => {
  Object.assign(form.value, warehouse.value)
  showEditModal.value = true
}
const closeEditModal = () => {
  showEditModal.value = false
}
const updateWarehouse = async () => {
  error.value = ''
  if (!form.value.name || !form.value.type) {
    error.value = 'Укажите имя и тип склада'
    return
  }

  if (form.value.type !== 'зерновой') {
    form.value.max_historical_load = null
  }

  try {
    const response = await api.put(`/warehouses/${route.params.id}`, form.value)
    warehouse.value = response.data.data
    showEditModal.value = false
  } catch (err) {
    error.value = 'Ошибка при сохранении'
    console.error(err)
  }
}

const deleteWarehouse = async () => {
  if (!confirm('Удалить склад?')) return
  await api.delete(`/warehouses/${route.params.id}`)
  router.push('/warehouses')
}

const downloadReport = async () => {
  const res = await api.get(`/warehouses/${route.params.id}/report`, { responseType: 'blob' })
  const blob = new Blob([res.data], { type: 'application/pdf' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = 'report.pdf'
  link.click()
}

onMounted(fetchWarehouse)
const deliveryChartData = computed(() => ({
  labels: deliveries.value.map(d => d.delivery_date),
  datasets: [
    {
      label: 'Поставки (т)',
      backgroundColor: '#36A2EB',
      data: deliveries.value.map(d => d.volume)
    }
  ]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'top' },
    title: { display: true, text: 'Объём поставок по датам' }
  }
}
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Информация о складе</h1>

    <div v-if="warehouse">
      <p><strong>Наименование:</strong> {{ warehouse.name }}</p>
      <p><strong>Тип:</strong> {{ warehouse.type }}</p>
      <p><strong>Площадь:</strong> {{ warehouse.area }} м²</p>
      <p><strong>Макс. загрузка:</strong> {{ warehouse.max_historical_load ?? '—' }} тонн</p>

      <div class="flex gap-4 mt-4">
        <button @click="openEditModal" class="bg-yellow-400 px-4 py-2 rounded text-white">✏️ Редактировать</button>
        <button @click="deleteWarehouse" class="bg-red-500 px-4 py-2 rounded text-white">🗑️ Удалить</button>
        <button @click="downloadReport" class="bg-green-600 px-4 py-2 rounded text-white">📄 Выгрузить отчёт</button>
      </div>

      <div v-if="grains.length" class="mt-8">
        <h2 class="text-xl font-semibold mb-2">Остатки по культурам:</h2>
        <ul class="list-disc list-inside text-gray-700">
          <li v-for="grain in grains" :key="grain.id">
            {{ grain.grain_type.name }} — {{ grain.amount }} т
          </li>
        </ul>
      </div>
      <button @click="showDeliveryModal = true" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
        ➕ Добавить поставку
      </button>

      <div v-if="deliveries.length" class="mt-10">
        <h2 class="text-xl font-semibold mb-2">История поставок</h2>
        <ul>
          <li v-for="d in deliveries" :key="d.id">
            {{ d.delivery_date }} — {{ d.grain_type }} — {{ d.volume }} т ({{ d.driver?.name ?? 'Без водителя' }})
          </li>
        </ul>
      </div>

      <div v-if="shipments.length" class="mt-10">
        <h2 class="text-xl font-semibold mb-2">История отгрузок</h2>
        <ul>
          <li v-for="s in shipments" :key="s.id">
            {{ s.shipment_date }} — {{ s.grain_type }} — {{ s.volume }} т ({{ s.driver?.name ?? 'Без водителя' }})
          </li>
        </ul>
      </div>

      <div v-if="deliveries.length" class="mt-10">
        <h2 class="text-xl font-semibold mb-2">Диаграмма объёма поставок</h2>
        <div class="h-[300px] w-full">
          <BarChart :chart-data="deliveryChartData" :chart-options="chartOptions" />
        </div>
      </div>
    </div>

    <div v-else>
      <p>Загрузка данных...</p>
    </div>

    <transition name="fade">
      <div v-if="showEditModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative animate-fade-in">
          <button class="absolute top-3 right-3 text-gray-500 hover:text-gray-700" @click="closeEditModal">✖</button>
          <h2 class="text-xl font-bold mb-4">Редактирование склада</h2>

          <div class="space-y-4">
            <input v-model="form.name" type="text" placeholder="Наименование склада"
              class="w-full border rounded px-4 py-2" />
            <select v-model="form.type" class="w-full border rounded px-4 py-2">
              <option disabled value="">Выберите тип склада</option>
              <option value="зерновой">Зерновой</option>
              <option value="склад запчастей">Склад запчастей</option>
              <option value="другое">Другое</option>
            </select>
            <input v-model="form.area" type="number" placeholder="Площадь (м²)"
              class="w-full border rounded px-4 py-2" />
            <input v-if="form.type === 'зерновой'" v-model="form.max_historical_load" type="number"
              placeholder="Макс. загрузка (тонн)" class="w-full border rounded px-4 py-2" />
            <p class="text-red-500 text-sm" v-if="error">{{ error }}</p>
            <div class="flex justify-end gap-2">
              <button @click="updateWarehouse" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">💾
                Сохранить</button>
              <button @click="closeEditModal" class="text-gray-600 underline">Отмена</button>
            </div>
          </div>
        </div>
      </div>
    </transition>
    {{ console.log('Проверка ID склада:', warehouse?.id) }}
    <Suspense>
      <CreateGrainDelivery v-if="showDeliveryModal" :key="warehouse?.id" :warehouse-id="warehouse?.id"
        :show="showDeliveryModal" @close="showDeliveryModal = false" @success="fetchDeliveries" />
    </Suspense>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }

  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>

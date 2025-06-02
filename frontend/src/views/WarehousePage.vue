<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import BarChart from '@/components/BarChart.vue'
import CreateGrainDelivery from '@/components/CreateGrainDelivery.vue'
import GrainDeliveryHistory from '@/components/GrainDeliveryHistory.vue'
import CreateGrainShipment from '@/components/CreateGrainShipment.vue'
import GrainShipmentHistory from '@/components/GrainShipmentHistory.vue'
import EditGrainWarehouseModal from '@/components/EditGrainWarehouseModal.vue'
import TareInputModal from '@/components/TareInputModal.vue'

const showTareModal = ref(false)
const showShipmentModal = ref(false)
const showDeliveryModal = ref(false)
const route = useRoute()
const router = useRouter()

const warehouse = ref(null)
const grains = ref([])
const deliveries = ref([])
const shipments = ref([])
const showEditModal = ref(false)
const error = ref('')
const toast = ref({ message: '', type: 'success', show: false })

const lastEditedDeliveryId = ref(null)
const lastEditedShipmentId = ref(null)

const form = ref({
  name: '',
  type: '',
  area: null,
  max_historical_load: null,
})

const showToast = (message, type = 'success') => {
  toast.value = { message, type, show: true }
  setTimeout(() => toast.value.show = false, 3000)
}

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
  try {
    const res = await api.get(`/warehouses/${route.params.id}/deliveries`)
    deliveries.value = res.data
  } catch (err) {
    console.error('Ошибка при загрузке поставок:', err)
  }
}

const fetchShipments = async () => {
  try {
    const res = await api.get(`/warehouses/${route.params.id}/shipments`)
    shipments.value = res.data
  } catch (err) {
    console.error('Ошибка при загрузке отгрузок:', err)
  }
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
    showToast('Склад успешно обновлён ✅')
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

const deliveryChartData = computed(() => {
  const cultureMap = new Map()

  // Собираем все культуры
  grains.value.forEach(g => {
    cultureMap.set(g.grain_type.id, g.grain_type.name)
  })

  const dateSet = new Set()
  const cultureIds = Array.from(cultureMap.keys())

  const dataMap = {}

  // Инициализируем структуру
  cultureIds.forEach(id => {
    dataMap[id] = { delivery: {}, shipment: {} }
  })

  // Заполняем поставки
  deliveries.value.forEach(d => {
    const date = d.delivery_date
    dateSet.add(date)

    if (dataMap[d.grain_type_id]) {
      if (!dataMap[d.grain_type_id].delivery[date]) {
        dataMap[d.grain_type_id].delivery[date] = 0
      }
      dataMap[d.grain_type_id].delivery[date] += d.volume
    }
  })

  // Заполняем отгрузки
  shipments.value.forEach(s => {
    const date = s.shipment_date
    dateSet.add(date)

    if (dataMap[s.grain_type_id]) {
      if (!dataMap[s.grain_type_id].shipment[date]) {
        dataMap[s.grain_type_id].shipment[date] = 0
      }
      dataMap[s.grain_type_id].shipment[date] += s.volume
    }
  })

  const allDates = Array.from(dateSet).sort()

  const datasets = []

  for (const [id, name] of cultureMap.entries()) {
    // Поставка
    datasets.push({
      label: `${name} — Поставка`,
      backgroundColor: getColor(name, 'delivery'),
      data: allDates.map(d => dataMap[id].delivery[d] || 0)
    })

    // Отгрузка
    datasets.push({
      label: `${name} — Отгрузка`,
      backgroundColor: getColor(name, 'shipment'),
      data: allDates.map(d => dataMap[id].shipment[d] || 0)
    })
  }

  return {
    labels: allDates,
    datasets
  }
})

function getColor(name, type) {
  const baseColors = {
    Пшеница: '#36A2EB',
    Кукуруза: '#F87171',
    Ячмень: '#10B981',
    Овёс: '#F59E0B'
  }

  const shade = type === 'shipment' ? '80' : 'CC' // Более прозрачный цвет для отгрузки
  const hex = baseColors[name] || '#A78BFA'
  return hex + shade
}
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  interaction: { mode: 'index', intersect: false },
  plugins: {
    legend: { position: 'bottom' },
    title: { display: true, text: 'Поставки и отгрузки по культурам и датам' }
  },
  scales: {
    x: { stacked: false, title: { display: true, text: 'Дата' } },
    y: { stacked: false, beginAtZero: true, title: { display: true, text: 'Объём (т)' } }
  }
}

const activeTab = ref('grains')
const tabs = [
  { key: 'grains', label: '🌾 Остатки' },
  { key: 'deliveries', label: '🚚 Поставки' },
  { key: 'shipments', label: '📦 Отгрузки' },
  { key: 'chart', label: '📊 Диаграмма' },
]
const checkBeforeDelivery = () => {
  const eligible = vehiclesWithTare.value

  if (eligible.length === 0) {
    showToast('❗ Нет транспорта с актуальным замером тары. Выполните повторный замер.', 'error')
    return
  }

  // Если хотя бы у одного транспорта `delivery_count` был равен 9, то после приёмки он удалён
  const needsTare = vehicles.value.some(v =>
    ['привоз', 'универсальный'].includes(v.type) &&
    (!v.latest_tare_measurement || v.latest_tare_measurement.delivery_count >= 10)
  )

  if (needsTare) {
    showToast('⚠️ Замер тары устарел. Сначала выполните повторный замер.', 'error')
    return
  }

  showDeliveryModal.value = true
}
const vehicles = ref([])

const fetchVehicles = async () => {
  try {
    const res = await api.get('/vehicles')
    vehicles.value = res.data
  } catch (e) {
    console.error('Ошибка загрузки транспорта', e)
  }
}

const vehiclesWithTare = computed(() =>
  vehicles.value.filter(v =>
    ['привоз', 'универсальный'].includes(v.type) && v.latest_tare_measurement
  )
)
const handleDeliverySuccess = (res) => {
  showDeliveryModal.value = false
  fetchDeliveries()
  fetchGrains()

  if (res?.showTareReminder) {
    toast.value = {
      message: '⚠️ Замер тары устарел. Необходимо выполнить повторный замер.',
      type: 'error',
      show: true
    }
  }
}
onMounted(() => {
  fetchWarehouse()
  fetchVehicles()
})

</script>

<template>
  <div class="p-6 max-w-6xl mx-auto space-y-10">
    <h1 class="text-2xl font-bold mb-4">Информация о складе</h1>

    <div v-if="warehouse" class="bg-white rounded-xl shadow-md p-6 space-y-6 border">
      <!-- Склад и кнопки -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700">
        <p><strong>Наименование:</strong> {{ warehouse.name }}</p>
        <p><strong>Тип:</strong> {{ warehouse.type }}</p>
        <p><strong>Площадь:</strong> {{ warehouse.area }} м²</p>
        <p><strong>Макс. загрузка:</strong> {{ warehouse.max_historical_load ?? '—' }} тонн</p>
      </div>

      <div class="flex flex-wrap gap-3 mt-2">
        <button @click="openEditModal"
          class="bg-yellow-400 hover:bg-yellow-500 px-4 py-2 rounded text-white font-medium">✏️ Редактировать</button>
        <button @click="deleteWarehouse"
          class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded text-white font-medium">🗑️ Удалить</button>
        <button @click="checkBeforeDelivery"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-medium shadow">
          ➕ Добавить поставку
        </button>
        <button @click="showShipmentModal = true"
          class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded font-medium shadow">➕ Добавить
          отгрузку</button>
        <button @click="downloadReport"
          class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded text-white font-medium">📄 Выгрузить отчёт</button>
        <button @click="showTareModal = true"
          class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded font-medium shadow">
          ⚖️ Замер тары
        </button>
      </div>

      <!-- Вкладки -->
      <div class="flex flex-wrap gap-2 border-b mt-6">
        <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key" :class="[
          'px-4 py-2 text-sm font-medium rounded-t-md transition',
          activeTab === tab.key ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-600'
        ]">{{ tab.label }}</button>
      </div>

      <div class="pt-4 space-y-6">
        <div v-if="activeTab === 'grains'">
          <div class="rounded-xl border shadow-sm overflow-hidden">
            <table class="w-full text-sm text-left border-separate border-spacing-y-1">
              <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wider">
                <tr>
                  <th class="px-4 py-3">Культура</th>
                  <th class="px-4 py-3 text-right">Остаток (т)</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="grain in [...grains].sort((a, b) => a.grain_type.name.localeCompare(b.grain_type.name))"
                  :key="grain.id" class="bg-white hover:bg-blue-50 transition-all">
                  <td class="px-4 py-2 font-medium text-gray-900">{{ grain.grain_type.name }}</td>
                  <td class="px-4 py-2 text-right text-gray-700">{{ grain.amount }}</td>
                </tr>
              </tbody>
              <tfoot v-if="grains.length">
                <tr class="bg-gray-100 border-t">
                  <td class="px-4 py-2 font-semibold text-right">Общий объём:</td>
                  <td class="px-4 py-2 text-right font-bold text-blue-600">
                    {{grains.reduce((sum, g) => sum + (parseFloat(g.amount) || 0), 0).toFixed(2)}} т
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
          <p v-if="!grains.length" class="text-gray-500 mt-4 text-center">Нет данных по остаткам.</p>
        </div>
        <div v-if="activeTab === 'deliveries'">
          <GrainDeliveryHistory :deliveries="deliveries" :grains="grains" :highlight-id="lastEditedDeliveryId"
            @refresh="fetchDeliveries" />
        </div>
        <div v-if="activeTab === 'shipments'">
          <GrainShipmentHistory :shipments="shipments" :grains="grains" :highlight-id="lastEditedShipmentId"
            @refresh="fetchShipments" />
        </div>
        <div v-if="activeTab === 'chart'">
          <BarChart :chart-data="deliveryChartData" :chart-options="chartOptions" class="h-[300px] w-full" />
        </div>
      </div>
    </div>
    <CreateGrainDelivery :warehouse-id="warehouse?.id" :show="showDeliveryModal" @close="showDeliveryModal = false"
      @success="handleDeliverySuccess" />

    <CreateGrainShipment :warehouse-id="warehouse?.id" :show="showShipmentModal" @close="showShipmentModal = false"
      @success="() => {
        showShipmentModal = false
        fetchShipments()
        fetchGrains()
      }" />
    <EditGrainWarehouseModal v-if="showEditModal" :model-value="showEditModal" :warehouse="warehouse"
      @close="showEditModal = false" @updated="fetchWarehouse" />
    <TareInputModal :show="showTareModal" @close="showTareModal = false" @saved="() => {
      showTareModal = false
      fetchVehicles()
    }" />
    <div v-if="toast.show" :class="['fixed bottom-5 right-5 px-4 py-2 rounded shadow text-white', {
      'bg-green-600': toast.type === 'success',
      'bg-red-500': toast.type === 'error'
    }]">
      {{ toast.message }}
    </div>
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

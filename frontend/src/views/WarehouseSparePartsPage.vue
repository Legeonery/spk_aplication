<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import BarChart from '@/components/BarChart.vue'

import SparePartDeliveryHistory from '@/components/SparePartDeliveryHistory.vue'
import SparePartUsageHistory from '@/components/SparePartUsageHistory.vue'

import CreateSparePartDelivery from '@/components/CreateSparePartDelivery.vue'
import CreateSparePartUsage from '@/components/CreateSparePartUsage.vue'

const route = useRoute()
const router = useRouter()

const warehouse = ref(null)
const spareParts = ref([])
const deliveries = ref([])
const usages = ref([])
const showDeliveryModal = ref(false)
const showUsageModal = ref(false)
const showEditModal = ref(false)
const error = ref('')
const form = ref({ name: '', type: '', area: null })

const activeTab = ref('parts')
const tabs = [
  { key: 'parts', label: '🧩 Остатки' },
  { key: 'deliveries', label: '📥 Поступления' },
  { key: 'usages', label: '🔧 Списания' },
  { key: 'chart', label: '📊 Диаграмма' },
]

onMounted(async () => {
  await fetchWarehouse()
})

const fetchWarehouse = async () => {
  const res = await api.get(`/warehouses/${route.params.id}`)
  warehouse.value = res.data.data
  Object.assign(form.value, warehouse.value)
  await fetchSpareParts()
  await fetchDeliveries()
  await fetchUsages()
}

const fetchSpareParts = async () => {
  const res = await api.get(`/warehouses/${route.params.id}/spare-parts`)
  spareParts.value = res.data
}

const fetchDeliveries = async () => {
  const res = await api.get(`/warehouses/${route.params.id}/spare-parts/deliveries`)
  deliveries.value = res.data
}

const fetchUsages = async () => {
  const res = await api.get(`/warehouses/${route.params.id}/spare-parts/usages`)
  usages.value = res.data
}

const deleteWarehouse = async () => {
  if (!confirm('Удалить склад?')) return
  await api.delete(`/warehouses/${route.params.id}`)
  router.push('/warehouses')
}

const chartData = computed(() => {
  const dateSet = new Set()
  const map = {}

  deliveries.value.forEach((d) => {
    dateSet.add(d.date)
    if (!map[d.name]) map[d.name] = { delivery: {}, usage: {} }
    map[d.name].delivery[d.date] = (map[d.name].delivery[d.date] || 0) + d.quantity
  })

  usages.value.forEach((u) => {
    dateSet.add(u.date)
    if (!map[u.name]) map[u.name] = { delivery: {}, usage: {} }
    map[u.name].usage[u.date] = (map[u.name].usage[u.date] || 0) + u.quantity
  })

  const dates = Array.from(dateSet).sort()
  const datasets = []

  for (const [name, values] of Object.entries(map)) {
    datasets.push({
      label: `${name} - Поступление`,
      backgroundColor: '#3B82F6CC',
      data: dates.map((d) => values.delivery[d] || 0),
    })
    datasets.push({
      label: `${name} - Списание`,
      backgroundColor: '#F59E0BCC',
      data: dates.map((d) => values.usage[d] || 0),
    })
  }

  return { labels: dates, datasets }
})

const handleCloseDeliveryModal = () => {
  showDeliveryModal.value = false
  fetchDeliveries()
  fetchSpareParts()
}

const handleCloseUsageModal = () => {
  showUsageModal.value = false
  fetchUsages()
  fetchSpareParts()
}
</script>

<template>
  <div class="p-6 max-w-6xl mx-auto space-y-10">
    <h1 class="text-2xl font-bold mb-4">Информация о складе</h1>

    <div v-if="warehouse" class="bg-white rounded-xl shadow-md p-6 space-y-6 border">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700">
        <p><strong>Наименование:</strong> {{ warehouse.name }}</p>
        <p><strong>Тип:</strong> {{ warehouse.type }}</p>
        <p><strong>Площадь:</strong> {{ warehouse.area }} м²</p>
      </div>

      <div class="flex flex-wrap gap-3 mt-2">
        <button
          @click="showEditModal = true"
          class="bg-yellow-400 hover:bg-yellow-500 px-4 py-2 rounded text-white font-medium"
        >
          ✏️ Редактировать
        </button>
        <button
          @click="deleteWarehouse"
          class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded text-white font-medium"
        >
          🗑️ Удалить
        </button>
        <button
          @click="showDeliveryModal = true"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-medium shadow"
        >
          ➕ Добавить поступление
        </button>
        <button
          @click="showUsageModal = true"
          class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded font-medium shadow"
        >
          ➖ Добавить списание
        </button>
      </div>

      <div class="flex flex-wrap gap-2 border-b mt-6">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          @click="activeTab = tab.key"
          :class="[
            'px-4 py-2 text-sm font-medium rounded-t-md transition',
            activeTab === tab.key
              ? 'bg-blue-600 text-white'
              : 'bg-gray-100 hover:bg-gray-200 text-gray-600',
          ]"
        >
          {{ tab.label }}
        </button>
      </div>

      <div class="pt-4 space-y-6">
        <div v-if="activeTab === 'parts'">
          <div class="rounded-xl border shadow-sm overflow-hidden">
            <table class="w-full text-sm text-left border-separate border-spacing-y-1">
              <thead class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wider">
                <tr>
                  <th class="px-4 py-3">Название</th>
                  <th class="px-4 py-3">Артикул</th>
                  <th class="px-4 py-3 text-right">Остаток</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="part in spareParts"
                  :key="part.id"
                  class="bg-white hover:bg-blue-50 transition-all"
                >
                  <td class="px-4 py-2 font-medium text-gray-900">{{ part.name }}</td>
                  <td class="px-4 py-2 text-gray-700">{{ part.article }}</td>
                  <td class="px-4 py-2 text-right text-gray-700">{{ part.quantity }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <p v-if="!spareParts.length" class="text-gray-500 mt-4 text-center">
            Нет данных по остаткам.
          </p>
        </div>
        <div v-if="activeTab === 'deliveries'">
          <SparePartDeliveryHistory :deliveries="deliveries" />
        </div>
        <div v-if="activeTab === 'usages'">
          <SparePartUsageHistory :usages="usages" />
        </div>
        <div v-if="activeTab === 'chart'">
          <BarChart :chart-data="chartData" class="h-[300px] w-full" />
        </div>
      </div>
    </div>
  </div>
  <CreateSparePartDelivery
    v-if="showDeliveryModal && warehouse"
    :show="showDeliveryModal"
    :warehouse-id="warehouse.id"
    @close="handleCloseDeliveryModal"
  />

  <CreateSparePartUsage
    v-if="showUsageModal && warehouse"
    :show="showUsageModal"
    :warehouse-id="warehouse.id"
    @close="handleCloseUsageModal"
  />
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

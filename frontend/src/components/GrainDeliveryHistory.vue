<script setup>
import { computed, ref, watch, defineProps } from 'vue'
import EditGrainDelivery from '@/components/EditGrainDelivery.vue'

const showEditDeliveryModal = ref(false)
const selectedDelivery = ref(null)

function editDelivery(delivery) {
  selectedDelivery.value = delivery
  showEditDeliveryModal.value = true
}

const props = defineProps({
  deliveries: Array,
  grains: Array,
  highlightId: [Number, null],
})

const filter = ref({
  date: '',
  grainTypeId: '',
})
const currentPage = ref(1)
const perPage = 10

const filteredDeliveries = computed(() => {
  let result = props.deliveries
  if (filter.value.date) {
    result = result.filter((d) => d.delivery_date === filter.value.date)
  }
  if (filter.value.grainTypeId) {
    result = result.filter((d) => d.grain_type?.id === parseInt(filter.value.grainTypeId))
  }
  return result
})

const paginatedDeliveries = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredDeliveries.value.slice(start, start + perPage)
})

const totalPages = computed(() => Math.ceil(filteredDeliveries.value.length / perPage))

const visiblePages = computed(() => {
  const pages = []
  const maxButtons = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxButtons / 2))
  let end = Math.min(totalPages.value, start + maxButtons - 1)
  if (end - start < maxButtons - 1) {
    start = Math.max(1, end - maxButtons + 1)
  }
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

watch(
  [filter],
  () => {
    currentPage.value = 1
  },
  { deep: true },
)
</script>

<template>
  <div class="bg-white rounded-xl shadow p-4 border">
    <div class="flex flex-wrap gap-4 mb-4">
      <input type="date" v-model="filter.date" class="border px-4 py-2 rounded w-full sm:w-auto" />
      <select v-model="filter.grainTypeId" class="border px-4 py-2 rounded w-full sm:w-auto">
        <option value="">Все культуры</option>
        <option v-for="g in grains" :key="g.grain_type.id" :value="g.grain_type.id">
          {{ g.grain_type.name }}
        </option>
      </select>
    </div>

    <transition name="fade" mode="out-in">
      <table v-if="paginatedDeliveries.length" key="table"
        class="w-full text-sm border border-gray-200 rounded overflow-hidden shadow-sm">
        <thead class="bg-blue-50 text-left">
          <tr>
            <th class="px-4 py-3 font-semibold">Дата</th>
            <th class="px-4 py-3 font-semibold">Культура</th>
            <th class="px-4 py-3 font-semibold">Объём (кг)</th>
            <th class="px-4 py-3 font-semibold">Водитель</th>
            <th class="px-4 py-3 font-semibold">Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="d in paginatedDeliveries" :key="d.id" class="hover:bg-blue-50 transition-colors border-t">
            <td class="px-4 py-2">{{ d.delivery_date }}</td>
            <td class="px-4 py-2">{{ d.grain_type?.name ?? '—' }}</td>
            <td class="px-4 py-2">{{ d.volume }}</td>
            <td class="px-4 py-2">
              <div v-if="d.driver">
                <div class="font-medium">{{ d.driver.name }}</div>
                <div class="text-xs text-gray-500">{{ d.driver.email }}</div>
              </div>
              <span v-else>—</span>
            </td>
            <td class="px-4 py-2">
              <button @click="editDelivery(d)" class="text-blue-600 hover:underline text-sm" title="Редактировать">
                ✏️
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else key="empty" class="text-center text-gray-500 py-6">
        Нет данных для отображения
      </div>
    </transition>

    <div v-if="totalPages > 1" class="flex justify-center items-center gap-2 mt-6 flex-wrap">
      <button v-for="page in visiblePages" :key="page" @click="currentPage = page" :class="[
        'px-3 py-1 rounded border text-sm',
        page === currentPage
          ? 'bg-blue-600 text-white border-blue-600'
          : 'bg-white text-gray-700 hover:bg-blue-100 border-gray-300',
      ]">
        {{ page }}
      </button>
    </div>
  </div>
  <EditGrainDelivery v-if="selectedDelivery" :key="selectedDelivery.id" :delivery="selectedDelivery"
    :show="showEditDeliveryModal" @close="showEditDeliveryModal = false" @success="
      () => {
        showEditDeliveryModal = false
        $emit('refresh')
      }
    " />
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(6px);
}
</style>

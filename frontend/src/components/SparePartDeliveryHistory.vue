<script setup>
import { computed, ref, watch, defineProps } from 'vue'

const props = defineProps({
  deliveries: {
    type: Array,
    default: () => [],
  },
})

const currentPage = ref(1)
const perPage = 10

const totalPages = computed(() =>
  Math.ceil(props.deliveries.length / perPage)
)

const paginatedDeliveries = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return props.deliveries.slice(start, start + perPage)
})

const visiblePages = computed(() => {
  const maxButtons = 5
  const total = totalPages.value
  const pages = []

  let start = Math.max(1, currentPage.value - Math.floor(maxButtons / 2))
  let end = Math.min(total, start + maxButtons - 1)

  if (end - start < maxButtons - 1) {
    start = Math.max(1, end - maxButtons + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

watch(() => props.deliveries, () => {
  currentPage.value = 1
})
</script>

<template>
  <div class="bg-white rounded-xl shadow p-4 border">
    <transition name="fade" mode="out-in">
      <table v-if="paginatedDeliveries.length" key="table"
        class="w-full text-sm border border-gray-200 rounded overflow-hidden shadow-sm">
        <thead class="bg-blue-50 text-left">
          <tr>
            <th class="px-4 py-3 font-semibold">Дата</th>
            <th class="px-4 py-3 font-semibold">Название</th>
            <th class="px-4 py-3 font-semibold">Артикул</th>
            <th class="px-4 py-3 font-semibold text-right">Количество</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="d in paginatedDeliveries"
            :key="d.id"
            class="hover:bg-blue-50 transition-colors border-t"
          >
            <td class="px-4 py-2">{{ d.date }}</td>
            <td class="px-4 py-2">{{ d.spare_part?.name || '—' }}</td>
            <td class="px-4 py-2">{{ d.spare_part?.article || '—' }}</td>
            <td class="px-4 py-2 text-right">{{ d.quantity }}</td>
          </tr>
        </tbody>
      </table>
      <div v-else key="empty" class="text-center text-gray-500 py-6">Нет данных для отображения</div>
    </transition>

    <div v-if="totalPages > 1" class="flex justify-center items-center gap-2 mt-6 flex-wrap">
      <button
        v-for="page in visiblePages"
        :key="page"
        @click="currentPage = page"
        :class="[
          'px-3 py-1 rounded border text-sm',
          page === currentPage
            ? 'bg-blue-600 text-white border-blue-600'
            : 'bg-white text-gray-700 hover:bg-blue-100 border-gray-300'
        ]"
      >
        {{ page }}
      </button>
    </div>
  </div>
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

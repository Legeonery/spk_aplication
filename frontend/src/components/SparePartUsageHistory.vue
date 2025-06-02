<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  usages: {
    type: Array,
    default: () => [],
  },
})

const currentPage = ref(1)
const perPage = 10

const totalPages = computed(() =>
  Math.ceil(props.usages.length / perPage)
)

const paginatedUsages = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return props.usages.slice(start, start + perPage)
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

watch(() => props.usages, () => {
  currentPage.value = 1
})
</script>

<template>
  <div class="bg-white rounded-xl shadow p-4 border">
    <transition name="fade" mode="out-in">
      <table v-if="paginatedUsages.length" key="table"
        class="w-full text-sm border border-gray-200 rounded overflow-hidden shadow-sm">
        <thead class="bg-blue-50 text-left">
          <tr>
            <th class="px-4 py-3 font-semibold">Дата</th>
            <th class="px-4 py-3 font-semibold">Название</th>
            <th class="px-4 py-3 font-semibold">Артикул</th>
            <th class="px-4 py-3 font-semibold text-right">Количество</th>
            <th class="px-4 py-3 font-semibold">Причина</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="u in paginatedUsages"
            :key="u.id"
            class="hover:bg-blue-50 transition-colors border-t"
          >
            <td class="px-4 py-2">{{ u.date }}</td>
            <td class="px-4 py-2">{{ u.name }}</td>
            <td class="px-4 py-2">{{ u.article || '—' }}</td>
            <td class="px-4 py-2 text-right">{{ u.quantity }}</td>
            <td class="px-4 py-2">{{ u.reason }}</td>
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

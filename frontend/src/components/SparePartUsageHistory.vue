<script setup>
defineProps({
  usages: {
    type: Array,
    default: () => [],
  },
})
</script>

<template>
  <div>
    <div v-if="!usages.length" class="text-gray-500 text-center py-4">Нет списаний.</div>

    <div v-else class="overflow-auto rounded-xl border shadow-sm">
      <table class="w-full text-sm text-left border-separate border-spacing-y-1">
        <thead class="bg-gray-50 text-gray-700 uppercase tracking-wider text-sm">
          <tr>
            <th class="px-4 py-3">Дата</th>
            <th class="px-4 py-3">Название</th>
            <th class="px-4 py-3">Артикул</th>
            <th class="px-4 py-3 text-right">Количество</th>
            <th class="px-4 py-3">Причина</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in usages" :key="u.id" class="bg-white hover:bg-blue-50 transition-all">
            <td class="px-4 py-2">{{ u.date }}</td>
            <td class="px-4 py-2">{{ u.name }}</td>
            <td class="px-4 py-2">{{ u.article || '—' }}</td>
            <td class="px-4 py-2 text-right">{{ u.quantity }}</td>
            <td class="px-4 py-2">{{ u.reason }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
